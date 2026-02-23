<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class UserNotificationController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        $items = NotificationUser::query()
            ->with('notification')
            ->where('user_id', $userId)
            ->whereHas('notification', function ($query) {
                $query->where('audience', 'user')
                    ->where(function ($q) {
                        $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                    });
            })
            ->orderByDesc('id')
            ->paginate(20);

        return view('notifications.index', [
            'items' => $items,
        ]);
    }

    public function markRead(Request $request, Notification $notification): RedirectResponse
    {
        $pivot = NotificationUser::where('notification_id', $notification->id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if (!$pivot->read_at) {
            $pivot->update(['read_at' => now()]);
        }

        return back();
    }

    public function dismiss(Request $request, Notification $notification): RedirectResponse
    {
        $pivot = NotificationUser::where('notification_id', $notification->id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if (!$pivot->dismissed_at) {
            $pivot->update(['dismissed_at' => now()]);
        }

        return back();
    }

    public function shown(Request $request, Notification $notification): Response
    {
        $pivot = NotificationUser::where('notification_id', $notification->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($pivot && !$pivot->shown_popup_at) {
            $pivot->update(['shown_popup_at' => now()]);
        }

        return response()->noContent();
    }
}
