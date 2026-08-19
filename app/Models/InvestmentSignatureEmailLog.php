<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentSignatureEmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'investment_contract_document_id',
        'recipient_type',
        'recipient_email',
        'recipient_name',
        'subject',
        'template',
        'status',
        'response',
        'attempt_count',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function investmentContractDocument()
    {
        return $this->belongsTo(InvestmentContractDocuments::class, 'investment_contract_document_id');
    }
}
