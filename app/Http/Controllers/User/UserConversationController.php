<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserConversationController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        $conversations = Conversation::query()
            ->where('user_id', $userId)
            ->withCount([
                'messages as unread_count' => function ($query) {
                    $query->where('sender_type', 'admin')->whereNull('read_at');
                },
            ])
            ->orderByDesc('last_message_at')
            ->paginate(15);

        return view('support.index', [
            'conversations' => $conversations,
        ]);
    }

    public function create(): View
    {
        return view('support.create');
    }

    public function store(Request $request, NotificationService $notifications): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'min:5', 'max:150'],
            'priority' => ['required', 'in:low,normal,high'],
            'body' => ['required', 'string', 'min:5', 'max:5000'],
            'attachment' => ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:5120'],
        ]);

        $conversation = Conversation::create([
            'user_id' => $request->user()->id,
            'subject' => $validated['subject'],
            'priority' => $validated['priority'],
            'status' => 'open',
            'last_message_at' => now(),
            'last_message_by' => 'user',
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

        $notifications->notifyAdminsByRoleOrPermission(
            'support.manage',
            'support_message',
            'New support ticket',
            'A new support ticket was created.',
            'warning',
            ['conversation_id' => $conversation->id]
        );

        return redirect()->route('support.show', $conversation)->with('status', 'Support ticket created.');
    }

    public function show(Request $request, Conversation $conversation): View
    {
        if ($conversation->user_id !== $request->user()->id) {
            abort(403);
        }

        Message::where('conversation_id', $conversation->id)
            ->where('sender_type', 'admin')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('support.show', [
            'conversation' => $conversation->load('messages', 'assignedAdmin'),
        ]);
    }

    public function close(Request $request, Conversation $conversation): RedirectResponse
    {
        if ($conversation->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($conversation->status !== 'closed') {
            $conversation->update([
                'status' => 'closed',
                'closed_at' => now(),
            ]);
        }

        return back()->with('status', 'Conversation closed.');
    }
}
