<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesTenantUnit extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sales_tenant_units';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sales_tenant_agreement_id',
        'floor_number',
        'unit_type_id',
        'contract_id',
        'contract_unit_details_id',
        'contract_subunit_details_id',
        'subunit_ids',
        'annual_rent',
        'monthly_rent',
        'added_by',
        'updated_by',
        'deleted_by',
    ];
    public function salesAgreement()
    {
        return $this->belongsTo(SalesTenantAgreement::class, 'sales_tenant_agreement_id');
    }

    public function contractUnitDetail()
    {
        return $this->belongsTo(ContractUnitDetail::class, 'contract_unit_details_id');
    }

    public function contractSubunitDetail()
    {
        return $this->belongsTo(ContractSubunitDetail::class, 'contract_subunit_details_id');
    }
    public function unitType()
    {
        return $this->belongsTo(UnitType::class);
    }
    public function salesTenantSubunitRents()
    {
        return $this->hasMany(SalesTenantSubunitRent::class, 'sales_tenant_unit_id');
    }
    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }
}
