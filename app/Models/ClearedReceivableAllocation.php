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
        'bank_reference',
        'receipt_allocation_id',
        'have_other_adjustments'
    ];

    protected $casts = [
        'cleared_receivable_ids' => 'array',
    ];
}
