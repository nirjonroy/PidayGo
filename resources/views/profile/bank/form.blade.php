@extends('layouts.frontend')

@section('content')
@include('frontend.partials.page-banner', ['title' => $account->exists ? 'Edit Crypto Wallet' : 'Add Crypto Wallet'])

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
                        <label class="form-label">Network</label>
                        <select name="network" class="form-control" required>
                            @php
                                $networkOptions = ['BEP20', 'TRC20', 'ERC20', 'Solana', 'Polygon'];
                                $selectedNetwork = old('network', $account->network);
                            @endphp
                            <option value="" disabled {{ $selectedNetwork ? '' : 'selected' }}>Select network</option>
                            @foreach ($networkOptions as $option)
                                <option value="{{ $option }}" {{ $selectedNetwork === $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                        @error('network')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Wallet Address</label>
                        <input name="wallet_address" class="form-control" value="{{ old('wallet_address', $account->wallet_address) }}" required>
                        @error('wallet_address')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Address Label</label>
                            <input name="address_label" class="form-control" value="{{ old('address_label', $account->address_label) }}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Memo/Tag (optional)</label>
                            <input name="memo_tag" class="form-control" value="{{ old('memo_tag', $account->memo_tag) }}">
                        </div>
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
