<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StakePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'min_amount',
        'max_amount',
        'daily_rate',
        'duration_days',
        'max_payout_multiplier',
        'level_required',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_amount' => 'decimal:8',
        'max_amount' => 'decimal:8',
        'daily_rate' => 'decimal:6',
        'max_payout_multiplier' => 'decimal:2',
    ];

    public function stakes()
    {
        return $this->hasMany(Stake::class);
    }
}
