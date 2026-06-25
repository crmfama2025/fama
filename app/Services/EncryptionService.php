<?php

namespace App\Services;

use Illuminate\Encryption\Encrypter;

class EncryptionService
{
    protected Encrypter $encrypter;

    public function __construct()
    {
        $key     = config('bank.encryption_key');
        $decoded = base64_decode(substr($key, 7));

        $this->encrypter = new Encrypter($decoded, 'AES-256-CBC');
    }

    public function encrypt(?string $value): ?string
    {
        if (is_null($value)) return null;

        return $this->encrypter->encryptString($value);
    }

    public function decrypt(?string $value): ?string
    {
        if (is_null($value)) return null;

        return $this->encrypter->decryptString($value);
    }
}
