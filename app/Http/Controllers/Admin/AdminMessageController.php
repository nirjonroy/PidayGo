<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminMessageController extends Controller
{
    public function store(Request $request, Conversation $conversation, NotificationService $notifications): RedirectResponse
    {
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
            'sender_type' => 'admin',
            'sender_id' => $request->user('admin')->id,
            'body' => $validated['body'],
            'attachment_path' => $path,
        ]);

        $conversation->update([
            'status' => 'answered',
            'last_message_at' => now(),
            'last_message_by' => 'admin',
        ]);

        $notifications->notifyUser(
            $conversation->user_id,
            'support_reply',
            'Support replied',
            'You have a new reply to your support ticket.',
            'success',
            ['conversation_id' => $conversation->id],
            true
        );

        return back()->with('status', 'Reply sent.');
    }
}
