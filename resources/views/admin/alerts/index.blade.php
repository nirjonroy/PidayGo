@extends('layouts.admin-panel')

@section('page-title', 'Notifications')

@section('content')
  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
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
            @forelse ($items as $item)
              <tr>
                <td>{{ $item->read_at ? 'Read' : 'Unread' }}</td>
                <td>{{ $item->notification->title }}</td>
                <td>{{ $item->notification->message }}</td>
                <td>{{ $item->created_at }}</td>
                <td>
                  @if (!$item->read_at)
                    <form method="POST" action="{{ route('admin.alerts.read', $item->notification) }}" style="display:inline;">
                      @csrf
                      <button class="btn btn-sm btn-outline-primary" type="submit">Mark Read</button>
                    </form>
                  @endif
                  @if (!$item->dismissed_at)
                    <form method="POST" action="{{ route('admin.alerts.dismiss', $item->notification) }}" style="display:inline;">
                      @csrf
                      <button class="btn btn-sm btn-outline-secondary" type="submit">Dismiss</button>
                    </form>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted">No notifications.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if ($items->hasPages())
      <div class="card-footer">
        {{ $items->links() }}
      </div>
    @endif
  </div>
@endsection
