<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasCompanyAccess;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvestorLedger extends Model
{
    // use HasFactory;
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;
    protected $fillable = [
        'investment_id',
        'investor_id',
        'company_id',
        'investor_transaction_type_id',
        'transaction_amount',
        'is_credit',
        'transaction_date',
        'status',
        'added_by',
        'updated_by',
        'deleted_by',
        'investment_contract_document_id',
        'requested_date',
        'withdrawal_date',
        'duration_days',
        'withdrawal_status',
        'profit_payout_status',
        'withdrawal_month_profit',
        'approved_date',
        'approved_by',
        'approval_remarks'
    ];

    public function transactionType()
    {
        return $this->belongsTo(InvestorTransactionTypes::class, 'investor_transaction_type_id');
    }
    public function investor()
    {
        return $this->belongsTo(Investor::class, 'investor_id');
    }
    public function investment()
    {
        return $this->belongsTo(Investment::class, 'investment_id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
