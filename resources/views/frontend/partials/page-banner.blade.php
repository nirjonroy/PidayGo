@props(['title' => '', 'subtitle' => null, 'compact' => null])

@php
    $isCompact = $compact ?? request()->is(
        'dashboard',
        'dashboard/*',
        'profile',
        'account/*',
        'wallet*',
        'notifications*',
        'support*',
        'reserve*',
        'sell*',
        'stake*'
    );
@endphp

<section class="page-banner{{ $isCompact ? ' page-banner--compact' : '' }}">
    <div class="container">
        <div class="page-banner__content text-center">
            <h1 class="page-banner__title">{{ $title }}</h1>
            @if ($subtitle)
                <p class="page-banner__subtitle">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
</section>
