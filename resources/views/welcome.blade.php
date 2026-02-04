@extends('layouts.app')

@section('content')
    <h1>{{ $siteName ?? 'PidayGo' }}</h1>
    <p class="muted">{{ $siteDescription ?? 'NFT marketplace for PidayGo.' }}</p>

    <div style="margin-top:20px;">
        <div><strong>Mobile:</strong> {{ $siteMobile ?? '-' }}</div>
        <div><strong>Email:</strong> {{ $siteEmail ?? '-' }}</div>
        <div><strong>Address:</strong> {{ $siteAddress ?? '-' }}</div>
    </div>
@endsection
