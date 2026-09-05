<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IrisProfile extends Model
{
    protected $fillable = [
        'user_id',
        'left_template',
        'right_template',
        'device_model',
        'device_reference',
        'quality_score',
        'enrolled_at',
        'enrolled_by',
        'is_active',
    ];

    protected $hidden = ['left_template', 'right_template'];

    protected function casts(): array
    {
        return [
            'left_template' => 'encrypted',
            'right_template' => 'encrypted',
            'enrolled_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enrolled_by');
    }
}
