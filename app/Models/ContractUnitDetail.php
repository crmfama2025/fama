<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractUnitDetail extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;

    protected $fillable = [
        'contract_id',
        'contract_unit_id',
        'unit_number',
        'unit_type_id',
        'floor_no',
        'unit_status_id',
        'unit_rent_per_annum',
        'fb_unit_count',
        'unit_size_unit_id',
        'unit_size',
        'property_type_id',
        'partition',
        'bedspace',
        'room',
        'maid_room',
        'rent_per_flat',
        'rent_per_unit_per_month',
        'rent_per_unit_per_annum',
        'total_rent_per_unit_per_month',
        'subunittype',
        'subunitcount_per_unit',
        'subunit_rent_per_unit',
        'total_partition',
        'total_bedspace',
        'rent_per_partition',
        'rent_per_bedspace',
        'rent_per_room',
        'unit_profit_perc',
        'unit_profit',
        'unit_revenue',
        'unit_amount_payable',
        'unit_commission',
        'unit_deposit',
        'added_by',
        'updated_by',
        'deleted_by',
        'is_vacant',
        'unit_rent_per_month',
        'subunit_occupied_count',
        'subunit_vacant_count',
        'total_payment_received',
        'total_payment_pending',
        'total_room'
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function contract_unit()
    {
        return $this->belongsTo(ContractUnit::class);
    }

    public function unit_type()
    {
        return $this->belongsTo(UnitType::class);
    }

    public function unit_status()
    {
        return $this->belongsTo(UnitStatus::class);
    }

    public function unit_size_unit()
    {
        return $this->belongsTo(UnitSizeUnit::class);
    }

    public function property_type()
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function user()
    {
        return $this->belongsTo(
            [User::class, 'added_by', 'id'],
            [User::class, 'updated_by', 'id'],
            [User::class, 'deleted_by', 'id'],
        );
    }

    // public function contractSubUnitDetails()
    // {
    //     return $this->hasMany(ContractSubUnitDetail::class);
    // }

    public function contractSubUnitDetails()  // <-- this name must match what you call
    {
        return $this->hasMany(ContractSubunitDetail::class, 'contract_unit_detail_id');
    }

    private function formatNumber($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = (float) $value;

        if (fmod($value, 1) !== 0.0) {
            return rtrim(rtrim(number_format($value, 2, '.', ','), '0'), '.');
        }

        return number_format((int) $value);
    }

    public function getAttributeValue($key)
    {
        // $value = parent::getAttributeValue($key);
        $formatted = [
            'unit_rent_per_annum',
            'rent_per_room',
            'rent_per_partition',
            'rent_per_bedspace',
        ];

        // ✅ Safely get value only if attribute exists
        $value = array_key_exists($key, $this->attributes)
            ? parent::getAttributeValue($key)
            : null;

        // ✅ Only format if the key is one of your formatted fields
        //    and the value is numeric
        if (in_array($key, $formatted, true) && is_numeric($value)) {
            return $this->formatNumber($value);
        }

        return $value ?? parent::getAttributeValue($key);
    }

    // public function getUnitRentPerAnnumAttribute($value)
    // {
    //     return formatNumber($value);
    // }
    // public function getRentPerRoomAttribute($value)
    // {
    //     return formatNumber($value);
    // }
    // public function getRentPerPartitionAttribute($value)
    // {
    //     return formatNumber($value);
    // }
    // public function getRentPerBedspaceAttribute($value)
    // {
    //     return formatNumber($value);
    // }

    protected static function booted()
    {
        static::deleting(function ($contractUnitDetail) {

            $userId = auth()->id();
            // dd($contractUnitDetail->contract_subunit_details);
            // hasMany relations
            $hasManyRelations = [
                'contractSubUnitDetails',
            ];

            if (!$contractUnitDetail->isForceDeleting()) {
                // Soft delete hasMany
                foreach ($hasManyRelations as $relation) {
                    // print($contractUnitDetail->$relation);
                    foreach ($contractUnitDetail->$relation as $related) {
                        $relatedModels = $contractUnitDetail->$relation ?? collect(); // avoid null

                        foreach ($relatedModels as $related) {
                            if ($userId) {
                                $related->update(['deleted_by' => $userId]);
                            }
                            $related->delete();
                        }
                    }
                }
            } else {
                // Force delete
                foreach (array_merge($hasManyRelations) as $relation) {
                    $contractUnitDetail->$relation()->withTrashed()->forceDelete();
                }
            }
        });

        static::restoring(function ($contractUnitDetail) {
            $relations = [
                'contract_subunit_details',
            ];

            foreach ($relations as $relation) {
                $contractUnitDetail->$relation()->withTrashed()->restore();
            }
        });
    }


    public function agreementUnits()
    {
        return $this->hasMany(AgreementUnit::class, 'contract_unit_details_id');
    }
}
