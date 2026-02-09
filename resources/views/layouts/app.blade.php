<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $siteName ?? 'PidayGo' }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7fb; color: #1f2937; margin: 0; }
        .container { max-width: 720px; margin: 40px auto; background: #fff; padding: 24px; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.06); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .nav a { margin-left: 12px; }
        label { display: block; margin-top: 12px; font-weight: 600; }
        input, textarea { width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #d1d5db; border-radius: 6px; }
        button { margin-top: 16px; padding: 10px 14px; border: 0; border-radius: 6px; background: #111827; color: #fff; cursor: pointer; }
        .muted { color: #6b7280; font-size: 0.9em; }
        .error { color: #b91c1c; }
        .status { background: #ecfeff; border: 1px solid #67e8f9; color: #0e7490; padding: 8px 10px; border-radius: 6px; margin-bottom: 12px; }
        a { color: #2563eb; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div style="display:flex; align-items:center; gap:10px;">
                @if (!empty($siteLogo))
                    <img src="{{ asset('storage/' . $siteLogo) }}" alt="Logo" style="height:32px;">
                @endif
                <strong>{{ $siteName ?? 'PidayGo' }}</strong>
            </div>
            <div class="nav">
                @auth
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="{{ route('wallet.index') }}">Wallet</a>
                    <a href="{{ route('stake.index') }}">Stake</a>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Login</a>
                    <a href="{{ route('register') }}">Register</a>
                @endauth
            </div>
        </div>

        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        @yield('content')
    </div>
</body>
</html>
