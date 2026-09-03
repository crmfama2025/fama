<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'leads';

    protected $fillable = [
        'lead_code',
        'company_name',
        'contact_person_name',
        'phone_number',
        'email',
        'lead_source',
        'total_staff',
        'required_location',
        'requirement',
        'status',
        'assigned_to',
        'assigned_by',
        'assigned_at',
        'created_by',
        'updated_by',
        'deleted_by',
        'assigned_to',
        'assigned_by',
        'assigned_at'
    ];

    protected $casts = [
        'total_staff' => 'integer',
        'status' => 'integer',
        'assigned_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
    public function assignments(): HasMany
    {
        return $this->hasMany(LeadAssignment::class);
    }

    public function followUps()
    {
        return $this->hasMany(LeadFollowUp::class, 'lead_id');
    }
    public function latestFollowUp()
    {
        return $this->hasOne(LeadFollowUp::class, 'lead_id')->latestOfMany();
    }
    protected static function booted()
    {
        static::deleting(function ($lead) {
            $userId = auth()->id();

            // hasMany relations
            $hasManyRelations = [
                'followUps',
                'assignments',
            ];

            if (!$lead->isForceDeleting()) {
                // Soft delete hasMany relations
                foreach ($hasManyRelations as $relation) {
                    foreach ($lead->$relation as $related) {
                        $related->update([
                            'deleted_by' => $userId,
                        ]);
                        $related->delete();
                    }
                }

                // Store who deleted the lead
                $lead->updateQuietly([
                    'deleted_by' => $userId,
                ]);
            } else {
                // Force delete related records
                foreach ($hasManyRelations as $relation) {
                    $lead->$relation()
                        ->withTrashed()
                        ->forceDelete();
                }
            }
        });

        static::restoring(function ($lead) {
            $relations = [
                'followUps',
                'assignments',
            ];

            // Restore related records
            foreach ($relations as $relation) {
                $lead->$relation()
                    ->withTrashed()
                    ->restore();
            }
        });
    }
}
