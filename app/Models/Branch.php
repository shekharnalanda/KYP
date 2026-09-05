<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = [
        'name','code','address','city','district','state',
        'pin','phone','email','is_active','position'
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class);
    }
}
