<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasCompanyAccess;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Agreement extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy, HasCompanyAccess;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'agreements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'agreement_code',
        'contract_id',
        'company_id',
        'start_date',
        'end_date',
        'duration_in_months',
        'duration_in_days',
        'is_emirates_id_uploaded',
        'is_passport_uploaded',
        'is_visa_uploaded',
        'is_signed_agreement_uploaded',
        'is_trade_license_uploaded',
        'agreement_status',
        'terminated_date',
        'terminated_reason',
        'terminated_by',
        'added_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */


    /**
     * Define relationships (optional, based on foreign keys).
     */
    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id')->withoutGlobalScopes();
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function tenant()
    {
        return $this->hasOne(AgreementTenant::class, 'agreement_id');
    }
    public function agreement_documents()
    {
        return $this->hasMany(AgreementDocument::class, 'agreement_id');
    }
    public function agreement_payment()
    {
        return $this->hasOne(AgreementPayment::class, 'agreement_id');
    }
    public function agreement_payment_details()
    {
        return $this->hasMany(AgreementPaymentDetail::class, 'agreement_id');
    }
    // public function agreementUnit()
    // {
    //     return $this->hasOne(AgreementUnit::class, 'agreement_id');
    // }
    public function agreement_units()
    {
        return $this->hasMany(AgreementUnit::class, 'agreement_id', 'id');
    }

    protected static function booted()
    {
        // dd("test");
        static::deleting(function ($agreement) {

            $userId = auth()->user()->id;
            // dd($userId);

            // hasOne relations
            $hasOneRelations = [
                'tenant',
                'agreement_payment',

            ];

            // hasMany relations
            $hasManyRelations = [
                'agreement_payment_details',
                'agreement_documents',
                'agreement_units',
                'tenant_invoices',
                'agreementStatusLogs',
                'agreementSubunitRentBifurcations'


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
                'tenant',
                'agreement_documents',
                'agreement_payment',
                'agreement_units',
                'agreement_payment_details',
                'tenant_invoices',
                'agreementStatusLogs',
                'agreementSubunitRentBifurcations'
            ];

            foreach ($relations as $relation) {
                $agreement->$relation()->withTrashed()->restore();
            }
        });
    }

    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }
    public function setTerminatedDateAttribute($value)
    {
        $this->attributes['terminated_date'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }
    public function getVacantUnitTypes()
    {
        $vacantUnitTypes = $this->contract->contract_unit_details
            ->where('is_vacant', 0)
            ->pluck('unit_type_id')
            ->unique();
        $agreementUnitTypes = $this->agreement_units
            ->pluck('unit_type_id')
            ->unique();

        $unitTypeIds = $vacantUnitTypes->merge($agreementUnitTypes)->unique();

        return UnitType::whereIn('id', $unitTypeIds)->get();
    }
    public function getVacantunits()
    {
        $vacantUnitIds = $this->contract->contract_unit_details
            ->where('is_vacant', 0)
            ->pluck('id')
            ->unique();

        $vacantUnits = ContractUnitDetail::whereIn('id', $vacantUnitIds)->get();

        $vacantSubunits = ContractSubUnitDetail::whereIn('contract_unit_detail_id', $vacantUnitIds)
            ->where('is_vacant', 0)
            ->get();

        $subunitsByUnitId = $vacantSubunits->groupBy('contract_unit_detail_id');

        return [
            'units' => $vacantUnits,
            'subunits_by_unit' => $subunitsByUnitId,
        ];
    }
    public function tenant_invoices()
    {
        return $this->hasMany(AgreementUnit::class, 'agreement_id', 'id');
    }
    public function agreementStatusLogs()
    {
        return $this->hasMany(AgreementStatusLogs::class, 'agreement_id', 'id');
    }
    public function agreementSubunitRentBifurcations()
    {
        return $this->hasMany(AgreementSubunitRentBifurcation::class, 'agreement_id', 'id');
    }
}
