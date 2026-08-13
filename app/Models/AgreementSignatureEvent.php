<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgreementSignatureEvent extends Model
{
    use HasFactory;

    protected $fillable = ['contract_id', 'signer_role', 'event_type', 'channel', 'occurred_at', 'send_by'];
}
