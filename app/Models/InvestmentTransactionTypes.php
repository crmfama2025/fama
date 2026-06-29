<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentTransactionTypes extends Model
{
    use HasFactory;
    protected $fillable = [
        'transaction_type'
    ];
}
