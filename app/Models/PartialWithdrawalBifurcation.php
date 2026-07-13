<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartialWithdrawalBifurcation extends Model
{
    use HasFactory;

    protected $table = 'partial_withdrawal_bifurcations';

    protected $fillable = [
        'investment_id',
        'ledger_id',
        'company_id',
        'withdrawal_amount',
        'previous_amount',
        'balance_amount',
        'added_by',
        'requested_date',
        'withdrawal_date',
        'duration_days'
    ];

    public function investment()
    {
        return $this->belongsTo(Investment::class, 'investment_id');
    }

    public function ledger()
    {
        return $this->belongsTo(InvestorLedger::class, 'ledger_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
