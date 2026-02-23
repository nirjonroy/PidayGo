@extends('layouts.app')

@section('content')
    <h1>Support</h1>

    <div style="margin-bottom:12px;">
        <a href="{{ route('support.create') }}">Create New Ticket</a>
    </div>

    @if ($conversations->isEmpty())
        <p class="muted">No tickets yet.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Last Message</th>
                    <th>Unread</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($conversations as $conversation)
                    <tr>
                        <td>
                            <a href="{{ route('support.show', $conversation) }}">{{ $conversation->subject }}</a>
                        </td>
                        <td>{{ ucfirst($conversation->status) }}</td>
                        <td>{{ ucfirst($conversation->priority) }}</td>
                        <td>{{ $conversation->last_message_at }}</td>
                        <td>{{ $conversation->unread_count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($conversations->hasPages())
            <div style="margin-top:16px;">
                {{ $conversations->links() }}
            </div>
        @endif
    @endif
@endsection
