@extends('layouts.frontend')

@section('content')
    <section class="page-banner">
        <div class="container">
            <div class="page-banner__content">
                <h1 class="page-banner__title">Latest News</h1>
                <div class="page-banner__subtitle">Updates and announcements from {{ $siteName ?? 'PidayGo' }}</div>
            </div>
        </div>
    </section>

    <section class="container pb40">
        <div class="row">
            @forelse ($posts as $post)
                @php
                    $image = $post->image_path ? asset('storage/' . $post->image_path) : asset('frontend/images/news/news-b1.jpg');
                    $excerpt = $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 160);
                    $published = $post->published_at?->format('F d, Y') ?? $post->created_at?->format('F d, Y');
                @endphp
                <div class="col-lg-4 col-md-6 mb-sm-30">
                    <div class="bloglist item">
                        <div class="post-content">
                            <div class="post-image">
                                <img alt="{{ $post->title }}" src="{{ $image }}" class="lazy">
                            </div>
                            <div class="post-text">
                                <span class="p-tagline">{{ $post->category ?? 'News' }}</span>
                                <span class="p-date">{{ $published }}</span>
                                <h4><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}<span></span></a></h4>
                                <p>{{ $excerpt }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-lg-12 text-center text-muted">
                    No posts yet.
                </div>
            @endforelse
        </div>

        <div class="mt-3">
            {{ $posts->links('vendor.pagination.gigaland') }}
        </div>
    </section>
@endsection
