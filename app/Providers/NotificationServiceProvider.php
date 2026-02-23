<?php

namespace App\Providers;

use App\Models\NotificationUser;
use App\Models\NotificationAdmin;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('layouts.app', function ($view) {
            $user = auth()->user();

            if (!$user) {
                $view->with([
                    'userNotificationCount' => 0,
                    'popupNotification' => null,
                ]);
                return;
            }

            if (!Schema::hasTable('notification_users') || !Schema::hasTable('notifications')) {
                $view->with([
                    'userNotificationCount' => 0,
                    'popupNotification' => null,
                ]);
                return;
            }

            $baseQuery = NotificationUser::query()
                ->where('user_id', $user->id)
                ->whereHas('notification', function ($query) {
                    $query->where('audience', 'user')
                        ->where(function ($q) {
                            $q->whereNull('expires_at')
                                ->orWhere('expires_at', '>', now());
                        });
                });

            $count = (clone $baseQuery)
                ->whereNull('read_at')
                ->whereNull('dismissed_at')
                ->count();

            $popup = (clone $baseQuery)
                ->whereNull('read_at')
                ->whereNull('dismissed_at')
                ->whereNull('shown_popup_at')
                ->whereHas('notification', function ($query) {
                    $query->where('is_popup', true);
                })
                ->with('notification')
                ->orderBy('id')
                ->first();

            $view->with([
                'userNotificationCount' => $count,
                'popupNotification' => $popup,
            ]);
        });

        View::composer('layouts.admin-panel', function ($view) {
            $admin = auth('admin')->user();

            if (!$admin) {
                $view->with([
                    'adminNotificationCount' => 0,
                    'adminNotificationsPreview' => collect(),
                ]);
                return;
            }

            if (!Schema::hasTable('notification_admins') || !Schema::hasTable('notifications')) {
                $view->with([
                    'adminNotificationCount' => 0,
                    'adminNotificationsPreview' => collect(),
                ]);
                return;
            }

            $baseQuery = NotificationAdmin::query()
                ->where('admin_id', $admin->id)
                ->whereHas('notification', function ($query) {
                    $query->where('audience', 'admin')
                        ->where(function ($q) {
                            $q->whereNull('expires_at')
                                ->orWhere('expires_at', '>', now());
                        });
                });

            $count = (clone $baseQuery)
                ->whereNull('read_at')
                ->whereNull('dismissed_at')
                ->count();

            $preview = (clone $baseQuery)
                ->whereNull('dismissed_at')
                ->with('notification')
                ->orderByDesc('id')
                ->limit(5)
                ->get();

            $view->with([
                'adminNotificationCount' => $count,
                'adminNotificationsPreview' => $preview,
            ]);
        });
    }
}
