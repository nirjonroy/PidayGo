<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservePlanRange extends Model
{
    use HasFactory;

    protected $fillable = [
        'reserve_plan_id',
        'wallet_balance_min',
        'wallet_balance_max',
        'reserve_percentage',
    ];

    protected $casts = [
        'wallet_balance_min' => 'decimal:8',
        'wallet_balance_max' => 'decimal:8',
        'reserve_percentage' => 'decimal:3',
    ];

    public function plan()
    {
        return $this->belongsTo(ReservePlan::class, 'reserve_plan_id');
    }
}
