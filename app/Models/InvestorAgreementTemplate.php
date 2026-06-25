<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestorAgreementTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'investor_agreement_type_id',
        'version_no',
        'effective_from',
        'template',
        'added_by',
        'updated_by',
        'status',
    ];

    public function agreementType()
    {
        return $this->belongsTo(InvestorAgreementType::class, 'investor_agreement_type_id');
    }

    public function setEffectiveFromAttribute($value)
    {
        $this->attributes['effective_from'] = date('Y-m-d', strtotime($value));
    }

    public function getEffectiveFromAttribute($value)
    {
        return date('d-m-Y', strtotime($value));
    }
}
