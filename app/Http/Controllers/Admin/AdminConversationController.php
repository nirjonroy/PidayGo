<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminConversationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->get('status', 'open');
        $search = $request->get('q');

        $query = Conversation::query()
            ->with('user')
            ->withCount([
                'messages as unread_count' => function ($q) {
                    $q->where('sender_type', 'user')->whereNull('read_at');
                },
            ])
            ->orderByDesc('last_message_at');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('email', 'like', '%' . $search . '%')
                            ->orWhere('name', 'like', '%' . $search . '%');
                    });
            });
        }

        return view('admin.support.index', [
            'conversations' => $query->paginate(20)->withQueryString(),
            'status' => $status,
            'search' => $search,
        ]);
    }

    public function show(Request $request, Conversation $conversation): View
    {
        Message::where('conversation_id', $conversation->id)
            ->where('sender_type', 'user')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('admin.support.show', [
            'conversation' => $conversation->load('messages', 'user', 'assignedAdmin'),
            'admins' => Admin::orderBy('name')->get(),
        ]);
    }

    public function assign(Request $request, Conversation $conversation): RedirectResponse
    {
        $validated = $request->validate([
            'admin_id' => ['nullable', 'exists:admins,id'],
        ]);

        $conversation->update([
            'assigned_admin_id' => $validated['admin_id'] ?? null,
        ]);

        return back()->with('status', 'Conversation assigned.');
    }

    public function status(Request $request, Conversation $conversation): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:open,pending,answered,closed'],
        ]);

        $data = ['status' => $validated['status']];

        if ($validated['status'] === 'closed') {
            $data['closed_at'] = now();
        } else {
            $data['closed_at'] = null;
        }

        $conversation->update($data);

        return back()->with('status', 'Status updated.');
    }
}
