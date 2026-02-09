<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReserveLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'reserve_account_id',
        'amount',
        'reason',
        'created_by_admin_id',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'meta' => 'array',
    ];

    public function reserveAccount()
    {
        return $this->belongsTo(ReserveAccount::class);
    }

    public function createdByAdmin()
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }
}
