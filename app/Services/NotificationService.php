<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Notification;
use App\Models\NotificationAdmin;
use App\Models\NotificationUser;
use App\Models\User;
use App\Models\UserNotificationSetting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NotificationService
{
    public function __construct(private MailSettingsService $mailSettings)
    {
    }

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
        $userIds = $this->filterUserIdsByPreference($userIds, $type);

        $notification = DB::transaction(function () use ($userIds, $type, $title, $message, $level, $meta, $isPopup, $expiresAt, $senderAdminId) {
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

        $this->sendUserEmails($userIds, $notification);

        return $notification;
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
        $notification = DB::transaction(function () use ($type, $title, $message, $level, $isPopup, $expiresAt, $senderAdminId) {
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
                $userIds = $users->pluck('id')->all();
                $allowed = $this->filterUserIdsByPreference($userIds, $notification->type);
                $rows = [];
                $now = now();
                foreach ($users as $user) {
                    if (!in_array($user->id, $allowed, true)) {
                        continue;
                    }
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

        $this->sendEmailsToAllUsers($notification);

        return $notification;
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
        $notification = DB::transaction(function () use ($adminIds, $type, $title, $message, $level, $meta, $isPopup, $expiresAt) {
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

        $this->sendAdminEmails($notification);

        return $notification;
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

    private function sendUserEmails(array $userIds, Notification $notification): void
    {
        if (empty($userIds) || !$this->mailSettings->isActive()) {
            return;
        }

        $subject = $notification->title;
        $message = $notification->message;

        User::query()
            ->whereIn('id', $userIds)
            ->select(['id', 'email'])
            ->get()
            ->each(function (User $user) use ($subject, $message) {
                if (!empty($user->email)) {
                    $this->mailSettings->sendNotificationEmail($user->email, $subject, $message);
                }
            });
    }

    private function sendEmailsToAllUsers(Notification $notification): void
    {
        if (!$this->mailSettings->isActive()) {
            return;
        }

        $subject = $notification->title;
        $message = $notification->message;

        User::query()
            ->whereNotNull('email')
            ->select(['id', 'email'])
            ->orderBy('id')
            ->chunk(200, function ($users) use ($subject, $message, $notification) {
                $allowed = $this->filterUserIdsByPreference($users->pluck('id')->all(), $notification->type);
                foreach ($users as $user) {
                    if (!in_array($user->id, $allowed, true)) {
                        continue;
                    }
                    if (!empty($user->email)) {
                        $this->mailSettings->sendNotificationEmail($user->email, $subject, $message);
                    }
                }
            });
    }

    private function sendAdminEmails(Notification $notification): void
    {
        if (!$this->mailSettings->isActive()) {
            return;
        }

        $emails = $this->mailSettings->getAdminNotifyEmails();
        if (empty($emails)) {
            return;
        }

        $this->mailSettings->sendNotificationEmail($emails, $notification->title, $notification->message);
    }

    private function filterUserIdsByPreference(array $userIds, string $type): array
    {
        if (empty($userIds)) {
            return [];
        }

        $preferenceMap = [
            'deposit_submitted' => 'system_alerts',
            'deposit_approved' => 'system_alerts',
            'deposit_rejected' => 'system_alerts',
            'withdraw_requested' => 'system_alerts',
            'withdraw_approved' => 'system_alerts',
            'withdraw_rejected' => 'system_alerts',
            'stake_created' => 'system_alerts',
            'reserve_started' => 'system_alerts',
            'reserve_completed' => 'system_alerts',
            'kyc_submitted' => 'system_alerts',
            'kyc_approved' => 'system_alerts',
            'kyc_rejected' => 'system_alerts',
            'support_reply' => 'system_alerts',
            'admin_custom' => 'system_alerts',
        ];

        $column = $preferenceMap[$type] ?? null;
        if (!$column) {
            return $userIds;
        }

        if (!Schema::hasTable('user_notification_settings')) {
            return $userIds;
        }

        $settings = UserNotificationSetting::query()
            ->whereIn('user_id', $userIds)
            ->pluck($column, 'user_id');

        $allowed = [];
        foreach ($userIds as $userId) {
            if (!$settings->has($userId)) {
                $allowed[] = $userId;
                continue;
            }
            if ($settings->get($userId)) {
                $allowed[] = $userId;
            }
        }

        return $allowed;
    }
}
