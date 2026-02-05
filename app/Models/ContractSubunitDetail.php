<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractSubunitDetail extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;

    protected $fillable = [
        'contract_id',
        'contract_unit_id',
        'contract_unit_detail_id',
        'subunit_no',
        'subunit_type',
        'subunit_code',
        'subunit_rent',
        'added_by',
        'updated_by',
        'deleted_by',
        'is_vacant'
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function contract_unit()
    {
        return $this->belongsTo(ContractUnit::class);
    }

    public function contract_unit_detail()
    {
        return $this->belongsTo(ContractUnitDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(
            [User::class, 'added_by', 'id'],
            [User::class, 'updated_by', 'id'],
            [User::class, 'deleted_by', 'id'],
        );
    }
}
