<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class agreementSubunitRentBifurcation extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;

    protected $table = 'agreement_subunit_rent_bifurcation';

    protected $fillable = [
        'agreement_id',
        'agreement_unit_id',
        'contract_subunit_details_id',
        'contract_unit_details_id',
        'rent_per_month',
        'added_by',
        'updated_by',
        'deleted_by',
    ];
    public function agreement()
    {
        return $this->belongsTo(Agreement::class);
    }
    public function agreementUnit()
    {
        return $this->belongsTo(AgreementUnit::class, 'agreement_unit_id');
    }
    public function contractUnitDetail()
    {
        return $this->belongsTo(ContractUnitDetail::class, 'contract_unit_details_id');
    }
    public function contractSubunitDetail()
    {
        return $this->belongsTo(ContractSubUnitDetail::class, 'contract_subunit_details_id');
    }
}
