<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'min_reservation',
        'max_reservation',
        'income_min_percent',
        'income_max_percent',
        'is_active',
    ];

    protected $casts = [
        'min_reservation' => 'decimal:8',
        'max_reservation' => 'decimal:8',
        'income_min_percent' => 'decimal:3',
        'income_max_percent' => 'decimal:3',
        'is_active' => 'boolean',
    ];
}
