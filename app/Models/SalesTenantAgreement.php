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
        'approved_comments'
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
}
