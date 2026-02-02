<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgreementUnit extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;

    protected $table = 'agreement_units';

    protected $fillable = [
        'agreement_id',
        'unit_type_id',
        'contract_unit_details_id',
        'contract_subunit_details_id',
        'rent_per_month',
        'rent_per_annum_agreement',
        'added_by',
        'updated_by',
        'deleted_by',
        'subunit_ids',
        'unit_revenue'
    ];
    protected $casts = [
        'subunit_ids' => 'array',
    ];

    /**
     * Relationships
     */

    public function agreement()
    {
        return $this->belongsTo(Agreement::class);
    }

    public function unitType()
    {
        return $this->belongsTo(UnitType::class);
    }

    public function contractUnitDetail()
    {
        return $this->belongsTo(ContractUnitDetail::class, 'contract_unit_details_id', 'id');
    }


    public function contractSubunitDetail()
    {
        return $this->belongsTo(ContractSubunitDetail::class, 'contract_subunit_details_id', 'id');
    }
    public function agreement_payment_details()
    {
        return $this->hasMany(AgreementPaymentDetail::class, 'agreement_unit_id', 'id');
    }
    public function agreementSubunitRentBifurcation()
    {
        return $this->hasMany(AgreementSubunitRentBifurcation::class, 'agreement_unit_id', 'id');
    }
}
