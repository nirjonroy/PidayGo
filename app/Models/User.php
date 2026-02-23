<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\CustomVerifyEmail;
use App\Models\UserProfile;
use App\Models\UserBankAccount;
use App\Models\UserReserve;
use App\Models\UserReserveLedger;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'two_factor_secret',
        'two_factor_confirmed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'two_factor_secret' => 'encrypted',
        'two_factor_confirmed_at' => 'datetime',
    ];

    public function kycRequests()
    {
        return $this->hasMany(KycRequest::class);
    }

    public function latestKycRequest()
    {
        return $this->hasOne(KycRequest::class)->latestOfMany();
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(UserBankAccount::class);
    }

    public function reserve()
    {
        return $this->hasOne(UserReserve::class);
    }

    public function reserveLedgers()
    {
        return $this->hasMany(UserReserveLedger::class);
    }

    public function hasTwoFactorEnabled(): bool
    {
        return !empty($this->two_factor_secret) && !is_null($this->two_factor_confirmed_at);
    }

    public function isKycApproved(): bool
    {
        return $this->latestKycRequest?->status === 'approved';
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail());
    }

    public function stakes()
    {
        return $this->hasMany(Stake::class);
    }

    public function walletLedgers()
    {
        return $this->hasMany(WalletLedger::class);
    }

    public function depositRequests()
    {
        return $this->hasMany(DepositRequest::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }
}
