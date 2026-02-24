<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NftItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'image_path',
        'description',
        'creator_seller_id',
        'owner_seller_id',
        'price',
        'auction_end_at',
        'likes_count',
        'views_count',
        'is_trending',
        'is_featured',
        'status',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:8',
        'auction_end_at' => 'datetime',
        'likes_count' => 'integer',
        'views_count' => 'integer',
        'is_trending' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function creatorSeller()
    {
        return $this->belongsTo(Seller::class, 'creator_seller_id');
    }

    public function ownerSeller()
    {
        return $this->belongsTo(Seller::class, 'owner_seller_id');
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }
}
