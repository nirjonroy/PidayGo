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
        'document_front_path',
        'document_back_path',
        'selfie_path',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }
}
