<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    protected $fillable = ['user_id', 'result_id', 'serial_number', 'qr_token', 'issued_at', 'file_path', 'status'];
    protected function casts(): array { return ['issued_at' => 'datetime']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function result(): BelongsTo { return $this->belongsTo(Result::class); }
}
