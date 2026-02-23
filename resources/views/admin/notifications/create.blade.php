@extends('layouts.admin-panel')

@section('page-title', 'Create Notification')

@section('content')
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.notifications.store') }}">
        @csrf

        <div class="mb-3">
          <label class="form-label">Audience</label>
          <select name="audience" class="form-select" required>
            <option value="user" selected>Users</option>
          </select>
          @error('audience') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Recipients</label>
          <select name="recipients_mode" id="recipients_mode" class="form-select" required>
            <option value="all">All Users</option>
            <option value="selected">Selected Users</option>
          </select>
          @error('recipients_mode') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3" id="recipients_box" style="display:none;">
          <label class="form-label">Select Users</label>
          <select name="recipients[]" class="form-select" multiple size="6">
            @foreach ($users as $user)
              <option value="{{ $user->id }}">{{ $user->email }}</option>
            @endforeach
          </select>
          @error('recipients') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Title</label>
          <input name="title" class="form-control" value="{{ old('title') }}" required>
          @error('title') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Message</label>
          <textarea name="message" class="form-control" rows="4" required>{{ old('message') }}</textarea>
          @error('message') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Level</label>
          <select name="level" class="form-select">
            @foreach (['info', 'success', 'warning', 'error'] as $level)
              <option value="{{ $level }}" @selected(old('level', 'info') === $level)>{{ ucfirst($level) }}</option>
            @endforeach
          </select>
          @error('level') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" name="is_popup" id="is_popup" value="1" @checked(old('is_popup'))>
          <label class="form-check-label" for="is_popup">Show as popup on login</label>
        </div>

        <div class="mb-3">
          <label class="form-label">Expires At (optional)</label>
          <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at') }}">
          @error('expires_at') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <button class="btn btn-success" type="submit">Send Notification</button>
        <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>

  <script>
    const modeSelect = document.getElementById('recipients_mode');
    const recipientsBox = document.getElementById('recipients_box');
    const toggleRecipients = () => {
      recipientsBox.style.display = modeSelect.value === 'selected' ? 'block' : 'none';
    };
    modeSelect.addEventListener('change', toggleRecipients);
    toggleRecipients();
  </script>
@endsection
