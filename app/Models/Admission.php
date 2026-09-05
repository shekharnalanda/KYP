<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admission extends Model
{
    protected $fillable = [
        'application_number','branch_id','course_id','name',
        'date_of_birth','gender','mobile','email',
        'father_name','mother_name','guardian_name','guardian_mobile',
        'qualification','address','city','district','state','pin',
        'identity_type','identity_number','photo_path','remarks',
        'consent','status','admin_note','approved_by','approved_at','user_id'
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'consent' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
