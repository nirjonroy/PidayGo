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
        'bank_name',
        'account_name',
        'account_number',
        'branch',
        'routing_number',
        'swift_code',
        'ifsc_code',
        'currency',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setAccountNumberAttribute(?string $value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['account_number'] = null;
            return;
        }

        $this->attributes['account_number'] = Crypt::encryptString($value);
    }

    public function getAccountNumberAttribute($value): ?string
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
