<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'actor_type',
        'actor_id',
        'action',
        'subject_type',
        'subject_id',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function actor()
    {
        return $this->morphTo();
    }

    public function subject()
    {
        return $this->morphTo();
    }

    public static function record(string $action, ?Model $actor, ?Model $subject = null, array $metadata = []): self
    {
        return static::create([
            'actor_type' => $actor?->getMorphClass(),
            'actor_id' => $actor?->getKey(),
            'action' => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => Arr::wrap($metadata),
        ]);
    }
}
