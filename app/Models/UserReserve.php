<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReserve extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reserved_balance',
        'level_id',
        'reserve_plan_id',
        'amount',
        'status',
        'confirmed_at',
        'sell_available_at',
        'completed_at',
        'meta',
    ];

    protected $casts = [
        'reserved_balance' => 'decimal:8',
        'amount' => 'decimal:8',
        'confirmed_at' => 'datetime',
        'sell_available_at' => 'datetime',
        'completed_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function plan()
    {
        return $this->belongsTo(ReservePlan::class, 'reserve_plan_id');
    }

    public function sale()
    {
        return $this->hasOne(NftSale::class, 'user_reserve_id');
    }

    public function isSellUnlocked(): bool
    {
        return $this->sell_available_at === null || now()->gte($this->sell_available_at);
    }
}
