@extends('layouts.admin-panel')

@section('content')
    @section('page-title', 'KYC Requests')

    @if ($pending->isEmpty())
        <p class="text-secondary">No pending requests.</p>
    @else
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Submitted</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pending as $request)
                    <tr>
                        <td>{{ $request->user->name }}</td>
                        <td>{{ $request->user->email }}</td>
                        <td>{{ $request->submitted_at }}</td>
                        <td><a href="{{ route('admin.kyc.show', $request) }}">Review</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $pending->links() }}
    @endif
@endsection
