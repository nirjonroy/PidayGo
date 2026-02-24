@php
    $settings = $settings ?? $siteSetting ?? null;
    $logoLightPath = $settings?->logo_light_path ?: $settings?->logo_path;
    $logoDarkPath = $settings?->logo_dark_path ?: $settings?->logo_path;
    $logoLight = $logoLightPath ? asset('storage/' . $logoLightPath) : asset('frontend/images/logo-7-light.png');
    $logoDark = $logoDarkPath ? asset('storage/' . $logoDarkPath) : asset('frontend/images/logo-7.png');
    $notificationCount = $userNotificationCount ?? 0;
@endphp

<header class="scroll-dark">
    <div class="container">
        <div class="header-bar sm-pt10">
            <div class="header-left">
                <div id="logo">
                    <a href="{{ route('home') }}">
                        <img alt="" class="logo" src="{{ $logoLight }}" />
                        <img alt="" class="logo-2" src="{{ $logoDark }}" />
                    </a>
                </div>
                <div class="header-search xs-hide">
                    <input id="quick_search" name="quick_search" placeholder="search item here..." type="text" />
                </div>
            </div>

            <div class="header-center">
                <ul id="mainmenu">
                    <li><a href="{{ route('home') }}">Home<span></span></a></li>
                    @if (feature('nft_enabled'))
                        <li><a href="{{ route('explore') }}">Explore<span></span></a></li>
                        <li><a href="{{ route('rankings') }}">Rankings<span></span></a></li>
                    @endif
                    @auth
                        <li><a href="{{ route('dashboard') }}">Dashboard<span></span></a></li>
                        <li><a href="{{ route('profile.edit') }}">Profile<span></span></a></li>
                        <li><a href="{{ route('wallet.index') }}">Wallet<span></span></a></li>
                        <li><a href="{{ route('stake.index') }}">Stake<span></span></a></li>
                        <li>
                            <a href="{{ route('notifications.index') }}">
                                Notifications
                                @if ($notificationCount > 0)
                                    <span class="notif-badge">{{ $notificationCount }}</span>
                                @endif
                                <span></span>
                            </a>
                        </li>
                        <li><a href="{{ route('support.index') }}">Support<span></span></a></li>
                    @endauth
                </ul>
            </div>

            <div class="header-right menu_side_area">
                @auth
                    <a href="{{ route('wallet.index') }}" class="btn-main btn-wallet">
                        <i class="icon_wallet_alt"></i><span>Wallet</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn-main btn-light">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-main">Login</a>
                    <a href="{{ route('register') }}" class="btn-main btn-light">Register</a>
                @endauth
                <span href="#" id="switch_scheme">
                    <i class="ss_dark fa fa-moon-o"></i>
                    <i class="ss_light fa fa-sun-o"></i>
                </span>
                <span id="menu-btn"></span>
            </div>
        </div>
    </div>
</header>
<!-- header close -->
