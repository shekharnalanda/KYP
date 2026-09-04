<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningSessionProgress extends Model
{
    protected $table = 'learning_session_progress';

    protected $fillable = [
        'user_id',
        'learning_session_id',
        'current_step',
        'completed_steps',
        'active_seconds',
        'quiz_answers',
        'quiz_score',
        'practical_response',
        'practical_submitted_at',
        'last_activity_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_steps' => 'array',
            'quiz_answers' => 'array',
            'quiz_score' => 'decimal:2',
            'practical_submitted_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function learningSession(): BelongsTo
    {
        return $this->belongsTo(LearningSession::class);
    }
}
