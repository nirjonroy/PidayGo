@extends('layouts.admin-panel')

@section('content')
    @section('page-title', 'KYC Requests')

    <div class="mb-3">
        <a class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.kyc.index', ['status' => 'all']) }}">All</a>
        <a class="btn btn-sm {{ $status === 'pending' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.kyc.index', ['status' => 'pending']) }}">Pending ({{ $counts['pending'] }})</a>
        <a class="btn btn-sm {{ $status === 'approved' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.kyc.index', ['status' => 'approved']) }}">Approved ({{ $counts['approved'] }})</a>
        <a class="btn btn-sm {{ $status === 'rejected' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.kyc.index', ['status' => 'rejected']) }}">Rejected ({{ $counts['rejected'] }})</a>
    </div>

    @if ($requests->isEmpty())
        <p class="text-secondary">No KYC requests found.</p>
    @else
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Reviewed</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requests as $request)
                    <tr>
                        <td>{{ $request->user->name }}</td>
                        <td>{{ $request->user->email }}</td>
                        <td>{{ ucfirst($request->status) }}</td>
                        <td>{{ $request->submitted_at }}</td>
                        <td>{{ $request->reviewed_at ?? '-' }}</td>
                        <td><a href="{{ route('admin.kyc.show', $request) }}">Details</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $requests->links() }}
    @endif
@endsection
