<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Result extends Model
{
    protected $fillable = ['user_id', 'exam_attempt_id', 'exam_raw', 'lab_raw', 'classroom_raw', 'total_raw', 'exam_final', 'lab_final', 'classroom_final', 'final_score', 'result_status', 'published_at'];
    protected function casts(): array
    {
        return ['exam_raw' => 'decimal:2', 'lab_raw' => 'decimal:2', 'classroom_raw' => 'decimal:2', 'total_raw' => 'decimal:2', 'exam_final' => 'decimal:2', 'lab_final' => 'decimal:2', 'classroom_final' => 'decimal:2', 'final_score' => 'decimal:2', 'published_at' => 'datetime'];
    }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function examAttempt(): BelongsTo { return $this->belongsTo(ExamAttempt::class); }
    public function certificate(): HasOne { return $this->hasOne(Certificate::class); }
}
