<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationAdmin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAlertController extends Controller
{
    public function index(Request $request): View
    {
        $adminId = $request->user('admin')->id;

        $items = NotificationAdmin::query()
            ->with('notification')
            ->where('admin_id', $adminId)
            ->whereHas('notification', function ($query) {
                $query->where('audience', 'admin')
                    ->where(function ($q) {
                        $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                    });
            })
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.alerts.index', [
            'items' => $items,
        ]);
    }

    public function markRead(Request $request, Notification $notification): RedirectResponse
    {
        $pivot = NotificationAdmin::where('notification_id', $notification->id)
            ->where('admin_id', $request->user('admin')->id)
            ->firstOrFail();

        if (!$pivot->read_at) {
            $pivot->update(['read_at' => now()]);
        }

        return back();
    }

    public function dismiss(Request $request, Notification $notification): RedirectResponse
    {
        $pivot = NotificationAdmin::where('notification_id', $notification->id)
            ->where('admin_id', $request->user('admin')->id)
            ->firstOrFail();

        if (!$pivot->dismissed_at) {
            $pivot->update(['dismissed_at' => now()]);
        }

        return back();
    }
}
