<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'currency',
        'chain',
        'to_address',
        'amount',
        'txid',
        'status',
        'expires_at',
        'reviewed_by',
        'reviewed_at',
        'admin_note',
        'credited_ledger_id',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'expires_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewedBy()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }

    public function creditedLedger()
    {
        return $this->belongsTo(WalletLedger::class, 'credited_ledger_id');
    }
}
