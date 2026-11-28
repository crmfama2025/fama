<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestorPayoutAllocation extends Model
{
    protected $guarded = [];
    protected $casts = ['payout_ids' => 'array', 'allocations' => 'array', 'distribution_ids' => 'array'];
}
