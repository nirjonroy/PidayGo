<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasRoles;

    protected $guard_name = 'admin';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function kycReviews()
    {
        return $this->hasMany(KycRequest::class, 'reviewed_by');
    }

    public function createdWalletLedgers()
    {
        return $this->hasMany(WalletLedger::class, 'created_by_admin_id');
    }

    public function createdReserveLedgers()
    {
        return $this->hasMany(ReserveLedger::class, 'created_by_admin_id');
    }
}
