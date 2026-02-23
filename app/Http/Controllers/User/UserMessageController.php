<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserMessageController extends Controller
{
    public function store(Request $request, Conversation $conversation, NotificationService $notifications): RedirectResponse
    {
        if ($conversation->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:5000'],
            'attachment' => ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:5120'],
        ]);

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('support', 'public');
        }

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'user',
            'sender_id' => $request->user()->id,
            'body' => $validated['body'],
            'attachment_path' => $path,
        ]);

        $conversation->update([
            'status' => 'pending',
            'last_message_at' => now(),
            'last_message_by' => 'user',
            'closed_at' => null,
        ]);

        if ($conversation->assigned_admin_id) {
            $notifications->notifyAdmin(
                $conversation->assigned_admin_id,
                'support_message',
                'New support reply',
                'A user replied to a support ticket.',
                'info',
                ['conversation_id' => $conversation->id]
            );
        } else {
            $notifications->notifyAdminsByRoleOrPermission(
                'support.manage',
                'support_message',
                'New support reply',
                'A user replied to a support ticket.',
                'info',
                ['conversation_id' => $conversation->id]
            );
        }

        return back()->with('status', 'Message sent.');
    }
}
