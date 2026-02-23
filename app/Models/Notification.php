<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'level',
        'audience',
        'sender_admin_id',
        'metadata',
        'is_popup',
        'expires_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_popup' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function senderAdmin()
    {
        return $this->belongsTo(Admin::class, 'sender_admin_id');
    }

    public function userRecipients()
    {
        return $this->hasMany(NotificationUser::class);
    }

    public function adminRecipients()
    {
        return $this->hasMany(NotificationAdmin::class);
    }
}
