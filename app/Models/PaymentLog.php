<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway',
        'deposit_request_id',
        'track_id',
        'order_id',
        'status',
        'signature',
        'signature_valid',
        'response_code',
        'message',
        'headers',
        'payload',
        'raw_body',
    ];

    protected $casts = [
        'signature_valid' => 'boolean',
        'headers' => 'array',
        'payload' => 'array',
    ];

    public function depositRequest()
    {
        return $this->belongsTo(DepositRequest::class);
    }
}
