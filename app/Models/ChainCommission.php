<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChainCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_user_id',
        'target_user_id',
        'nft_sale_id',
        'level_depth',
        'percent',
        'amount',
    ];

    protected $casts = [
        'percent' => 'decimal:3',
        'amount' => 'decimal:8',
    ];

    public function sourceUser()
    {
        return $this->belongsTo(User::class, 'source_user_id');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function sale()
    {
        return $this->belongsTo(NftSale::class, 'nft_sale_id');
    }
}
