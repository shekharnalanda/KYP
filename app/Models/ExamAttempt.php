<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ExamAttempt extends Model
{
    protected $fillable = ['exam_id', 'user_id', 'attempt_number', 'status', 'started_at', 'submitted_at', 'raw_exam_score', 'max_raw_score'];
    protected function casts(): array { return ['started_at' => 'datetime', 'submitted_at' => 'datetime', 'raw_exam_score' => 'decimal:2', 'max_raw_score' => 'decimal:2']; }
    public function exam(): BelongsTo { return $this->belongsTo(Exam::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function answers(): HasMany { return $this->hasMany(AttemptAnswer::class); }
    public function result(): HasOne { return $this->hasOne(Result::class); }
}
