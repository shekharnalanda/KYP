<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = ['course_id', 'learning_session_id', 'text_hi', 'text_en', 'type', 'marks', 'negative_marks', 'explanation_hi', 'explanation_en', 'difficulty', 'status'];

    protected function casts(): array
    {
        return ['marks' => 'decimal:2', 'negative_marks' => 'decimal:2'];
    }

    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function learningSession(): BelongsTo { return $this->belongsTo(LearningSession::class); }
    public function options(): HasMany { return $this->hasMany(QuestionOption::class); }
    public function exams(): BelongsToMany { return $this->belongsToMany(Exam::class)->withPivot('position'); }
}
