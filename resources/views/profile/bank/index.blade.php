@extends('layouts.frontend')

@section('content')
@include('frontend.partials.page-banner', ['title' => 'Crypto Wallets'])

<section aria-label="section">
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div class="d-flex gap-2 mb-3">
            <a class="btn-main" href="{{ route('profile.bank.create') }}">Add Crypto Wallet</a>
            <a class="btn-main btn-light" href="{{ route('profile.edit') }}">Back to Profile</a>
        </div>

        <div class="nft__item s2">
            <div class="nft__item_info">
                @if ($accounts->isEmpty())
                    <p class="text-muted">No bank accounts added yet.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-borderless table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Network</th>
                                    <th>Wallet Address</th>
                                    <th>Label</th>
                                    <th>Memo/Tag</th>
                                    <th>Default</th>
                                    <th style="width:220px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($accounts as $account)
                                    <tr>
                                        <td>{{ $account->network ?? '-' }}</td>
                                        <td>
                                            @php
                                                $address = $account->wallet_address;
                                                $masked = $address ? substr($address, 0, 6) . '...' . substr($address, -4) : '-';
                                            @endphp
                                            {{ $masked }}
                                        </td>
                                        <td>{{ $account->address_label ?? '-' }}</td>
                                        <td>{{ $account->memo_tag ?? '-' }}</td>
                                        <td>
                                            @if ($account->is_default)
                                                <span class="badge bg-success">Default</span>
                                            @else
                                                <span class="badge bg-secondary">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('profile.bank.edit', $account) }}">Edit</a>
                                            <form method="POST" action="{{ route('profile.bank.default', $account) }}" style="display:inline;">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-success" type="submit">Set Default</button>
                                            </form>
                                            <form method="POST" action="{{ route('profile.bank.delete', $account) }}" style="display:inline;" onsubmit="return confirm('Delete this bank account?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
