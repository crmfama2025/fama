<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class InvestmentProfitRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'investor_id',
        'investment_id',
        'profit_release_month',
        'profit_amount',
        'release_status',
        'released_total_amount',
        'last_released_at',
        'last_released_by',
    ];

    protected $casts = [
        'profit_release_month'  => 'date',
        'last_released_at'  => 'date',
        'profit_amount'  => 'decimal:2',
        'released_total_amount'  => 'decimal:2',
    ];

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }

    // -----------------------
    // Scopes
    // -----------------------

    public function scopePending($query)
    {
        return $query->where('release_status', 'pending');
    }

    public function scopeReleased($query)
    {
        return $query->where('release_status', 'released');
    }

    public function scopePartiallyReleased($query)
    {
        return $query->where('release_status', 'partially_released');
    }

    public function scopeOnHold($query)
    {
        return $query->where('release_status', 'hold');
    }

    public function scopeForInvestor($query, int $investorId)
    {
        return $query->where('investor_id', $investorId);
    }

    public function scopeForInvestment($query, int $investmentId)
    {
        return $query->where('investment_id', $investmentId);
    }

    public function scopeHasProfit($query, int $investmentId)
    {
        return $query->where('has_profit_amount', $investmentId);
    }

    // -----------------------
    // Helpers
    // -----------------------

    public function markReleased(): void
    {
        $this->update([
            'release_status' => 'released',
            'released_at'    => now(),
        ]);
    }

    public function isReleased(): bool
    {
        return $this->release_status === 'released';
    }

    public function getProfitReleaseMonthAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }
}
