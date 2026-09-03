<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadFollowUp extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lead_follow_ups';

    protected $fillable = [

        'lead_id',
        'followed_up_by',
        'follow_up_type',
        'follow_up_status',
        'follow_up_date',
        'next_follow_up_date',
        'next_follow_up_time',
        'meeting_location',
        'meeting_date',
        'meeting_time',
        'not_interested_reason',
        'notes',
    ];


    protected $casts = [

        'follow_up_type' => 'integer',

        'follow_up_status' => 'integer',

        'follow_up_date' => 'date',

        'next_follow_up_date' => 'datetime',

        'meeting_date' => 'date',


    ];



    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }




    public function followedUpBy()
    {
        return $this->belongsTo(User::class, 'followed_up_by');
    }



    public function getFollowUpTypeNameAttribute()
    {
        return match ((int) $this->follow_up_type) {

            1 => 'Phone Call',

            2 => 'WhatsApp',

            3 => 'Email',

            4 => 'Meeting',

            5 => 'SMS',

            6 => 'Other',

            default => '-',
        };
    }



    public function getOutcomeNameAttribute()
    {
        return match ((int) $this->outcome) {

            2 => 'Interested',

            3 => 'Call Back',

            4 => 'No Answer',

            5 => 'Not Interested',

            6 => 'Meeting Scheduled',

            7 => 'Proposal Sent',

            8 => 'Negotiation',

            9 => 'Converted',

            10 => 'Lost',

            11 => 'Others',

            default => '-',
        };
    }
}
