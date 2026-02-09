@extends('layouts.admin-panel')

@section('content')
    @section('page-title', 'Staking Plans')

    <a href="{{ route('admin.staking-plans.create') }}" class="btn btn-primary mb-3">Create Plan</a>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Daily Rate</th>
                        <th>Duration</th>
                        <th>Min</th>
                        <th>Max</th>
                        <th>Max Multiplier</th>
                        <th>Level</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($plans as $plan)
                        <tr>
                            <td>{{ $plan->name }}</td>
                            <td>{{ $plan->daily_rate }}</td>
                            <td>{{ $plan->duration_days }}</td>
                            <td>{{ $plan->min_amount }}</td>
                            <td>{{ $plan->max_amount ?? '-' }}</td>
                            <td>{{ $plan->max_payout_multiplier }}</td>
                            <td>{{ $plan->level_required ?? '-' }}</td>
                            <td>{{ $plan->is_active ? 'Active' : 'Inactive' }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.staking-plans.edit', $plan) }}" class="btn btn-sm btn-secondary">Edit</a>
                                <form method="POST" action="{{ route('admin.staking-plans.delete', $plan) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this plan?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
