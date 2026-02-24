@extends('layouts.frontend')

@section('content')
@include('frontend.partials.page-banner', ['title' => $conversation->subject])

<section aria-label="section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 mb20">
                <div class="nft__item s2">
                    <div class="nft__item_info">
                        <div class="text-muted">Status: {{ ucfirst($conversation->status) }} | Priority: {{ ucfirst($conversation->priority) }}</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 mb30">
                @foreach ($conversation->messages as $message)
                    <div class="nft__item s2 mb-3" style="border-left: 4px solid {{ $message->sender_type === 'user' ? '#6b6bf1' : '#9ca3af' }};">
                        <div class="nft__item_info">
                            <div class="small text-muted">
                                {{ $message->sender_type === 'user' ? 'You' : 'Admin' }} &bull; {{ $message->created_at }}
                            </div>
                            <div class="mt-2">{{ $message->body }}</div>
                            @if ($message->attachment_path)
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $message->attachment_path) }}" target="_blank">View attachment</a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($conversation->status !== 'closed')
                <div class="col-lg-12">
                    <form method="POST" action="{{ route('support.message.store', $conversation) }}" enctype="multipart/form-data" class="form-border">
                        @csrf
                        <div class="field-set">
                            <h5>Reply</h5>
                            <textarea id="body" name="body" rows="4" class="form-control" required></textarea>
                            @error('body') <div class="text-danger">{{ $message }}</div> @enderror

                            <div class="spacer-20"></div>

                            <h5>Attachment (optional)</h5>
                            <input id="attachment" name="attachment" type="file" class="form-control" accept=".png,.jpg,.jpeg,.pdf">
                            @error('attachment') <div class="text-danger">{{ $message }}</div> @enderror

                            <div class="spacer-20"></div>

                            <button type="submit" class="btn-main">Send Reply</button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('support.close', $conversation) }}" class="mt-3">
                        @csrf
                        <button type="submit" class="btn-main btn-light">Close Ticket</button>
                    </form>
                </div>
            @else
                <div class="col-lg-12 text-muted">This conversation is closed.</div>
            @endif
        </div>
    </div>
</section>
@endsection
