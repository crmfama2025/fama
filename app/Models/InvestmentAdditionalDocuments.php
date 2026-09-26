<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvestmentAdditionalDocuments extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'investment_document_id',
        'investment_contract_document_id',
        'document_name',
        'document_path',
        'document_date',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function investmentDocument()
    {
        return $this->belongsTo(
            InvestmentDocument::class,
            'investment_document_id'
        );
    }
}
