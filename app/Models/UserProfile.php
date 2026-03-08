<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'username',
        'custom_url',
        'phone',
        'country',
        'city',
        'address',
        'dob',
        'photo_path',
        'banner_path',
        'bio',
        'social_twitter',
        'social_telegram',
        'social_discord',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->resolvePublicMediaUrl($this->photo_path);
    }

    public function getBannerUrlAttribute(): ?string
    {
        return $this->resolvePublicMediaUrl($this->banner_path);
    }

    protected function resolvePublicMediaUrl(?string $path): ?string
    {
        $path = trim(str_replace('\\', '/', (string) $path));

        if ($path === '') {
            return null;
        }

        if (Str::contains($path, '/storage/')) {
            $path = Str::after($path, '/storage/');
        } elseif (Str::contains($path, 'storage/app/public/')) {
            $path = Str::after($path, 'storage/app/public/');
        } elseif (Str::startsWith($path, 'storage/')) {
            $path = Str::after($path, 'storage/');
        } elseif (Str::startsWith($path, 'public/')) {
            $path = Str::after($path, 'public/');
        } elseif (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}
