<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IrisAttendanceEvent extends Model
{
    protected $fillable = [
        'event_uuid',
        'user_id',
        'course_id',
        'learning_session_id',
        'event_type',
        'device_reference',
        'matched_eye',
        'match_score',
        'captured_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'captured_at' => 'datetime',
            'match_score' => 'decimal:3',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function learningSession(): BelongsTo { return $this->belongsTo(LearningSession::class); }
}
