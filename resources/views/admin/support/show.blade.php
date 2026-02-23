@extends('layouts.admin-panel')

@section('page-title', 'Support Ticket')

@section('content')
  <div class="row">
    <div class="col-lg-8">
      <div class="card mb-3">
        <div class="card-body">
          <h5>{{ $conversation->subject }}</h5>
          <p class="text-muted">Status: {{ ucfirst($conversation->status) }} | Priority: {{ ucfirst($conversation->priority) }}</p>
          <div>
            @foreach ($conversation->messages as $message)
              <div class="mb-3 p-2 rounded {{ $message->sender_type === 'admin' ? 'bg-light' : 'bg-white' }}" style="border:1px solid #e5e7eb;">
                <div class="small text-muted">
                  {{ $message->sender_type === 'admin' ? 'Admin' : 'User' }} • {{ $message->created_at }}
                </div>
                <div>{{ $message->body }}</div>
                @if ($message->attachment_path)
                  <div class="mt-2">
                    <a href="{{ asset('storage/' . $message->attachment_path) }}" target="_blank">View attachment</a>
                  </div>
                @endif
              </div>
            @endforeach
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <form method="POST" action="{{ route('admin.support.message.store', $conversation) }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
              <label class="form-label">Reply</label>
              <textarea name="body" class="form-control" rows="4" required></textarea>
              @error('body') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
              <label class="form-label">Attachment (optional)</label>
              <input type="file" name="attachment" class="form-control" accept=".png,.jpg,.jpeg,.pdf">
              @error('attachment') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
            <button class="btn btn-primary" type="submit">Send Reply</button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card mb-3">
        <div class="card-body">
          <h6>User</h6>
          <div>{{ $conversation->user->name ?? '-' }}</div>
          <div class="text-muted">{{ $conversation->user->email ?? '-' }}</div>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-body">
          <form method="POST" action="{{ route('admin.support.status', $conversation) }}">
            @csrf
            <label class="form-label">Status</label>
            <select name="status" class="form-select mb-2">
              @foreach (['open', 'pending', 'answered', 'closed'] as $status)
                <option value="{{ $status }}" @selected($conversation->status === $status)>{{ ucfirst($status) }}</option>
              @endforeach
            </select>
            <button class="btn btn-outline-primary btn-sm" type="submit">Update Status</button>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <form method="POST" action="{{ route('admin.support.assign', $conversation) }}">
            @csrf
            <label class="form-label">Assign Admin</label>
            <select name="admin_id" class="form-select mb-2">
              <option value="">Unassigned</option>
              @foreach ($admins as $admin)
                <option value="{{ $admin->id }}" @selected($conversation->assigned_admin_id === $admin->id)>{{ $admin->name }}</option>
              @endforeach
            </select>
            <button class="btn btn-outline-secondary btn-sm" type="submit">Assign</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
