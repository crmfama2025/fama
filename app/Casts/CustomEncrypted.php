<?php

namespace App\Casts;

use App\Services\EncryptionService;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class CustomEncrypted implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        return app(EncryptionService::class)->decrypt($value);
    }

    public function set($model, $key, $value, $attributes)
    {
        return app(EncryptionService::class)->encrypt($value);
    }
}
