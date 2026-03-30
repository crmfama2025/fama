<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FcmToken extends Model
{
    use HasFactory, SoftDeletes;

    // Table name (optional, Laravel assumes 'fcm_tokens')
    protected $table = 'fcm_tokens';

    // Fillable fields (for mass assignment)
    protected $fillable = [
        'user_id',
        'token',
        'device_name',
        'device_id',
        'user_agent',
        'last_active_at'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
