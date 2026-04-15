@extends('layouts.frontend')

@push('styles')
<style>
    .bank-action-group {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }
    .bank-action-group form {
        margin: 0;
    }
    .bank-action-btn {
        min-width: 92px;
        min-height: 34px;
        padding: 8px 14px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        text-decoration: none;
        white-space: nowrap;
        transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        transform: none !important;
    }
    .bank-action-btn:hover,
    .bank-action-btn:focus,
    .bank-action-btn:active {
        transform: none !important;
    }
    .bank-action-btn--edit {
        border: 1px solid #2563eb;
        background: rgba(37, 99, 235, 0.1);
        color: #1d4ed8;
    }
    .bank-action-btn--edit:hover,
    .bank-action-btn--edit:focus {
        background: rgba(37, 99, 235, 0.16);
        color: #1e40af;
    }
    .bank-action-btn--default {
        border: 1px solid #16a34a;
        background: rgba(22, 163, 74, 0.1);
        color: #15803d;
    }
    .bank-action-btn--default:hover,
    .bank-action-btn--default:focus {
        background: rgba(22, 163, 74, 0.16);
        color: #166534;
    }
    .bank-action-btn--delete {
        border: 1px solid #dc2626;
        background: rgba(220, 38, 38, 0.08);
        color: #dc2626;
    }
    .bank-action-btn--delete:hover,
    .bank-action-btn--delete:focus {
        background: rgba(220, 38, 38, 0.14);
        color: #b91c1c;
    }
    @media (max-width: 575.98px) {
        .bank-action-group {
            gap: 6px;
        }
        .bank-action-btn {
            min-width: 84px;
            padding: 7px 12px;
            font-size: 12px;
        }
    }
</style>
@endpush

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
                                            <div class="bank-action-group">
                                            <a class="bank-action-btn bank-action-btn--edit" href="{{ route('profile.bank.edit', $account) }}">Edit</a>
                                            <form method="POST" action="{{ route('profile.bank.default', $account) }}">
                                                @csrf
                                                <button class="bank-action-btn bank-action-btn--default" type="submit">Set Default</button>
                                            </form>
                                            <form method="POST" action="{{ route('profile.bank.delete', $account) }}" onsubmit="return confirm('Delete this bank account?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="bank-action-btn bank-action-btn--delete" type="submit">Delete</button>
                                            </form>
                                            </div>
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
