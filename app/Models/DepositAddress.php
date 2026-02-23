<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'currency',
        'chain',
        'address',
        'qr_payload',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
