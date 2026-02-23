@extends('layouts.admin-panel')

@section('page-title', 'Support Inbox')

@section('content')
  <div class="card mb-3">
    <div class="card-body">
      <form method="GET" class="row g-2 align-items-end">
        <div class="col-auto">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            @foreach (['open', 'pending', 'answered', 'closed', 'all'] as $option)
              <option value="{{ $option }}" @selected($status === $option)>{{ ucfirst($option) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-auto">
          <label class="form-label">Search</label>
          <input type="text" name="q" class="form-control" value="{{ $search }}">
        </div>
        <div class="col-auto">
          <button class="btn btn-primary" type="submit">Filter</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>Subject</th>
              <th>User</th>
              <th>Status</th>
              <th>Priority</th>
              <th>Last Message</th>
              <th>Unread</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @forelse ($conversations as $conversation)
              <tr>
                <td>{{ $conversation->subject }}</td>
                <td>{{ $conversation->user->email ?? '-' }}</td>
                <td>{{ ucfirst($conversation->status) }}</td>
                <td>{{ ucfirst($conversation->priority) }}</td>
                <td>{{ $conversation->last_message_at }}</td>
                <td>{{ $conversation->unread_count }}</td>
                <td>
                  <a href="{{ route('admin.support.show', $conversation) }}" class="btn btn-sm btn-outline-primary">Open</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted">No tickets found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if ($conversations->hasPages())
      <div class="card-footer">
        {{ $conversations->links() }}
      </div>
    @endif
  </div>
@endsection
