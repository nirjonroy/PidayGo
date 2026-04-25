<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class GatewaySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway_name',
        'api_key',
        'secret_key',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function setApiKeyAttribute(?string $value): void
    {
        $this->attributes['api_key'] = $this->encryptSecret($value);
    }

    public function getApiKeyAttribute(?string $value): ?string
    {
        return $this->decryptSecret($value);
    }

    public function setSecretKeyAttribute(?string $value): void
    {
        $this->attributes['secret_key'] = $this->encryptSecret($value);
    }

    public function getSecretKeyAttribute(?string $value): ?string
    {
        return $this->decryptSecret($value);
    }

    private function encryptSecret(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        return Crypt::encryptString($value);
    }

    private function decryptSecret(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (Throwable $e) {
            report($e);
            return null;
        }
    }
}
