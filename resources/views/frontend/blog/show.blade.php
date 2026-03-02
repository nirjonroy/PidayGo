@extends('layouts.frontend')

@section('content')
    <section class="page-banner">
        <div class="container">
            <div class="page-banner__content">
                <h1 class="page-banner__title">{{ $post->title }}</h1>
                <div class="page-banner__subtitle">
                    {{ $post->category ?? 'News' }} •
                    {{ $post->published_at?->format('F d, Y') ?? $post->created_at?->format('F d, Y') }}
                </div>
            </div>
        </div>
    </section>

    <section class="container pb40">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if ($post->image_path)
                    <img src="{{ asset('storage/' . $post->image_path) }}" alt="{{ $post->title }}" class="img-fluid rounded mb-4">
                @endif

                @if ($post->excerpt)
                    <p class="lead">{{ $post->excerpt }}</p>
                @endif

                <div class="content">
                    {!! nl2br(e($post->content ?? '')) !!}
                </div>
            </div>
        </div>
    </section>
@endsection
