<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stake extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stake_plan_id',
        'principal_amount',
        'status',
        'started_at',
        'ends_at',
        'closed_at',
        'last_reward_at',
        'total_reward_paid',
    ];

    protected $casts = [
        'principal_amount' => 'decimal:8',
        'total_reward_paid' => 'decimal:8',
        'started_at' => 'datetime',
        'ends_at' => 'datetime',
        'last_reward_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(StakePlan::class, 'stake_plan_id');
    }
}
