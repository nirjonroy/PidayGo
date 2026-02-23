<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
        'notes',
        'document_type',
        'document_number',
        'document_front_path',
        'document_back_path',
        'selfie_path',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function setDocumentNumberAttribute(?string $value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['document_number'] = null;
            return;
        }

        $this->attributes['document_number'] = \Illuminate\Support\Facades\Crypt::encryptString($value);
    }

    public function getDocumentNumberAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return \Illuminate\Support\Facades\Crypt::decryptString($value);
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }
}
