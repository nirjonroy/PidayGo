@extends('layouts.admin-panel')

@section('page-title', 'Payment Settings')

@section('content')
  <div class="card">
    <div class="card-body">
      @if ($errors->any())
        <div class="alert alert-danger">
          {{ $errors->first() }}
        </div>
      @endif

      <form method="POST" action="{{ route('admin.payment-settings.update') }}">
        @csrf

        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $settings->is_active))>
          <label class="form-check-label" for="is_active">OxaPay Active</label>
        </div>

        <div class="mb-3">
          <label class="form-label" for="gateway_name">Gateway Name</label>
          <input id="gateway_name" class="form-control" value="{{ $settings->gateway_name ?? 'oxapay' }}" readonly>
          <div class="form-text">This page loads the active row into config('oxapay.*').</div>
        </div>

        <div class="row">
          <div class="col-lg-6">
            <div class="mb-3">
              <label class="form-label" for="api_key">API Key</label>
              <input id="api_key" name="api_key" type="password" class="form-control" autocomplete="new-password" placeholder="{{ $hasApiKey ? 'Saved - leave blank to keep current key' : 'Enter API key' }}">
              @error('api_key') <div class="text-danger">{{ $message }}</div> @enderror
              @if ($hasApiKey)
                <div class="form-text">An API key is saved and encrypted.</div>
              @endif
            </div>
          </div>

          <div class="col-lg-6">
            <div class="mb-3">
              <label class="form-label" for="secret_key">Secret Key</label>
              <input id="secret_key" name="secret_key" type="password" class="form-control" autocomplete="new-password" placeholder="{{ $hasSecretKey ? 'Saved - leave blank to keep current key' : 'Enter secret key' }}">
              @error('secret_key') <div class="text-danger">{{ $message }}</div> @enderror
              @if ($hasSecretKey)
                <div class="form-text">A secret key is saved and encrypted.</div>
              @endif
            </div>
          </div>
        </div>

        <button class="btn btn-success" type="submit">Save Settings</button>
      </form>
    </div>
  </div>
@endsection
