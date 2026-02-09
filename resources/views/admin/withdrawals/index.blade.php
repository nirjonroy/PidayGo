@extends('layouts.admin-panel')

@section('content')
    @section('page-title', 'Withdrawals')

    <div class="mb-3">
        <a class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.withdrawals.index', ['status' => 'all']) }}">All</a>
        <a class="btn btn-sm {{ $status === 'pending' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.withdrawals.index', ['status' => 'pending']) }}">Pending</a>
        <a class="btn btn-sm {{ $status === 'approved' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.withdrawals.index', ['status' => 'approved']) }}">Approved</a>
        <a class="btn btn-sm {{ $status === 'rejected' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.withdrawals.index', ['status' => 'rejected']) }}">Rejected</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Requested</th>
                        <th>Eligible At</th>
                        <th>Reviewed</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $request)
                        @php
                            $eligible = $request->eligible_at && now()->gte($request->eligible_at);
                            $overdue = $request->eligible_at && now()->gt($request->eligible_at->copy()->addHours(24));
                        @endphp
                        <tr>
                            <td>{{ $request->user->email }}</td>
                            <td>{{ $request->amount }}</td>
                            <td>{{ ucfirst($request->status) }}</td>
                            <td>{{ $request->requested_at }}</td>
                            <td>
                                {{ $request->eligible_at }}
                                @if ($overdue)
                                    <span class="text-danger">(overdue)</span>
                                @endif
                            </td>
                            <td>{{ $request->reviewed_at ?? '-' }}</td>
                            <td class="text-end">
                                @if ($request->status === 'pending')
                                    <form method="POST" action="{{ route('admin.withdrawals.approve', $request) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" {{ $eligible ? '' : 'disabled' }}>Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.withdrawals.reject', $request) }}" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="notes" value="Rejected by admin">
                                        <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $requests->links() }}
        </div>
    </div>
@endsection
