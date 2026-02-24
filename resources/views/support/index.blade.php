@extends('layouts.frontend')

@section('content')
@include('frontend.partials.page-banner', ['title' => 'Support'])

<section aria-label="section">
    <div class="container">
        <div class="row mb-4">
            <div class="col-lg-12 text-end">
                <a href="{{ route('support.create') }}" class="btn-main">Create New Ticket</a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table table-borderless table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Last Message</th>
                                <th>Unread</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($conversations as $conversation)
                                <tr>
                                    <td>
                                        <a href="{{ route('support.show', $conversation) }}">{{ $conversation->subject }}</a>
                                    </td>
                                    <td>{{ ucfirst($conversation->status) }}</td>
                                    <td>{{ ucfirst($conversation->priority) }}</td>
                                    <td>{{ $conversation->last_message_at }}</td>
                                    <td>{{ $conversation->unread_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No tickets yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($conversations->hasPages())
                    <div class="mt-3">
                        {{ $conversations->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
