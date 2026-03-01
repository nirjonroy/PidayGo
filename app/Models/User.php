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
use App\Models\UserNotificationSetting;
use Illuminate\Support\Str;

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
        'user_code',
        'ref_code',
        'referred_by_id',
        'chain_slot',
        'chain_path',
        'is_master',
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
        'is_master' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function (self $user) {
            if (empty($user->user_code)) {
                $user->user_code = self::generateUserCode();
            }
            if (empty($user->ref_code)) {
                $user->ref_code = self::generateRefCode();
            }
        });
    }

    public static function generateUserCode(): string
    {
        do {
            $code = 'PG' . now()->format('ymd') . strtoupper(Str::random(6));
        } while (self::where('user_code', $code)->exists());

        return $code;
    }

    public static function generateRefCode(): string
    {
        do {
            $code = 'PG' . strtoupper(Str::random(8));
        } while (self::where('ref_code', $code)->exists());

        return $code;
    }

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

    public function notificationSettings()
    {
        return $this->hasOne(UserNotificationSetting::class);
    }

    public function sponsor()
    {
        return $this->belongsTo(User::class, 'referred_by_id');
    }

    public function placementParent()
    {
        return $this->belongsTo(User::class, 'referred_by_id');
    }

    public function placements()
    {
        return $this->hasMany(User::class, 'referred_by_id');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by_id');
    }

    public function reserves()
    {
        return $this->hasMany(UserReserve::class);
    }
}
