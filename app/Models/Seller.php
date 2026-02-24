<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'username',
        'avatar_path',
        'volume',
        'is_verified',
        'is_active',
    ];

    protected $casts = [
        'volume' => 'decimal:8',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function createdItems()
    {
        return $this->hasMany(NftItem::class, 'creator_seller_id');
    }

    public function ownedItems()
    {
        return $this->hasMany(NftItem::class, 'owner_seller_id');
    }
}
