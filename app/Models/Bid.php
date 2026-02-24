<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    use HasFactory;

    protected $fillable = [
        'nft_item_id',
        'user_id',
        'bidder_name',
        'amount',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    public function item()
    {
        return $this->belongsTo(NftItem::class, 'nft_item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
