@props(['title' => '', 'subtitle' => null])

<section class="page-banner">
    <div class="container">
        <div class="page-banner__content text-center">
            <h1 class="page-banner__title">{{ $title }}</h1>
            @if ($subtitle)
                <p class="page-banner__subtitle">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
</section>
