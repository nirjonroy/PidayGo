@extends('layouts.app')

@section('content')
    <h1>{{ $conversation->subject }}</h1>
    <p class="muted">Status: {{ ucfirst($conversation->status) }} | Priority: {{ ucfirst($conversation->priority) }}</p>

    <div style="margin:16px 0;">
        @foreach ($conversation->messages as $message)
            <div style="margin-bottom:12px; padding:10px; border-radius:8px; background: {{ $message->sender_type === 'user' ? '#eef2ff' : '#f3f4f6' }};">
                <div class="muted" style="font-size:12px;">
                    {{ $message->sender_type === 'user' ? 'You' : 'Admin' }} • {{ $message->created_at }}
                </div>
                <div>{{ $message->body }}</div>
                @if ($message->attachment_path)
                    <div style="margin-top:6px;">
                        <a href="{{ asset('storage/' . $message->attachment_path) }}" target="_blank">View attachment</a>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    @if ($conversation->status !== 'closed')
        <form method="POST" action="{{ route('support.message.store', $conversation) }}" enctype="multipart/form-data">
            @csrf
            <label for="body">Reply</label>
            <textarea id="body" name="body" rows="4" required></textarea>
            @error('body') <div class="error">{{ $message }}</div> @enderror

            <label for="attachment">Attachment (optional)</label>
            <input id="attachment" name="attachment" type="file" accept=".png,.jpg,.jpeg,.pdf">
            @error('attachment') <div class="error">{{ $message }}</div> @enderror

            <button type="submit">Send Reply</button>
        </form>

        <form method="POST" action="{{ route('support.close', $conversation) }}" style="margin-top:12px;">
            @csrf
            <button type="submit">Close Ticket</button>
        </form>
    @else
        <div class="muted">This conversation is closed.</div>
    @endif
@endsection
