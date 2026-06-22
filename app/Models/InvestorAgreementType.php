<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvestorAgreementType extends Model
{
    use HasFactory, softDeletes, HasActivityLog, HasDeletedBy;

    protected $fillable = ['investor_agreement_type', 'status'];

    public function templates()
    {
        return $this->hasMany(InvestorAgreementTemplate::class, 'investor_agreement_type_id');
    }
}
