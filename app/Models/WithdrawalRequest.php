<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'requested_at',
        'eligible_at',
        'reviewed_at',
        'reviewed_by',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'requested_at' => 'datetime',
        'eligible_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function reviewer()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }
}
