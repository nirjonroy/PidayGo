@extends('layouts.frontend')

@section('content')
@include('frontend.partials.page-banner', ['title' => 'Notifications'])

<section aria-label="section">
    <div class="container">
        <div class="row">
            @forelse ($items as $item)
                <div class="col-lg-12 mb20">
                    <div class="nft__item s2">
                        <div class="nft__item_info">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h4 class="mb-1">{{ $item->notification->title }}</h4>
                                    <p class="text-muted mb-2">{{ $item->notification->message }}</p>
                                    <div class="small text-muted">{{ $item->created_at }}</div>
                                </div>
                                <span class="badge {{ $item->read_at ? 'bg-secondary' : 'bg-success' }}">
                                    {{ $item->read_at ? 'Read' : 'Unread' }}
                                </span>
                            </div>
                            <div class="mt-3">
                                @if (!$item->read_at)
                                    <form method="POST" action="{{ route('notifications.read', $item->notification) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn-main btn-light btn-sm">Mark Read</button>
                                    </form>
                                @endif
                                @if (!$item->dismissed_at)
                                    <form method="POST" action="{{ route('notifications.dismiss', $item->notification) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn-main btn-sm">Dismiss</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-md-12 text-center text-muted">No notifications.</div>
            @endforelse
        </div>

        @if ($items->hasPages())
            <div class="mt-3">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
