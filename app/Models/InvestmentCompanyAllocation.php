<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvestmentCompanyAllocation extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;

    protected $fillable = [
        'investment_id',
        'company_id',
        'allocated_amount',
        'deleted_by',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function investment()
    {
        return $this->belongsTo(Investment::class, 'investment_id');
    }
}
