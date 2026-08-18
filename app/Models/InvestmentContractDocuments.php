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
        'company_id',
        'investor_agreement_template_id',
        'investor_agreement_type_id',
        'applied_investments',
        'reference_mudarabah_id',
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
        'generated_by',
        'sendto_investor_by',
        'sendto_management_by',
        'sendto_investor_date',
        'sendto_management_date',
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

    public function mudarabahReference()
    {
        return $this->belongsTo(InvestmentContractDocuments::class, 'reference_mudarabah_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
    public function sendToInvestorBy()
    {
        return $this->belongsTo(User::class, 'sendto_investor_by');
    }
    public function ledger()
    {
        return $this->hasOne(InvestorLedger::class, 'investment_contract_document_id');
    }
}
