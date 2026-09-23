<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentRenewalEditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'investment_id',
        'created_by',
        'reason',
        'before_values',
        'after_values',
        'changes',
        'schedule_changes',
    ];

    protected $casts = [
        'before_values' => 'array',
        'after_values' => 'array',
        'changes' => 'array',
        'schedule_changes' => 'array',
    ];

    public function investment()
    {
        return $this->belongsTo(Investment::class, 'investment_id');
    }
}
