@php
    $settings = $settings ?? $siteSetting ?? null;
    $logoLightPath = $settings?->logo_light_path ?: $settings?->logo_path;
    $logoDarkPath = $settings?->logo_dark_path ?: $settings?->logo_path;
    $logoLight = $logoLightPath ? asset('storage/' . $logoLightPath) : asset('frontend/images/logo-7-light.png');
    $logoDark = $logoDarkPath ? asset('storage/' . $logoDarkPath) : asset('frontend/images/logo-7.png');
    $notificationCount = $userNotificationCount ?? 0;
    $currentUser = auth()->user();
    $mobileProfilePhoto = $currentUser?->profile?->photo_url;
    $mobileProfileInitial = $currentUser ? strtoupper(\Illuminate\Support\Str::substr($currentUser->name, 0, 1)) : 'U';
    $menuLabel = function (string $field, string $default) use ($settings) {
        $value = $settings?->{$field};

        return filled($value) ? $value : $default;
    };
    $homeLabel = $menuLabel('nav_home_label', 'Home');
    $exploreLabel = $menuLabel('nav_explore_label', 'Explore');
    $rankingsLabel = $menuLabel('nav_rankings_label', 'Rankings');
    $marketplaceLabel = $menuLabel('nav_marketplace_label', 'Marketplace');
    $profileLabel = $menuLabel('nav_profile_label', 'Profile');
    $dashboardLabel = $menuLabel('nav_dashboard_label', 'Dashboard');
    $walletLabel = $menuLabel('nav_wallet_label', 'Wallet');
    $depositLabel = $menuLabel('nav_deposit_label', 'Deposit');
    $withdrawalsLabel = $menuLabel('nav_withdrawals_label', 'Withdrawals');
    $stakeLabel = $menuLabel('nav_stake_label', 'Stake');
    $reserveLabel = $menuLabel('nav_reserve_label', 'Reserve');
    $notificationsLabel = $menuLabel('nav_notifications_label', 'Notifications');
    $supportLabel = $menuLabel('nav_support_label', 'Support');
    $profileSettingsLabel = $menuLabel('nav_profile_settings_label', 'Profile Settings');
    $loginLabel = $menuLabel('nav_login_label', 'Login');
    $registerLabel = $menuLabel('nav_register_label', 'Register');
    $logoutLabel = $menuLabel('nav_logout_label', 'Logout');
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
                            <a href="{{ route('dashboard') }}"><i class="fa fa-dashboard menu-icon" aria-hidden="true"></i>{{ $dashboardLabel }}</a>
                            <a href="{{ route('marketplace') }}"><i class="fa fa-shopping-bag menu-icon" aria-hidden="true"></i>{{ $marketplaceLabel }}</a>
                            <a href="{{ route('wallet.index') }}"><i class="fa fa-wallet menu-icon" aria-hidden="true"></i>{{ $walletLabel }}</a>
                            <a href="{{ route('wallet.deposit') }}"><i class="fa fa-arrow-circle-down menu-icon" aria-hidden="true"></i>{{ $depositLabel }}</a>
                            <a href="{{ route('wallet.withdrawals') }}"><i class="fa fa-arrow-circle-up menu-icon" aria-hidden="true"></i>{{ $withdrawalsLabel }}</a>
                            <a href="{{ route('stake.index') }}"><i class="fa fa-line-chart menu-icon" aria-hidden="true"></i>{{ $stakeLabel }}</a>
                            <a href="{{ route('reserve.index') }}"><i class="fa fa-lock menu-icon" aria-hidden="true"></i>{{ $reserveLabel }}</a>
                            <a href="{{ route('support.index') }}"><i class="fa fa-life-ring menu-icon" aria-hidden="true"></i>{{ $supportLabel }}</a>
                            <a href="{{ route('profile.edit') }}"><i class="fa fa-cog menu-icon" aria-hidden="true"></i>{{ $profileSettingsLabel }}</a>
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
                    <li><a href="{{ route('home') }}">{{ $homeLabel }}<span></span></a></li>
                    @if (feature('nft_enabled'))
                        <li><a href="{{ route('explore') }}">{{ $exploreLabel }}<span></span></a></li>
                        <li><a href="{{ route('rankings') }}">{{ $rankingsLabel }}<span></span></a></li>
                    @endif
                    <li><a href="{{ route('marketplace') }}">{{ $marketplaceLabel }}<span></span></a></li>
                    @guest
                        <li class="menu-mobile-only">
                            <a href="{{ route('login') }}">
                                <i class="fa fa-sign-in menu-icon" aria-hidden="true"></i>{{ $loginLabel }}
                            </a>
                        </li>
                        <li class="menu-mobile-only">
                            <a href="{{ route('register') }}">
                                <i class="fa fa-user-plus menu-icon" aria-hidden="true"></i>{{ $registerLabel }}
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
                                <span class="menu-label">{{ $profileLabel }}</span>
                                <span class="mobile-dropdown-hint">Tap to open</span>
                                <i class="fa fa-angle-down dropdown-caret" aria-hidden="true"></i>
                                <span class="menu-underline"></span>
                            </a>
                            <ul>
                                <li><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard menu-icon" aria-hidden="true"></i>{{ $dashboardLabel }}</a></li>
                                <li><a href="{{ route('marketplace') }}"><i class="fa fa-shopping-bag menu-icon" aria-hidden="true"></i>{{ $marketplaceLabel }}</a></li>
                                <li><a href="{{ route('wallet.index') }}"><i class="fa fa-wallet menu-icon" aria-hidden="true"></i>{{ $walletLabel }}</a></li>
                                <li><a href="{{ route('wallet.deposit') }}"><i class="fa fa-arrow-circle-down menu-icon" aria-hidden="true"></i>{{ $depositLabel }}</a></li>
                                <li><a href="{{ route('wallet.withdrawals') }}"><i class="fa fa-arrow-circle-up menu-icon" aria-hidden="true"></i>{{ $withdrawalsLabel }}</a></li>
                                <li><a href="{{ route('stake.index') }}"><i class="fa fa-line-chart menu-icon" aria-hidden="true"></i>{{ $stakeLabel }}</a></li>
                                <li><a href="{{ route('reserve.index') }}"><i class="fa fa-lock menu-icon" aria-hidden="true"></i>{{ $reserveLabel }}</a></li>
                                <li class="menu-notifications-item">
                                    <a href="{{ route('notifications.index') }}">
                                        <i class="fa fa-bell menu-icon" aria-hidden="true"></i>
                                        {{ $notificationsLabel }}
                                        @if ($notificationCount > 0)
                                            <span class="notif-badge">{{ $notificationCount }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li><a href="{{ route('support.index') }}"><i class="fa fa-life-ring menu-icon" aria-hidden="true"></i>{{ $supportLabel }}</a></li>
                                <li><a href="{{ route('profile.edit') }}"><i class="fa fa-cog menu-icon" aria-hidden="true"></i>{{ $profileSettingsLabel }}</a></li>
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
                                    <i class="fa fa-sign-out menu-icon" aria-hidden="true"></i>{{ $logoutLabel }}
                                </button>
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>

            <div class="header-right menu_side_area">
                @auth
                    <a href="{{ route('wallet.index') }}" class="btn-main btn-wallet">
                        <i class="icon_wallet_alt"></i><span>{{ $walletLabel }}</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn-main btn-light">{{ $logoutLabel }}</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-main">{{ $loginLabel }}</a>
                    <a href="{{ route('register') }}" class="btn-main btn-light">{{ $registerLabel }}</a>
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
