<?php

namespace App\Console\Commands;

use App\Models\AttendanceRecord;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoCheckoutIrisAttendanceCommand extends Command
{
    protected $signature = 'kyp:iris-auto-checkout';

    protected $description = 'Auto close Centre-Iris lab attendance after 120 minutes when Mark-Out is missing.';

    public function handle(): int
    {
        $cutoff = now()->subMinutes(120);
        $closed = 0;

        AttendanceRecord::query()
            ->where('mode', 'lab')
            ->where('source', 'centre_iris')
            ->where('status', 'present')
            ->whereNotNull('checked_in_at')
            ->whereNull('checked_out_at')
            ->where('checked_in_at', '<=', $cutoff)
            ->orderBy('id')
            ->chunkById(100, function ($records) use (&$closed): void {
                foreach ($records as $attendance) {
                    DB::transaction(function () use ($attendance, &$closed): void {
                        $record = AttendanceRecord::query()
                            ->lockForUpdate()
                            ->find($attendance->id);

                        if (! $record
                            || $record->status !== 'present'
                            || $record->checked_out_at !== null
                            || ! $record->checked_in_at
                            || $record->checked_in_at->copy()->addMinutes(120)->isFuture()
                        ) {
                            return;
                        }

                        $autoOut = $record->checked_in_at->copy()->addMinutes(120);

                        $record->update([
                            'checked_out_at' => $autoOut,
                            'checkout_source' => 'system_auto',
                            'minutes_completed' => 120,
                            'status' => 'completed',
                        ]);

                        $closed++;
                    });
                }
            });

        $this->info("Auto Mark-Out completed: {$closed}");

        return self::SUCCESS;
    }
}
