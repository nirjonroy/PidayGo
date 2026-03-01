<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NftSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_reserve_id',
        'nft_item_id',
        'sale_amount',
        'profit_percent',
        'profit_amount',
        'status',
    ];

    protected $casts = [
        'sale_amount' => 'decimal:8',
        'profit_percent' => 'decimal:3',
        'profit_amount' => 'decimal:8',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reserve()
    {
        return $this->belongsTo(UserReserve::class, 'user_reserve_id');
    }

    public function nftItem()
    {
        return $this->belongsTo(NftItem::class, 'nft_item_id');
    }
}
