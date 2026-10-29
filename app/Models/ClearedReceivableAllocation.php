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
        'have_other_adjustments',
        'company_id'
    ];

    protected $casts = [
        'cleared_receivable_ids' => 'array',
    ];
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
    public function clearedReceivables()
    {
        return $this->hasMany(ClearedReceivable::class, 'id', 'id')
            ->whereRaw('1 = 0');
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
