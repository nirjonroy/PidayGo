<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_id',
        'wallet_balance_min',
        'wallet_balance_max',
        'reserve_amount',
        'profit_min_percent',
        'profit_max_percent',
        'max_sells',
        'max_sells_per_day',
        'unlock_policy',
        'is_active',
    ];

    protected $casts = [
        'wallet_balance_min' => 'decimal:8',
        'wallet_balance_max' => 'decimal:8',
        'reserve_amount' => 'decimal:8',
        'profit_min_percent' => 'decimal:3',
        'profit_max_percent' => 'decimal:3',
        'max_sells' => 'integer',
        'max_sells_per_day' => 'integer',
        'unlock_policy' => 'string',
        'is_active' => 'boolean',
    ];

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function ranges()
    {
        return $this->hasMany(ReservePlanRange::class)->orderBy('wallet_balance_min')->orderBy('wallet_balance_max');
    }
}
