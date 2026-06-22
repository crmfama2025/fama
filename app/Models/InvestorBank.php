<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvestorBank extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;

    protected $fillable = [
        'investor_id',
        'investor_beneficiary',
        'investor_bank_name',
        'investor_iban',
        'is_primary',
        'status',
        'added_by',
        'updated_by',
        'deleted_by',
        'banking_region',

        // new fields for contracts
        'investor_beneficiary_arabic',
        'investor_bank_name_arabic',
    ];

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }
}
