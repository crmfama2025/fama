<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UnitType;

class LocalityPricing extends Model
{
    use HasFactory;
    protected $fillable = [
        'locality_id',
        'unit_type_id',
        'price',
    ];
    public function unitType()
    {
        return $this->belongsTo(UnitType::class, 'unit_type_id');
    }
}
