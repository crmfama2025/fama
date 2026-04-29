<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClearedReceivableAllocation extends Model
{
    use HasFactory;
    protected $fillable = [
        'allocated_amount',
        'cleared_receivable_ids',
        'cleared_date',
        'added_by',
        'updated_by',
    ];

    protected $casts = [
        'cleared_receivable_ids' => 'array',
    ];
}
