<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\LearningSession;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class KypDailyVideoSyncController extends Controller
{
    public function preview(Request $request): JsonResponse
    {
        $this->authorizeMci($request);
        $videos = $this->validatedVideos($request);
        [$courses, $sessions, $structureHash] = $this->sessionStructure();

        return response()->json([
            'ok' => true,
            'days' => count($videos),
            'sessions' => $sessions->count(),
            'already_linked' => $sessions->whereNotNull('theory_youtube_video_id')->count(),
            'will_change' => $this->willChangeCount($sessions, $videos),
            'structure_hash' => $structureHash,
            'courses' => $this->courseRanges($courses),
            'day_mappings' => $this->dayMappings($courses),
            'mapping_note' => 'Sessions are ordered by course position, then session number. Days 1–66 map to two consecutive sessions; Day 67 maps to sessions 133–135.',
        ]);
    }

    public function sync(Request $request): JsonResponse
    {
        $this->authorizeMci($request);
        $videos = $this->validatedVideos($request);
        $validated = $request->validate([
            'expected_structure_hash' => ['required', 'string', 'size:64', 'regex:/^[a-f0-9]{64}$/'],
        ]);
        [$courses, $sessions, $structureHash] = $this->sessionStructure();

        if (! hash_equals($structureHash, $validated['expected_structure_hash'])) {
            if ($this->alreadyApplied($sessions, $videos)) {
                return response()->json([
                    'ok' => true,
                    'already_applied' => true,
                    'days_applied' => count($videos),
                    'updated_sessions' => $sessions->count(),
                    'acknowledged_structure_hash' => $validated['expected_structure_hash'],
                    'structure_hash' => $structureHash,
                    'courses' => $this->courseRanges($courses),
                    'message' => 'This manifest was already synced to KYP.',
                ]);
            }

            return response()->json([
                'ok' => false,
                'message' => 'KYP course sessions changed after preview. Preview the mapping again before syncing.',
            ], 409);
        }

        $videoByDay = collect($videos)->keyBy('day');
        DB::transaction(function () use ($sessions, $videoByDay): void {
            foreach ($sessions as $index => $session) {
                $ordinal = $index + 1;
                $day = $ordinal <= 132 ? intdiv($ordinal - 1, 2) + 1 : 67;
                $session->theory_video_day = $day;
                $session->theory_youtube_video_id = $videoByDay->get($day)['youtube_video_id'];
                $session->save();
            }
        });

        $updatedStructureHash = $this->structureHash($courses);

        return response()->json([
            'ok' => true,
            'days_applied' => count($videos),
            'updated_sessions' => $sessions->count(),
            'acknowledged_structure_hash' => $validated['expected_structure_hash'],
            'structure_hash' => $updatedStructureHash,
            'already_applied' => false,
            'courses' => $this->courseRanges($courses),
            'day_mappings' => $this->dayMappings($courses),
            'message' => 'Daily theory videos synced to all KYP learning sessions.',
        ]);
    }

    private function authorizeMci(Request $request): void
    {
        $expected = (string) config('services.mci_daily_video_sync.token');
        $provided = (string) $request->bearerToken();

        abort_unless(
            $expected !== '' && $provided !== '' && hash_equals($expected, $provided),
            401,
            'Invalid MCI video sync token.'
        );
    }

    private function validatedVideos(Request $request): array
    {
        $validated = Validator::make($request->all(), [
            'videos' => ['required', 'array', 'size:67'],
            'videos.*' => ['required', 'array:day,youtube_video_id'],
            'videos.*.day' => ['required', 'integer', 'min:1', 'max:67', 'distinct'],
            'videos.*.youtube_video_id' => ['required', 'string', 'regex:/^[A-Za-z0-9_-]{11}$/'],
        ])->validate();

        $videos = collect($validated['videos'])->sortBy('day')->values()->all();
        $days = array_map(static fn (array $video): int => (int) $video['day'], $videos);

        if ($days !== range(1, 67)) {
            throw ValidationException::withMessages([
                'videos' => 'The manifest must contain exactly one video for each day, 1 through 67.',
            ]);
        }

        return $videos;
    }

    /** @return array{0: EloquentCollection, 1: Collection, 2: string} */
    private function sessionStructure(): array
    {
        $courses = Course::query()
            ->where('is_active', true)
            ->with(['sessions' => fn ($query) => $query->orderBy('session_number')])
            ->orderBy('position')
            ->orderBy('id')
            ->get();
        $sessions = $courses->flatMap(fn (Course $course) => $course->sessions)->values();

        if ($sessions->count() !== 135) {
            abort(response()->json([
                'ok' => false,
                'message' => 'Expected 135 active KYP sessions across the course catalog; found '.$sessions->count().'. No videos were changed.',
            ], 409));
        }

        return [$courses, $sessions, $this->structureHash($courses)];
    }

    private function structureHash(EloquentCollection $courses): string
    {
        $structure = $courses->flatMap(fn (Course $course) => $course->sessions->map(fn (LearningSession $session) => [
            'course_code' => $course->code,
            'session_number' => (int) $session->session_number,
            'id' => (int) $session->id,
            'theory_video_day' => $session->theory_video_day === null ? null : (int) $session->theory_video_day,
            'theory_youtube_video_id' => $session->theory_youtube_video_id,
        ]))->values()->all();

        return hash('sha256', json_encode($structure, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
    }

    private function courseRanges(EloquentCollection $courses): array
    {
        $position = 0;

        return $courses->map(function (Course $course) use (&$position): array {
            $count = $course->sessions->count();
            $range = [
                'code' => $course->code,
                'name' => $course->name,
                'sessions' => $count,
                'first_ordinal' => $count ? $position + 1 : null,
                'last_ordinal' => $count ? $position + $count : null,
            ];
            $position += $count;

            return $range;
        })->all();
    }

    private function dayMappings(EloquentCollection $courses): array
    {
        $mappings = [];
        $ordinal = 0;

        foreach ($courses as $course) {
            foreach ($course->sessions as $session) {
                $ordinal++;
                $day = $ordinal <= 132 ? intdiv($ordinal - 1, 2) + 1 : 67;
                $mappings[$day] ??= ['day' => $day, 'sessions' => []];
                $mappings[$day]['sessions'][] = [
                    'course_code' => $course->code,
                    'session_number' => (int) $session->session_number,
                ];
            }
        }

        return array_values($mappings);
    }

    private function willChangeCount(Collection $sessions, array $videos): int
    {
        $videoByDay = collect($videos)->keyBy('day');

        return $sessions->filter(function (LearningSession $session, int $index) use ($videoByDay): bool {
            $ordinal = $index + 1;
            $day = $ordinal <= 132 ? intdiv($ordinal - 1, 2) + 1 : 67;

            return $session->theory_youtube_video_id !== $videoByDay->get($day)['youtube_video_id'];
        })->count();
    }

    private function alreadyApplied(Collection $sessions, array $videos): bool
    {
        $videoByDay = collect($videos)->keyBy('day');

        return $sessions->every(function (LearningSession $session, int $index) use ($videoByDay): bool {
            $ordinal = $index + 1;
            $day = $ordinal <= 132 ? intdiv($ordinal - 1, 2) + 1 : 67;

            return (int) $session->theory_video_day === $day
                && $session->theory_youtube_video_id === $videoByDay->get($day)['youtube_video_id'];
        });
    }
}
