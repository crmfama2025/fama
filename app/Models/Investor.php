<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Investor extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;

    protected $fillable = [
        'investor_code',
        'investor_name',
        'investor_mobile',
        'investor_email',
        'investor_address',
        'nationality_id',
        'country_of_residence',
        'payment_mode_id',
        'id_number',
        'passport_number',
        'referral_id',
        'payout_batch_id',
        'profit_release_date',
        'status',
        'total_no_of_investments',
        'total_invested_amount',
        'total_profit_received',
        'total_referal_commission',
        'total_referral_commission_received',
        'total_terminated_investments',
        'is_passport_uploaded',
        'is_supp_doc_uploaded',
        'is_ref_com_cont_uploaded',
        'created_by',
        'updated_by',
        'deleted_by',
        'total_principal_received',
        'investor_relation_id',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country_id',
    ];

    public function nationality()
    {
        return $this->belongsTo(Nationality::class);
    }

    public function countryOfResidence()
    {
        return $this->belongsTo(Nationality::class, 'country_of_residence', 'id');
    }

    public function country()
    {
        return $this->belongsTo(Nationality::class, 'country_id', 'id');
    }

    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode_id', 'id');
    }

    public function referral()
    {
        return $this->belongsTo(Investor::class, 'referral_id', 'id');
    }

    public function investor_relation()
    {
        return $this->belongsTo(InvestorRelation::class, 'investor_relation_id', 'id');
    }

    public function payoutBatch()
    {
        return $this->belongsTo(PayoutBatch::class, 'payout_batch_id', 'id');
    }

    public function investorBanks()
    {
        return $this->hasMany(InvestorBank::class, 'investor_id');
    }

    public function investorDocuments()
    {
        return $this->hasMany(InvestorDocument::class, 'investor_id');
    }

    public function hasReferrer(): bool
    {
        return !is_null($this->referral_id) && $this->referral_id > 0;
    }
    // public function referrals()
    // {
    //     return $this->hasMany(Investor::class, 'referral_id');
    // }

    // public function setProfitReleaseDateAttribute($value)
    // {
    //     $this->attributes['profit_release_date'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    // }

    // public function getProfitReleaseDateAttribute($value)
    // {
    //     return $this->attributes['profit_release_date'] = Carbon::parse($value)->format('d-m-Y');
    // }

    public function primaryBank()
    {
        return $this->hasOne(InvestorBank::class, 'investor_id')
            ->where('is_primary', 1);
    }

    protected static function booted()
    {
        /**
         * When Investor is deleted
         */
        static::deleting(function ($investor) {

            $userId = auth()->id();

            // hasMany / hasOne relations that must be soft deleted
            $relations = [
                'investorBanks',
                'investorDocuments',
            ];

            if (!$investor->isForceDeleting()) {

                foreach ($relations as $relation) {
                    $investor->$relation->each(function ($item) use ($userId) {
                        if ($userId) {
                            $item->update(['deleted_by' => $userId]);
                        }
                        $item->delete(); // soft delete
                    });
                }
            } else {
                // Force delete
                foreach ($relations as $relation) {
                    $investor->$relation()->withTrashed()->forceDelete();
                }
            }
        });

        /**
         * When Investor is restored
         */
        static::restoring(function ($investor) {

            $relations = [
                'investorBanks',
                'investorDocuments',
            ];

            foreach ($relations as $relation) {
                $investor->$relation()->withTrashed()->restore();
            }
        });
    }
    public function investments()
    {
        return $this->hasmany(Investment::class, 'investor_id');
    }
}
