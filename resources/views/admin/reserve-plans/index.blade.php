@extends('layouts.admin-panel')

@section('content')
    @section('page-title', 'Reserve Plans')

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('admin.reserve-plans.create') }}">Create Reserve Plan</a>
    </div>

    <div class="card">
        <div class="card-body">
            @if ($plans->isEmpty())
                <p class="text-muted">No reserve plans configured.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Level</th>
                                <th>Wallet Range</th>
                                <th>Reserve %</th>
                                <th>Profit % Range</th>
                                <th>Max Sells</th>
                                <th>Max/Day</th>
                                <th>Unlock Policy</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($plans as $plan)
                                <tr>
                                    <td>{{ $plan->level?->code ?? 'N/A' }}</td>
                                    <td>{{ number_format((float) ($plan->wallet_balance_min ?? 0), 4) }} - {{ number_format((float) ($plan->wallet_balance_max ?? 0), 4) }}</td>
                                    <td>{{ number_format((float) $plan->reserve_amount, 3) }}%</td>
                                    <td>{{ $plan->profit_min_percent }}% - {{ $plan->profit_max_percent }}%</td>
                                    <td>{{ $plan->max_sells ?? 'Unlimited' }}</td>
                                    <td>{{ $plan->max_sells_per_day ?? 'Unlimited' }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $plan->unlock_policy ?? 'never')) }}</td>
                                    <td>
                                        @if ($plan->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.reserve-plans.edit', $plan) }}">Edit</a>
                                        <form method="POST" action="{{ route('admin.reserve-plans.toggle', $plan) }}" style="display:inline;">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-secondary" type="submit">
                                                {{ $plan->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.reserve-plans.delete', $plan) }}" style="display:inline;">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Delete this reserve plan?')">Delete</button>
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
@endsection
