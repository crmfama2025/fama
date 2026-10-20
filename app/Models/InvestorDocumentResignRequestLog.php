<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestorDocumentResignRequestLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'investment_contract_document_id',
        'attempt_no',
        'reason',
        'channel',
        'requested_by',
        'rejected_signed_html',
        'rejected_signature_image',
        'investor_signed_at',
        'resigned_at',
        'signer_role'
    ];

    public function InvestmentContractDocument()
    {
        return $this->belongsTo(InvestmentContractDocuments::class, 'investment_contract_document_id');
    }

    public function requester()
    {
        return $this->belongsTo(\App\Models\User::class, 'requested_by');
    }

    public function resignRequests()
    {
        return $this->hasMany(InvestorDocumentResignRequestLog::class, 'investment_contract_document_id')->latest();
    }
}
