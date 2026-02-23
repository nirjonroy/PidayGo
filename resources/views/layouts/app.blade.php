<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        .badge { display: inline-block; padding: 2px 6px; font-size: 12px; background: #ef4444; color: #fff; border-radius: 999px; margin-left: 4px; }
        .modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.45); display: none; align-items: center; justify-content: center; z-index: 50; }
        .modal { background: #fff; border-radius: 10px; max-width: 520px; width: 90%; padding: 18px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .modal h3 { margin-top: 0; }
        .modal-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 16px; }
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
                    <a href="{{ route('notifications.index') }}">Notifications
                        @if (!empty($userNotificationCount))
                            <span class="badge">{{ $userNotificationCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('support.index') }}">Support</a>
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

    @if (!empty($popupNotification) && $popupNotification->notification)
        <div class="modal-backdrop" id="notif-modal">
            <div class="modal">
                <h3>{{ $popupNotification->notification->title }}</h3>
                <p>{{ $popupNotification->notification->message }}</p>
                <div class="modal-actions">
                    <form method="POST" action="{{ route('notifications.dismiss', $popupNotification->notification) }}">
                        @csrf
                        <button type="submit">Dismiss</button>
                    </form>
                    <form method="POST" action="{{ route('notifications.read', $popupNotification->notification) }}">
                        @csrf
                        <button type="submit">Mark as Read</button>
                    </form>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modal = document.getElementById('notif-modal');
                if (modal) {
                    modal.style.display = 'flex';
                    fetch("{{ route('notifications.shown', $popupNotification->notification) }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    });
                    modal.addEventListener('click', function (e) {
                        if (e.target === modal) {
                            modal.style.display = 'none';
                        }
                    });
                }
            });
        </script>
    @endif
</body>
</html>
