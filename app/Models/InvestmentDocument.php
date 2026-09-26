<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvestmentDocument extends Model
{
    use HasFactory,  SoftDeletes, HasActivityLog, HasDeletedBy;

    protected $table = 'investment_documents';


    protected $fillable = [
        'investment_id',
        'investor_id',
        'investment_contract_file_name',
        'investment_contract_file_path',

        'investment_contract_document_id',
        'version_number',
        'investment_agreement_type_id',
        'company_id',
        'document_date',

        'added_by',
        'updated_by',
        'deleted_by',
    ];

    public function additionalDocuments()
    {
        return $this->hasMany(
            InvestmentAdditionalDocuments::class,
            'investment_document_id'
        );
    }
    public function agreementType()
    {
        return $this->belongsTo(InvestorAgreementType::class, 'investment_agreement_type_id');
    }
}
