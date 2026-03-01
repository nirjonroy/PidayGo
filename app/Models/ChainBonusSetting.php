<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChainBonusSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'depth',
        'percent',
        'is_active',
    ];

    protected $casts = [
        'percent' => 'decimal:3',
        'is_active' => 'boolean',
    ];
}
