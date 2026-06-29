<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvestmentContractDocuments extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;

    protected $fillable = [
        'investment_id',
        'investor_id',
        'investor_agreement_template_id',
        'investor_agreement_type_id',
        'is_investor_signed',
        'investor_signed_at',
        'is_company_signed',
        'company_signed_at',
        'status',
        'added_by',
        'contract_document_html',
        'contract_file_path',
        'additional_file_path',
        'generated_date',
        'has_additional_doc',
        'action_type',
        'generated_by'
    ];

    public function investment()
    {
        return $this->belongsTo(Investment::class, 'investment_id');
    }

    public function investor()
    {
        return $this->belongsTo(Investor::class, 'investor_id');
    }

    public function agreementTemplate()
    {
        return $this->belongsTo(InvestorAgreementTemplate::class, 'investor_agreement_template_id');
    }

    public function agreementType()
    {
        return $this->belongsTo(InvestorAgreementType::class, 'investor_agreement_type_id');
    }
}
