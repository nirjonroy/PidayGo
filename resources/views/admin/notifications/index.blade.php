@extends('layouts.admin-panel')

@section('page-title', 'Notifications')

@section('content')
  <div class="mb-3">
    <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary">Create Notification</a>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>Title</th>
              <th>Level</th>
              <th>Audience</th>
              <th>Recipients</th>
              <th>Created</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($notifications as $notification)
              <tr>
                <td>{{ $notification->title }}</td>
                <td>{{ ucfirst($notification->level) }}</td>
                <td>{{ ucfirst($notification->audience) }}</td>
                <td>{{ $notification->user_recipients_count }}</td>
                <td>{{ $notification->created_at }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted">No notifications sent.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if ($notifications->hasPages())
      <div class="card-footer">
        {{ $notifications->links() }}
      </div>
    @endif
  </div>
@endsection
