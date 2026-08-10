<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
// use App\Models\Traits\HasCompanyAccess;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartialWithdrawalBifurcation extends Model
{
    // use HasFactory;
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;

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
        'duration_days',
        'withdrawal_month_profit',
        'total_paid',
        'balance_to_pay',
        'payout_status',
        'profit_payout_status',
        'withdrawal_month_profit',
        'updated_by',
        'deleted_by'
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
