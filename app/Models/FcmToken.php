<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    use HasFactory;

    // Table name (optional, Laravel assumes 'fcm_tokens')
    protected $table = 'fcm_tokens';

    // Fillable fields (for mass assignment)
    protected $fillable = [
        'user_id',
        'token',
        'device_name',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
