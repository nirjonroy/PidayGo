<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Notification;
use App\Models\NotificationAdmin;
use App\Models\NotificationUser;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    public function notifyUser(
        int $userId,
        string $type,
        string $title,
        string $message,
        string $level = 'info',
        array $meta = [],
        bool $isPopup = false,
        $expiresAt = null,
        ?int $senderAdminId = null
    ): Notification {
        return $this->notifyUsers([$userId], $type, $title, $message, $level, $meta, $isPopup, $expiresAt, $senderAdminId);
    }

    public function notifyUsers(
        array $userIds,
        string $type,
        string $title,
        string $message,
        string $level = 'info',
        array $meta = [],
        bool $isPopup = false,
        $expiresAt = null,
        ?int $senderAdminId = null
    ): Notification {
        return DB::transaction(function () use ($userIds, $type, $title, $message, $level, $meta, $isPopup, $expiresAt, $senderAdminId) {
            $notification = $this->createNotification(
                'user',
                $type,
                $title,
                $message,
                $level,
                $meta,
                $isPopup,
                $expiresAt,
                $senderAdminId
            );

            $this->attachUsers($notification->id, $userIds);

            return $notification;
        });
    }

    public function notifyAdmin(
        int $adminId,
        string $type,
        string $title,
        string $message,
        string $level = 'info',
        array $meta = [],
        bool $isPopup = false,
        $expiresAt = null
    ): Notification {
        return $this->notifyAdmins([$adminId], $type, $title, $message, $level, $meta, $isPopup, $expiresAt);
    }

    public function notifyAdminsByRoleOrPermission(
        string $permission,
        string $type,
        string $title,
        string $message,
        string $level = 'info',
        array $meta = [],
        bool $isPopup = false,
        $expiresAt = null
    ): ?Notification {
        $adminIds = Admin::permission($permission)->pluck('id')->all();
        if (empty($adminIds)) {
            return null;
        }

        return $this->notifyAdmins($adminIds, $type, $title, $message, $level, $meta, $isPopup, $expiresAt);
    }

    public function broadcastToAllUsers(
        string $type,
        string $title,
        string $message,
        string $level = 'info',
        bool $isPopup = false,
        $expiresAt = null,
        ?int $senderAdminId = null
    ): Notification {
        return DB::transaction(function () use ($type, $title, $message, $level, $isPopup, $expiresAt, $senderAdminId) {
            $notification = $this->createNotification(
                'user',
                $type,
                $title,
                $message,
                $level,
                [],
                $isPopup,
                $expiresAt,
                $senderAdminId
            );

            User::query()->select('id')->orderBy('id')->chunk(500, function ($users) use ($notification) {
                $rows = [];
                $now = now();
                foreach ($users as $user) {
                    $rows[] = [
                        'notification_id' => $notification->id,
                        'user_id' => $user->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                if ($rows) {
                    NotificationUser::insert($rows);
                }
            });

            return $notification;
        });
    }

    private function notifyAdmins(
        array $adminIds,
        string $type,
        string $title,
        string $message,
        string $level,
        array $meta,
        bool $isPopup,
        $expiresAt
    ): Notification {
        return DB::transaction(function () use ($adminIds, $type, $title, $message, $level, $meta, $isPopup, $expiresAt) {
            $notification = $this->createNotification(
                'admin',
                $type,
                $title,
                $message,
                $level,
                $meta,
                $isPopup,
                $expiresAt,
                null
            );

            $this->attachAdmins($notification->id, $adminIds);

            return $notification;
        });
    }

    private function createNotification(
        string $audience,
        string $type,
        string $title,
        string $message,
        string $level,
        array $meta,
        bool $isPopup,
        $expiresAt,
        ?int $senderAdminId
    ): Notification {
        return Notification::create([
            'audience' => $audience,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'level' => $level,
            'metadata' => $meta,
            'is_popup' => $isPopup,
            'expires_at' => $this->parseExpires($expiresAt),
            'sender_admin_id' => $senderAdminId,
        ]);
    }

    private function attachUsers(int $notificationId, array $userIds): void
    {
        $rows = [];
        $now = now();
        foreach ($userIds as $userId) {
            $rows[] = [
                'notification_id' => $notificationId,
                'user_id' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($rows) {
            NotificationUser::insert($rows);
        }
    }

    private function attachAdmins(int $notificationId, array $adminIds): void
    {
        $rows = [];
        $now = now();
        foreach ($adminIds as $adminId) {
            $rows[] = [
                'notification_id' => $notificationId,
                'admin_id' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($rows) {
            NotificationAdmin::insert($rows);
        }
    }

    private function parseExpires($expiresAt): ?Carbon
    {
        if (!$expiresAt) {
            return null;
        }

        if ($expiresAt instanceof Carbon) {
            return $expiresAt;
        }

        return Carbon::parse($expiresAt);
    }
}
