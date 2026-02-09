<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvestorPayout extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;
    protected $table = 'investor_payouts';

    protected $fillable = [
        'investment_id',
        'investor_id',
        'payout_type',
        'payout_reference_id',
        'payout_release_month',
        'payout_amount',
        'amount_paid',
        'amount_pending',
        'is_processed',
        'updated_by',
        'deleted_by',
    ];

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }
    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }
    public function investmentReferral()
    {
        return $this->belongsTo(InvestmentReferral::class, 'investment_referral_id');
    }
    public function investorReference()
    {
        return $this->belongsTo(Investor::class, 'investor_referrence_id');
    }
    public function investorPayoutDistribution()
    {
        return $this->hasmany(InvestorPaymentDistribution::class, 'payout_id', 'id');
    }
}
