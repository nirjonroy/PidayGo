<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReserve extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reserved_balance',
    ];

    protected $casts = [
        'reserved_balance' => 'decimal:8',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
