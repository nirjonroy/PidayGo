<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'system_alerts',
        'item_sold',
        'auction_expiration',
        'bid_activity',
        'outbid',
        'price_change',
        'successful_purchase',
    ];

    protected $casts = [
        'system_alerts' => 'boolean',
        'item_sold' => 'boolean',
        'auction_expiration' => 'boolean',
        'bid_activity' => 'boolean',
        'outbid' => 'boolean',
        'price_change' => 'boolean',
        'successful_purchase' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
