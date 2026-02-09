<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvestmentReferral extends Model
{
    use HasFactory,  SoftDeletes, HasActivityLog, HasDeletedBy;

    protected $table = 'investment_referrals';

    protected $fillable = [
        'investment_id',
        'investor_id',
        'investor_referror_id',
        'referral_commission_perc',
        'referral_commission_amount',
        // 'referral_commission_released_amount',
        // 'referral_commission_pending_amount',
        'referral_commission_frequency_id',
        'referral_commission_status',
        'last_referral_commission_released_date',
        'added_by',
        'updated_by',
        'deleted_by',
        'total_commission_pending',
        'total_commission_released',
        'current_month_commission_released',
        'commission_released_perc'
    ];

    public function investor()
    {
        return $this->belongsTo(Investor::class, 'investor_id');
    }

    public function referrer()
    {
        return $this->belongsTo(Investor::class, 'investor_referror_id');
    }

    public function commissionFrequency()
    {
        return $this->belongsTo(ReferralCommissionFrequency::class, 'referral_commission_frequency_id');
    }
    public function investment()
    {
        return $this->belongsTo(investment::class, 'investment_id');
    }


    //     public function getPendingAmountAttribute()
    //     {
    //         return $this->referral_commission_amount - $this->referral_commission_released_amount;
    //     }
    public function investorPayouts()
    {
        return $this->hasMany(InvestorPayout::class, 'payout_reference_id', 'id');
    }
}
