@extends('layouts.app')

@section('content')
    <h1>Bank Accounts</h1>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('profile.bank.create') }}">Add Bank Account</a>
        <a class="btn btn-outline-secondary" href="{{ route('profile.edit') }}">Back to Profile</a>
    </div>

    @if ($accounts->isEmpty())
        <p class="text-muted">No bank accounts added yet.</p>
    @else
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Bank</th>
                        <th>Account Name</th>
                        <th>Account Number</th>
                        <th>Currency</th>
                        <th>Default</th>
                        <th style="width:180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($accounts as $account)
                        <tr>
                            <td>{{ $account->bank_name }}</td>
                            <td>{{ $account->account_name }}</td>
                            <td>{{ $account->account_number }}</td>
                            <td>{{ $account->currency ?? '-' }}</td>
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
@endsection
