<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestorLedger extends Model
{
    use HasFactory;
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
        'withdrawal_month_profit'
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
}
