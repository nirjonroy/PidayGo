@extends('layouts.app')

@section('content')
    <h1>Notifications</h1>

    @if ($items->isEmpty())
        <p class="muted">No notifications.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item->read_at ? 'Read' : 'Unread' }}</td>
                        <td>{{ $item->notification->title }}</td>
                        <td>{{ $item->notification->message }}</td>
                        <td>{{ $item->created_at }}</td>
                        <td>
                            @if (!$item->read_at)
                                <form method="POST" action="{{ route('notifications.read', $item->notification) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit">Mark Read</button>
                                </form>
                            @endif
                            @if (!$item->dismissed_at)
                                <form method="POST" action="{{ route('notifications.dismiss', $item->notification) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit">Dismiss</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($items->hasPages())
            <div style="margin-top:16px;">
                {{ $items->links() }}
            </div>
        @endif
    @endif
@endsection
