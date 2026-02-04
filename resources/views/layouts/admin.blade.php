<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - {{ config('app.name') }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #111827; color: #e5e7eb; margin: 0; }
        .container { max-width: 960px; margin: 40px auto; background: #1f2937; padding: 24px; border-radius: 10px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .nav a { margin-left: 12px; color: #93c5fd; }
        label { display: block; margin-top: 12px; font-weight: 600; }
        input, textarea { width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #374151; border-radius: 6px; background: #111827; color: #e5e7eb; }
        button { margin-top: 16px; padding: 10px 14px; border: 0; border-radius: 6px; background: #2563eb; color: #fff; cursor: pointer; }
        .status { background: #0f172a; border: 1px solid #2563eb; color: #bfdbfe; padding: 8px 10px; border-radius: 6px; margin-bottom: 12px; }
        .muted { color: #9ca3af; font-size: 0.9em; }
        .error { color: #fca5a5; }
        a { color: #93c5fd; text-decoration: none; }
        a:hover { text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #374151; padding: 10px; text-align: left; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <strong>Admin</strong>
            <div class="nav">
                @auth('admin')
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    <a href="{{ route('admin.kyc.index') }}">KYC</a>
                    <form method="POST" action="{{ route('admin.logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit">Logout</button>
                    </form>
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
