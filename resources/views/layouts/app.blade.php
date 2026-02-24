@extends('layouts.frontend')

@section('content')
<section aria-label="section">
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        <div class="nft__item s2">
            <div class="nft__item_info">
                @yield('content')
            </div>
        </div>
    </div>
</section>
@endsection
