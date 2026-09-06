<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    protected $fillable = ['user_id', 'course_id', 'learning_session_id', 'recorded_by', 'attendance_date',
        'checked_in_at',
        'checked_out_at',
        'checkout_source', 'mode',
        'source', 'status', 'biometric_reference', 'minutes_completed'];

    protected function casts(): array
    {
        return [
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime','attendance_date' => 'date'];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function learningSession(): BelongsTo { return $this->belongsTo(LearningSession::class); }
    public function recorder(): BelongsTo { return $this->belongsTo(User::class, 'recorded_by'); }
}
