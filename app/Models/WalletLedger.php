<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'reference_type',
        'reference_id',
        'meta',
        'created_by_admin_id',
    ];

    protected $casts = [
        'meta' => 'array',
        'amount' => 'decimal:8',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createdByAdmin()
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }
}
