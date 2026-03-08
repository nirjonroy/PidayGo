@php
    $settings = $settings ?? $siteSetting ?? null;
    $logoLightPath = $settings?->logo_light_path ?: $settings?->logo_path;
    $logoDarkPath = $settings?->logo_dark_path ?: $settings?->logo_path;
    $logoLight = $logoLightPath ? asset('storage/' . $logoLightPath) : asset('frontend/images/logo-7-light.png');
    $logoDark = $logoDarkPath ? asset('storage/' . $logoDarkPath) : asset('frontend/images/logo-7.png');
    $notificationCount = $userNotificationCount ?? 0;
    $currentUser = auth()->user();
    $mobileProfilePhoto = $currentUser?->profile?->photo_path ? asset('storage/' . $currentUser->profile->photo_path) : null;
    $mobileProfileInitial = $currentUser ? strtoupper(\Illuminate\Support\Str::substr($currentUser->name, 0, 1)) : 'U';
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
                @auth
                    <div class="mobile-profile-dropdown-shell">
                        <button type="button" id="mobile-profile-toggle" class="mobile-profile-btn" aria-label="Profile menu" aria-expanded="false">
                            @if ($mobileProfilePhoto)
                                <img src="{{ $mobileProfilePhoto }}" alt="{{ $currentUser->name }}" class="mobile-profile-avatar">
                            @else
                                <span class="mobile-profile-fallback">{{ $mobileProfileInitial }}</span>
                            @endif
                            <i class="fa fa-angle-down mobile-profile-caret" aria-hidden="true"></i>
                        </button>
                        <div id="mobile-profile-menu" class="mobile-profile-menu">
                            <a href="{{ route('dashboard') }}"><i class="fa fa-dashboard menu-icon" aria-hidden="true"></i>Dashboard</a>
                            <a href="{{ route('wallet.index') }}"><i class="fa fa-wallet menu-icon" aria-hidden="true"></i>Wallet</a>
                            <a href="{{ route('wallet.deposit') }}"><i class="fa fa-arrow-circle-down menu-icon" aria-hidden="true"></i>Deposit</a>
                            <a href="{{ route('wallet.withdrawals') }}"><i class="fa fa-arrow-circle-up menu-icon" aria-hidden="true"></i>Withdrawals</a>
                            <a href="{{ route('stake.index') }}"><i class="fa fa-line-chart menu-icon" aria-hidden="true"></i>Stake</a>
                            <a href="{{ route('reserve.index') }}"><i class="fa fa-lock menu-icon" aria-hidden="true"></i>Reserve</a>
                            <a href="{{ route('support.index') }}"><i class="fa fa-life-ring menu-icon" aria-hidden="true"></i>Support</a>
                            <a href="{{ route('profile.edit') }}"><i class="fa fa-cog menu-icon" aria-hidden="true"></i>Profile Settings</a>
                        </div>
                    </div>
                    <a href="{{ route('notifications.index') }}" class="mobile-notification-btn" aria-label="Notifications">
                        <i class="fa fa-bell" aria-hidden="true"></i>
                        @if ($notificationCount > 0)
                            <span class="notif-badge notif-badge--floating">{{ $notificationCount }}</span>
                        @endif
                    </a>
                @endauth
                <span id="menu-btn"></span>
            </div>

            <div class="header-center">
                <ul id="mainmenu">
                    <li><a href="{{ route('home') }}">Home<span></span></a></li>
                    @if (feature('nft_enabled'))
                        <li><a href="{{ route('explore') }}">Explore<span></span></a></li>
                        <li><a href="{{ route('rankings') }}">Rankings<span></span></a></li>
                    @endif
                    @guest
                        <li class="menu-mobile-only">
                            <a href="{{ route('login') }}">
                                <i class="fa fa-sign-in menu-icon" aria-hidden="true"></i>Login
                            </a>
                        </li>
                        <li class="menu-mobile-only">
                            <a href="{{ route('register') }}">
                                <i class="fa fa-user-plus menu-icon" aria-hidden="true"></i>Register
                            </a>
                        </li>
                    @endguest
                    @auth
                        <li class="menu-profile-dropdown">
                            <a href="#" class="has-submenu">
                                @if ($mobileProfilePhoto)
                                    <img src="{{ $mobileProfilePhoto }}" alt="{{ $currentUser->name }}" class="desktop-profile-avatar">
                                @else
                                    <span class="desktop-profile-fallback">{{ $mobileProfileInitial }}</span>
                                @endif
                                <span class="menu-label">Profile</span>
                                <span class="mobile-dropdown-hint">Tap to open</span>
                                <i class="fa fa-angle-down dropdown-caret" aria-hidden="true"></i>
                                <span class="menu-underline"></span>
                            </a>
                            <ul>
                                <li><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard menu-icon" aria-hidden="true"></i>Dashboard</a></li>
                                <li><a href="{{ route('wallet.index') }}"><i class="fa fa-wallet menu-icon" aria-hidden="true"></i>Wallet</a></li>
                                <li><a href="{{ route('wallet.deposit') }}"><i class="fa fa-arrow-circle-down menu-icon" aria-hidden="true"></i>Deposit</a></li>
                                <li><a href="{{ route('wallet.withdrawals') }}"><i class="fa fa-arrow-circle-up menu-icon" aria-hidden="true"></i>Withdrawals</a></li>
                                <li><a href="{{ route('stake.index') }}"><i class="fa fa-line-chart menu-icon" aria-hidden="true"></i>Stake</a></li>
                                <li><a href="{{ route('reserve.index') }}"><i class="fa fa-lock menu-icon" aria-hidden="true"></i>Reserve</a></li>
                                <li class="menu-notifications-item">
                                    <a href="{{ route('notifications.index') }}">
                                        <i class="fa fa-bell menu-icon" aria-hidden="true"></i>
                                        Notifications
                                        @if ($notificationCount > 0)
                                            <span class="notif-badge">{{ $notificationCount }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li><a href="{{ route('support.index') }}"><i class="fa fa-life-ring menu-icon" aria-hidden="true"></i>Support</a></li>
                                <li><a href="{{ route('profile.edit') }}"><i class="fa fa-cog menu-icon" aria-hidden="true"></i>Profile Settings</a></li>
                            </ul>
                        </li>
                        <li class="menu-mobile-only">
                            <a href="#" id="mobile-theme-toggle">
                                <i class="fa fa-adjust menu-icon" aria-hidden="true"></i>Theme
                            </a>
                        </li>
                        <li class="menu-mobile-only">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="menu-logout-btn">
                                    <i class="fa fa-sign-out menu-icon" aria-hidden="true"></i>Logout
                                </button>
                            </form>
                        </li>
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
                <span id="mobile-switch-scheme" class="mobile-theme-btn" title="Toggle theme">
                    <i class="fa fa-moon-o"></i>
                </span>
                <span href="#" id="switch_scheme">
                    <i class="ss_dark fa fa-moon-o"></i>
                    <i class="ss_light fa fa-sun-o"></i>
                </span>
            </div>
        </div>
    </div>
</header>
<!-- header close -->
