<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityRecord extends Model
{
    protected $fillable = ['user_id', 'learning_session_id', 'status', 'score', 'started_at', 'completed_at', 'metadata'];

    protected function casts(): array
    {
        return ['score' => 'decimal:2', 'started_at' => 'datetime', 'completed_at' => 'datetime', 'metadata' => 'array'];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function learningSession(): BelongsTo { return $this->belongsTo(LearningSession::class); }
}
