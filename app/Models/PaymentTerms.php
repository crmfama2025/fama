<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTerms extends Model
{
    use HasFactory;
    protected $table = 'payment_terms';

    protected $fillable = [
        'term_name',
        // 'no_of_installments',
        'status',
    ];
}
