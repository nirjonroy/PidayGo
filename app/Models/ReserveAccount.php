<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReserveAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:8',
    ];

    public function ledgers()
    {
        return $this->hasMany(ReserveLedger::class);
    }
}
