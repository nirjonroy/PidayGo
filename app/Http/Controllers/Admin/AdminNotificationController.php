<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminNotificationController extends Controller
{
    public function index(): View
    {
        $notifications = Notification::query()
            ->where('type', 'admin_custom')
            ->where('audience', 'user')
            ->withCount('userRecipients')
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    public function create(): View
    {
        return view('admin.notifications.create', [
            'users' => User::orderBy('email')->get(),
        ]);
    }

    public function store(Request $request, NotificationService $service): RedirectResponse
    {
        $validated = $request->validate([
            'audience' => ['required', 'in:user'],
            'recipients_mode' => ['required', 'in:all,selected'],
            'recipients' => ['nullable', 'array'],
            'recipients.*' => ['integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:2000'],
            'level' => ['required', 'in:info,success,warning,error'],
            'is_popup' => ['nullable', 'boolean'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $isPopup = (bool) ($validated['is_popup'] ?? false);
        $expiresAt = $validated['expires_at'] ?? null;
        $adminId = $request->user('admin')->id;

        if ($validated['recipients_mode'] === 'all') {
            $service->broadcastToAllUsers(
                'admin_custom',
                $validated['title'],
                $validated['message'],
                $validated['level'],
                $isPopup,
                $expiresAt,
                $adminId
            );
        } else {
            $userIds = $validated['recipients'] ?? [];
            if (empty($userIds)) {
                return back()->withErrors(['recipients' => 'Select at least one user.'])->withInput();
            }

            $service->notifyUsers(
                $userIds,
                'admin_custom',
                $validated['title'],
                $validated['message'],
                $validated['level'],
                [],
                $isPopup,
                $expiresAt,
                $adminId
            );
        }

        return redirect()->route('admin.notifications.index')->with('status', 'Notification sent.');
    }
}
