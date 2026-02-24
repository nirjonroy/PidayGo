@extends('layouts.frontend')

@section('content')
<section id="subheader" class="text-light" data-bgimage="url({{ asset('frontend/images/background/subheader.jpg') }}) top">
    <div class="center-y relative text-center">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h1>{{ $account->exists ? 'Edit Bank Account' : 'Add Bank Account' }}</h1>
                </div>
            </div>
        </div>
    </div>
</section>

<section aria-label="section">
    <div class="container">
        <div class="nft__item s2">
            <div class="nft__item_info">
                <form method="POST" action="{{ $account->exists ? route('profile.bank.update', $account) : route('profile.bank.store') }}">
                    @csrf
                    @if ($account->exists)
                        @method('PUT')
                    @endif

                    <div class="mb-2">
                        <label class="form-label">Bank Name</label>
                        <input name="bank_name" class="form-control" value="{{ old('bank_name', $account->bank_name) }}" required>
                        @error('bank_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Account Name</label>
                        <input name="account_name" class="form-control" value="{{ old('account_name', $account->account_name) }}" required>
                        @error('account_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Account Number</label>
                        <input name="account_number" class="form-control" value="{{ old('account_number', $account->account_number) }}" {{ $account->exists ? '' : 'required' }}>
                        <small class="text-muted">{{ $account->exists ? 'Leave blank to keep existing number.' : '' }}</small>
                        @error('account_number')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Branch</label>
                            <input name="branch" class="form-control" value="{{ old('branch', $account->branch) }}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Routing Number</label>
                            <input name="routing_number" class="form-control" value="{{ old('routing_number', $account->routing_number) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">SWIFT Code</label>
                            <input name="swift_code" class="form-control" value="{{ old('swift_code', $account->swift_code) }}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">IFSC Code</label>
                            <input name="ifsc_code" class="form-control" value="{{ old('ifsc_code', $account->ifsc_code) }}">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Currency</label>
                        <input name="currency" class="form-control" value="{{ old('currency', $account->currency) }}">
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1" @checked(old('is_default', $account->is_default))>
                        <label class="form-check-label" for="is_default">Set as default</label>
                    </div>

                    <button class="btn-main" type="submit">{{ $account->exists ? 'Update' : 'Create' }}</button>
                    <a class="btn-main btn-light" href="{{ route('profile.bank.index') }}">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
