@extends('layouts.admin-panel')

@section('content')
    @section('page-title', 'Activity Logs')

    <div class="card">
        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Actor</th>
                        <th>Subject</th>
                        <th>IP</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        @php
                            $actor = $log->actor;
                            $actorLabel = $actor ? ($actor->name ?? 'Admin').' ('.($actor->email ?? 'n/a').')' : 'System';
                            $subjectLabel = $log->subject_type ? class_basename($log->subject_type).' #'.$log->subject_id : '-';
                        @endphp
                        <tr>
                            <td>{{ $log->action }}</td>
                            <td>{{ $actorLabel }}</td>
                            <td>{{ $subjectLabel }}</td>
                            <td>{{ $log->ip_address ?? '-' }}</td>
                            <td>{{ $log->created_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $logs->links() }}
        </div>
    </div>
@endsection
