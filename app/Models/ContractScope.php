<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use App\Models\Traits\ScopeLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractScope extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy, ScopeLogTrait;

    protected $fillable = [
        'contract_id',
        'scope',
        'file_name',
        'generated_by',
        'updated_by',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
