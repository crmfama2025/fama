<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentProfitRecordRenewalLog extends Model
{
    use HasFactory;

    protected $table = 'investment_profit_record_renewal_logs';

    protected $fillable = [
        'investment_id',
        'investor_id',
        'old_maturity_date',
        'new_maturity_date',
        'first_profit_date',
        'last_profit_date',
        'created_profit_records',
        'updated_profit_records',
        'renewal_type',
        'processed_at'
    ];
}
