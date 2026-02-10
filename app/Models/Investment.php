<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Investment extends Model
{
    use HasFactory,  SoftDeletes, HasActivityLog, HasDeletedBy;

    protected $table = 'investments';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'investment_code',
        'investor_id',
        'payout_batch_id',
        'company_id',
        'profit_interval_id',

        'investment_amount',
        'investment_type',
        'received_amount',
        'total_received_amount',
        'balance_amount',

        'has_fully_received',

        'investment_date',
        'investment_tenure',
        'grace_period',
        'maturity_date',

        'profit_perc',
        'referral_profit_perc',
        'profit_amount',
        'profit_amount_per_interval',

        'profit_release_date',
        'last_profit_released_date',
        'next_profit_release_date',
        'next_referral_commission_release_date',

        'nominee_name',
        'nominee_email',
        'nominee_phone',

        'company_bank_id',
        'investor_bank_id',

        'investment_status',
        'terminate_status',

        'reinvestment_or_not',
        'parent_investment_id',
        'has_reinvestment',
        'reinvested_count',

        'added_by',
        'updated_by',
        'deleted_by',

        'initial_profit_release_month',
        'total_profit_released',
        'current_month_released',
        'outstanding_profit',
        'is_profit_processed',

        'termination_requested_date',
        'termination_date',
        'termination_duration',
        'termination_document',
        'termination_requested_by',
        'terminated_by',
        'termination_outstanding',

        'invested_company_id'
    ];

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    public function payoutBatch()
    {
        return $this->belongsTo(PayoutBatch::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
    public function investedCompany()
    {
        return $this->belongsTo(Company::class, 'invested_company_id', 'id');
    }

    public function profitInterval()
    {
        return $this->belongsTo(ProfitInterval::class);
    }

    public function referralProfitFrequency()
    {
        return $this->belongsTo(ReferralCommissionFrequency::class);
    }

    public function companyBank()
    {
        return $this->belongsTo(Bank::class, 'company_bank_id');
    }

    public function investorBank()
    {
        return $this->belongsTo(Bank::class, 'investor_bank_id');
    }

    public function parentInvestment()
    {
        return $this->belongsTo(self::class, 'parent_investment_id');
    }

    public function childInvestments()
    {
        return $this->hasMany(self::class, 'parent_investment_id');
    }



    public function getFormattedInvestmentAmountAttribute()
    {
        return number_format($this->investment_amount, 2);
    }

    public function getIsActiveAttribute()
    {
        return $this->investment_status === 1;
    }
    public function setInvestmentDate($date)
    {
        $this->attributes['investment_date'] = Carbon::parse($date)->format('Y-m-d H:i:s');
    }


    public function setMaturityDate($date)
    {
        $this->attributes['maturity_date'] = Carbon::parse($date)->format('Y-m-d H:i:s');
    }

    // public function setProfitReleaseDate($date)
    // {
    //     $this->attributes['profit_release_date'] = Carbon::parse($date)->format('Y-m-d H:i:s');
    // }
    public function investmentReceivedPayments()
    {
        return $this->hasMany(InvestmentReceivedPayment::class, 'investment_id');
    }
    public function investmentReferral()
    {
        return $this->hasOne(InvestmentReferral::class, 'investment_id');
    }
    // public function getProfitReleaseDateAttribute($value)
    // {
    //     return $this->attributes['profit_release_date'] = Carbon::parse($value)->format('d-m-Y');
    // }
    public function investmentDocument()
    {
        return $this->hasOne(InvestmentDocument::class, 'investment_id');
    }
    public function getType()
    {
        return match ($this->investment_type) {
            0 => 'New',
            1 => 'Renew'
        };
    }
    protected static function booted()
    {
        // dd("test");
        static::deleting(function ($agreement) {

            $userId = auth()->user()->id;
            // dd($userId);

            // hasOne relations
            $hasOneRelations = [
                'investmentDocument',
                'investmentReferral',
            ];

            // hasMany relations
            $hasManyRelations = [
                'investmentReceivedPayments',
            ];

            if (!$agreement->isForceDeleting()) {
                // dd("test");

                // Soft delete hasOne
                foreach ($hasOneRelations as $relation) {
                    $related = $agreement->$relation;
                    if ($related) {
                        $related->update(['deleted_by' => $userId]);
                        $related->delete();
                    }
                }

                // Soft delete hasMany
                foreach ($hasManyRelations as $relation) {
                    foreach ($agreement->$relation as $related) {
                        $related->update(['deleted_by' => $userId]);
                        $related->delete();
                    }
                }
            } else {
                // Force delete
                foreach (array_merge($hasOneRelations, $hasManyRelations) as $relation) {
                    $agreement->$relation()->withTrashed()->forceDelete();
                }
            }
        });

        static::restoring(function ($agreement) {
            $relations = [
                'investmentDocument',
                'investmentReferral',
                'investmentReceivedPayments',
            ];

            foreach ($relations as $relation) {
                $agreement->$relation()->withTrashed()->restore();
            }
        });
    }


    public function getNextProfitReleaseDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d-m-Y') : null;
    }

    public function getNextReferralCommissionReleaseDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d-m-Y') : null;
    }
    public function getTerminationDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d-m-Y') : null;
    }

    public function setLastProfitReleasedDate($date)
    {
        $this->attributes['last_profit_released_date'] = Carbon::parse($date)->format('Y-m-d');
    }
}
