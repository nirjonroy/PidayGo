<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class UserBankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'network',
        'wallet_address',
        'address_label',
        'memo_tag',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setWalletAddressAttribute(?string $value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['wallet_address'] = null;
            return;
        }

        $this->attributes['wallet_address'] = Crypt::encryptString($value);
    }

    public function getWalletAddressAttribute($value): ?string
    {
        if (!$value) {
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
