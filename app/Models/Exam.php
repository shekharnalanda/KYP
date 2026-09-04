<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    protected $fillable = ['course_id', 'title_hi', 'title_en', 'duration_minutes', 'total_questions', 'max_marks', 'status', 'starts_at', 'ends_at', 'published_at'];

    protected function casts(): array
    {
        return ['max_marks' => 'decimal:2', 'starts_at' => 'datetime', 'ends_at' => 'datetime', 'published_at' => 'datetime'];
    }

    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function questions(): BelongsToMany { return $this->belongsToMany(Question::class)->withPivot('position')->orderByPivot('position'); }
    public function attempts(): HasMany { return $this->hasMany(ExamAttempt::class); }
}
