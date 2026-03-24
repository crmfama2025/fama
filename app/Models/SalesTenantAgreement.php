<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasCompanyAccess;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesTenantAgreement extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy, HasCompanyAccess;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sales_tenant_agreements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sales_agreement_code',
        'property_id',
        'area_id',
        'locality_id',
        'tenant_id',
        'business_type',
        'start_date',
        'end_date',
        'added_by',
        'updated_by',
        'deleted_by',
        'is_approved',
        'rejection_reason',
        'approved_by',
        'approved_date',
        'approved_comments',
        'is_agreement_added'
    ];
    public function tenant()
    {
        return $this->belongsTo(AgreementTenant::class, 'tenant_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function locality()
    {
        return $this->belongsTo(Locality::class, 'locality_id');
    }

    public function agreementUnits()
    {
        return $this->hasMany(SalesTenantUnit::class, 'sales_tenant_agreement_id');
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function salesTenantSubunitRents()
    {
        return $this->hasMany(SalesTenantSubunitRent::class, 'sales_tenant_unit_id');
    }
    protected static function booted()
    {
        // dd("test");
        static::deleting(function ($salesTenantAgreement) {
            // dd($salesTenantAgreement);
            $userId = auth()->user()->id;
            // dd($userId);
            // Conditionally add tenant
            $hasOneRelations = [];
            if ($salesTenantAgreement->business_type == 2) { // for b2c
                $hasOneRelations[] = 'tenant';
            }
            // dd($hasOneRelations);

            // hasMany relations
            $hasManyRelations = [
                'agreementUnits',
                'salesTenantSubunitRents',
            ];

            if (!$salesTenantAgreement->isForceDeleting()) {
                // dd("test");
                // dd($hasOneRelations);

                // Soft delete hasOne
                foreach ($hasOneRelations as $relation) {
                    $related = $salesTenantAgreement->$relation;
                    // dd($related);
                    if ($related) {
                        $related->update(['deleted_by' => $userId]);
                        $related->delete();
                    }
                }

                // Soft delete hasMany
                foreach ($hasManyRelations as $relation) {
                    foreach ($salesTenantAgreement->$relation as $related) {
                        $related->update(['deleted_by' => $userId]);
                        $related->delete();
                    }
                }
            } else {
                // Force delete
                foreach (array_merge($hasOneRelations, $hasManyRelations) as $relation) {
                    $salesTenantAgreement->$relation()->withTrashed()->forceDelete();
                }
            }
        });
    }
}
