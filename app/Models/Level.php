<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'min_deposit',
        'max_deposit',
        'req_chain_a',
        'req_chain_b',
        'req_chain_c',
        'min_reservation',
        'max_reservation',
        'income_min_percent',
        'income_max_percent',
        'chain_income_a_percent',
        'chain_income_b_percent',
        'chain_income_c_percent',
        'is_active',
    ];

    protected $casts = [
        'min_reservation' => 'decimal:8',
        'max_reservation' => 'decimal:8',
        'income_min_percent' => 'decimal:3',
        'income_max_percent' => 'decimal:3',
        'chain_income_a_percent' => 'decimal:3',
        'chain_income_b_percent' => 'decimal:3',
        'chain_income_c_percent' => 'decimal:3',
        'min_deposit' => 'decimal:8',
        'max_deposit' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    public function reservePlans()
    {
        return $this->hasMany(ReservePlan::class);
    }

    public function chainIncomePercentForDepth(int $depth): ?float
    {
        return match ($depth) {
            1 => $this->chain_income_a_percent !== null ? (float) $this->chain_income_a_percent : null,
            2 => $this->chain_income_b_percent !== null ? (float) $this->chain_income_b_percent : null,
            3 => $this->chain_income_c_percent !== null ? (float) $this->chain_income_c_percent : null,
            default => null,
        };
    }
}
