<?php

namespace App\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

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
        'pending_photo_path',
        'photo_status',
        'photo_submitted_at',
        'photo_reviewed_at',
        'photo_reviewed_by_admin_id',
        'banner_path',
        'bio',
        'social_twitter',
        'social_telegram',
        'social_discord',
    ];

    protected $casts = [
        'dob' => 'date',
        'photo_submitted_at' => 'datetime',
        'photo_reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'photo_reviewed_by_admin_id');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->resolvePublicMediaUrl($this->photo_path);
    }

    public function getPendingPhotoUrlAttribute(): ?string
    {
        return $this->resolvePublicMediaUrl($this->pending_photo_path);
    }

    public function getBannerUrlAttribute(): ?string
    {
        return $this->resolvePublicMediaUrl($this->banner_path);
    }

    public function getPhotoReviewDeadlineAttribute()
    {
        return $this->photo_submitted_at?->copy()->addHours(24);
    }

    public function hasPendingPhotoApproval(): bool
    {
        return $this->photo_status === 'pending' && !empty($this->pending_photo_path);
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

        if (!$this->publicMediaExists($path)) {
            return null;
        }

        return route('media.public', ['path' => ltrim($path, '/')]);
    }

    protected function publicMediaExists(string $path): bool
    {
        $path = ltrim($path, '/');

        foreach ($this->publicMediaRoots() as $root) {
            if ($this->exactMediaMatch($root, $path) !== null) {
                return true;
            }
        }

        $filename = basename($path);

        if ($filename === '') {
            return false;
        }

        foreach ($this->publicMediaRoots() as $root) {
            if ($this->findMediaByFilename($root, $filename) !== null) {
                return true;
            }
        }

        return false;
    }

    protected function publicMediaRoots(): array
    {
        return array_filter([
            storage_path('app/public'),
            storage_path('app'),
            public_path('storage'),
        ], static fn ($root) => is_dir($root));
    }

    protected function exactMediaMatch(string $root, string $path): ?string
    {
        $candidate = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);

        if (!File::isFile($candidate)) {
            return null;
        }

        return $candidate;
    }

    protected function findMediaByFilename(string $root, string $filename): ?string
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() === $filename) {
                return $file->getRealPath() ?: null;
            }
        }

        return null;
    }
}
