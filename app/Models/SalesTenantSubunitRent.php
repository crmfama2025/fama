<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesTenantSubunitRent extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sales_tenant_subunit_rents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sales_tenant_agreement_id',
        'sales_tenant_unit_id',
        'contract_subunit_details_id',
        'rent_per_month',
        'added_by',
        'updated_by',
        'deleted_by',
    ];


    public function contractSubUnitDetail()
    {
        return $this->belongsTo(ContractSubunitDetail::class, 'contract_subunit_details_id');
    }
}
