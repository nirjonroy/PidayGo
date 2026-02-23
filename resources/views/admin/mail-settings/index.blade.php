@extends('layouts.admin-panel')

@section('page-title', 'Mail Settings')

@section('content')
  <div class="card mb-3">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.mail-settings.update') }}">
        @csrf

        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $settings->is_active))>
          <label class="form-check-label" for="is_active">Email Active</label>
        </div>

        <div class="row">
          <div class="col-lg-6">
            <h5>Primary SMTP</h5>
            <div class="mb-2">
              <label class="form-label">Host</label>
              <input name="primary_host" class="form-control" value="{{ old('primary_host', $settings->primary_host) }}">
            </div>
            <div class="mb-2">
              <label class="form-label">Port</label>
              <input name="primary_port" type="number" class="form-control" value="{{ old('primary_port', $settings->primary_port) }}">
            </div>
            <div class="mb-2">
              <label class="form-label">Username</label>
              <input name="primary_username" class="form-control" value="{{ old('primary_username', $settings->primary_username) }}">
            </div>
            <div class="mb-2">
              <label class="form-label">Password</label>
              <input name="primary_password" type="password" class="form-control">
            </div>
            <div class="mb-2">
              <label class="form-label">Encryption</label>
              <input name="primary_encryption" class="form-control" value="{{ old('primary_encryption', $settings->primary_encryption) }}">
            </div>
            <div class="mb-2">
              <label class="form-label">From Address</label>
              <input name="primary_from_address" class="form-control" value="{{ old('primary_from_address', $settings->primary_from_address) }}">
            </div>
            <div class="mb-2">
              <label class="form-label">From Name</label>
              <input name="primary_from_name" class="form-control" value="{{ old('primary_from_name', $settings->primary_from_name) }}">
            </div>
          </div>

          <div class="col-lg-6">
            <h5>Secondary SMTP</h5>
            <div class="mb-2">
              <label class="form-label">Host</label>
              <input name="secondary_host" class="form-control" value="{{ old('secondary_host', $settings->secondary_host) }}">
            </div>
            <div class="mb-2">
              <label class="form-label">Port</label>
              <input name="secondary_port" type="number" class="form-control" value="{{ old('secondary_port', $settings->secondary_port) }}">
            </div>
            <div class="mb-2">
              <label class="form-label">Username</label>
              <input name="secondary_username" class="form-control" value="{{ old('secondary_username', $settings->secondary_username) }}">
            </div>
            <div class="mb-2">
              <label class="form-label">Password</label>
              <input name="secondary_password" type="password" class="form-control">
            </div>
            <div class="mb-2">
              <label class="form-label">Encryption</label>
              <input name="secondary_encryption" class="form-control" value="{{ old('secondary_encryption', $settings->secondary_encryption) }}">
            </div>
            <div class="mb-2">
              <label class="form-label">From Address</label>
              <input name="secondary_from_address" class="form-control" value="{{ old('secondary_from_address', $settings->secondary_from_address) }}">
            </div>
            <div class="mb-2">
              <label class="form-label">From Name</label>
              <input name="secondary_from_name" class="form-control" value="{{ old('secondary_from_name', $settings->secondary_from_name) }}">
            </div>
          </div>
        </div>

        <hr>

        <div class="row">
          <div class="col-md-6 mb-2">
            <label class="form-label">Verification Mailer</label>
            <select name="verification_mailer" class="form-select">
              <option value="primary" @selected(old('verification_mailer', $settings->verification_mailer ?? 'primary') === 'primary')>Primary</option>
              <option value="secondary" @selected(old('verification_mailer', $settings->verification_mailer ?? 'primary') === 'secondary')>Secondary</option>
            </select>
          </div>
          <div class="col-md-6 mb-2">
            <label class="form-label">Notification Mailer</label>
            <select name="notification_mailer" class="form-select">
              <option value="primary" @selected(old('notification_mailer', $settings->notification_mailer ?? 'primary') === 'primary')>Primary</option>
              <option value="secondary" @selected(old('notification_mailer', $settings->notification_mailer ?? 'primary') === 'secondary')>Secondary</option>
            </select>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Admin Notify Emails (comma separated)</label>
          <textarea name="admin_notify_emails" class="form-control" rows="2">{{ old('admin_notify_emails', $settings->admin_notify_emails) }}</textarea>
        </div>

        <button class="btn btn-success" type="submit">Save Settings</button>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <h5>Send Test Email</h5>
      <form method="POST" action="{{ route('admin.mail-settings.test') }}">
        @csrf
        <div class="row g-2 align-items-end">
          <div class="col-md-4">
            <label class="form-label">Profile</label>
            <select name="profile" class="form-select">
              <option value="primary">Primary</option>
              <option value="secondary">Secondary</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="col-md-2">
            <button class="btn btn-outline-primary" type="submit">Send</button>
          </div>
        </div>
      </form>
    </div>
  </div>
@endsection
