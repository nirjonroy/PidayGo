<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_id',
        'reserve_amount',
        'profit_min_percent',
        'profit_max_percent',
        'max_sells',
        'unlock_policy',
        'is_active',
    ];

    protected $casts = [
        'reserve_amount' => 'decimal:8',
        'profit_min_percent' => 'decimal:3',
        'profit_max_percent' => 'decimal:3',
        'max_sells' => 'integer',
        'unlock_policy' => 'string',
        'is_active' => 'boolean',
    ];

    public function level()
    {
        return $this->belongsTo(Level::class);
    }
}
