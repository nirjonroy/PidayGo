<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_active',
        'primary_host',
        'primary_port',
        'primary_username',
        'primary_password_encrypted',
        'primary_encryption',
        'primary_from_address',
        'primary_from_name',
        'secondary_host',
        'secondary_port',
        'secondary_username',
        'secondary_password_encrypted',
        'secondary_encryption',
        'secondary_from_address',
        'secondary_from_name',
        'verification_mailer',
        'notification_mailer',
        'admin_notify_emails',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'primary_port' => 'integer',
        'secondary_port' => 'integer',
    ];
}
