<!DOCTYPE html>
<html lang="zxx">

<head>
    @php
        $settings = $settings ?? $siteSetting ?? null;
        $siteName = $settings?->site_name ?: config('app.name', 'PidayGo');
        $normalizeHex = function ($hex) {
            $hex = trim((string) $hex);
            if ($hex === '') {
                return null;
            }
            $hex = ltrim($hex, '#');
            if (!preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
                return null;
            }
            return '#' . strtoupper($hex);
        };
        $hexToRgb = function ($hex) {
            $hex = ltrim($hex, '#');
            return hexdec(substr($hex, 0, 2)) . ', ' . hexdec(substr($hex, 2, 2)) . ', ' . hexdec(substr($hex, 4, 2));
        };
        $themePrimary = $normalizeHex($settings?->theme_primary_color);
        $themeSecondary = $normalizeHex($settings?->theme_secondary_color);
        $themeMode = $settings?->theme_mode ?? 'auto';
    @endphp
    <title>{{ $siteName }}</title>
    @if ($settings?->favicon_path)
        <link rel="icon" href="{{ asset('storage/' . $settings->favicon_path) }}">
    @else
        <link rel="icon" href="{{ asset('frontend/images/icon.png') }}">
    @endif
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="PidayGo - NFT Marketplace" name="description" />
    <meta content="" name="keywords" />
    <meta content="" name="author" />
    <!-- CSS Files
    ================================================== -->
    <link id="bootstrap" href="{{ asset('frontend/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('frontend/css/plugins.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('frontend/css/style.css') }}" rel="stylesheet" type="text/css" />
    <!-- color scheme -->
    <link id="colors" href="{{ asset('frontend/css/colors/scheme-12.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('frontend/css/coloring.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('frontend/css/de-modern.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('frontend/css/coloring-gradient.css') }}" rel="stylesheet" type="text/css" />
    <!-- custom font -->
    <link href="{{ asset('frontend/css/custom-font-3.css') }}" rel="stylesheet" type="text/css" />
    @if (in_array($themeMode, ['light', 'dark'], true))
        <script>
            (function () {
                var mode = "{{ $themeMode }}";
                var value = mode === 'dark' ? '1' : '2';
                var expires = new Date();
                expires.setFullYear(expires.getFullYear() + 3);
                document.cookie = "c_mod=" + value + "; path=/; expires=" + expires.toUTCString();
            })();
        </script>
    @endif
    @if ($themePrimary || $themeSecondary)
        <style>
            :root {
                @if ($themePrimary)
                --primary-color: {{ $themePrimary }};
                --primary-color-rgb: {{ $hexToRgb($themePrimary) }};
                --primary-color-2: {{ $themePrimary }};
                --primary-color-2-rgb: {{ $hexToRgb($themePrimary) }};
                @endif
                @if ($themeSecondary)
                --secondary-color: {{ $themeSecondary }};
                --secondary-color-rgb: {{ $hexToRgb($themeSecondary) }};
                --secondary-color-2: {{ $themeSecondary }};
                --secondary-color-rgb-2: {{ $hexToRgb($themeSecondary) }};
                @endif
            }
        </style>
    @endif
    <style>
        #logo img.logo,
        #logo img.logo-2 {
            height: 60px;
            width: auto;
        }
        #logo {
            display: flex;
            align-items: center;
        }
        #logo a {
            display: inline-flex;
            align-items: center;
        }
        @media (max-width: 991.98px) {
            #logo img.logo,
            #logo img.logo-2 {
                height: 58px;
            }
        }
        .dark-scheme .table,
        .dark-scheme .table td,
        .dark-scheme .table th {
            color: #ffffff;
        }
        .dark-scheme .table thead th {
            color: #ffffff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }
        .dark-scheme .table td,
        .dark-scheme .table th {
            border-color: rgba(255, 255, 255, 0.08);
        }
        .dark-scheme .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.05);
        }
        .dark-scheme .table-striped > tbody > tr:nth-of-type(even) {
            background-color: rgba(255, 255, 255, 0.02);
        }
        .dark-scheme .table .text-muted {
            color: #b4b9c2 !important;
        }
        .reserve-table-card {
            background: rgba(255, 255, 255, 0.6);
            border-radius: 16px;
            padding: 12px 16px;
            box-shadow: 0 10px 24px rgba(24, 24, 31, 0.08);
            margin-bottom: 16px;
        }
        .reserve-table-card .table {
            margin-bottom: 0;
        }
        .reserve-table-card .table th,
        .reserve-table-card .table td {
            padding: 10px 12px;
        }
        .dark-scheme .reserve-table-card {
            background: rgba(13, 13, 20, 0.65);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.32);
        }
        .dark-scheme .form-control,
        .dark-scheme .form-select,
        .dark-scheme select,
        .dark-scheme textarea,
        .dark-scheme input[type="text"],
        .dark-scheme input[type="number"],
        .dark-scheme input[type="email"],
        .dark-scheme input[type="password"] {
            background: rgba(255, 255, 255, 0.06);
            color: #f2f5f9;
            border-color: rgba(255, 255, 255, 0.15);
        }
        .dark-scheme .form-control::placeholder,
        .dark-scheme textarea::placeholder {
            color: rgba(255, 255, 255, 0.55);
        }
        .dark-scheme body,
        .dark-scheme,
        .dark-scheme p,
        .dark-scheme li,
        .dark-scheme label,
        .dark-scheme .form-label,
        .dark-scheme h1,
        .dark-scheme h2,
        .dark-scheme h3,
        .dark-scheme h4,
        .dark-scheme h5,
        .dark-scheme h6 {
            color: #f2f5f9;
        }
        .dark-scheme .text-dark {
            color: #f2f5f9 !important;
        }
        .header-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 18px;
            min-width: 260px;
        }
        .header-left #menu-btn {
            display: none;
        }
        .header-search input {
            min-width: 220px;
        }
        .header-center {
            flex: 1;
            display: flex;
            justify-content: center;
        }
        #mainmenu {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: nowrap;
            white-space: nowrap;
        }
        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
            justify-content: flex-end;
            min-width: 220px;
        }
        .menu-mobile-only {
            display: none;
        }
        .menu-icon {
            display: inline-block;
            width: 18px;
            margin-right: 8px;
            opacity: 0.75;
        }
        .dropdown-caret {
            margin-left: 8px;
            font-size: 14px;
            opacity: 0.7;
        }
        .menu-logout-btn {
            background: none;
            border: none;
            padding: 0;
            color: inherit;
            font: inherit;
            cursor: pointer;
        }
        .mobile-theme-btn {
            display: none;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 1px solid rgba(17, 24, 39, 0.12);
            align-items: center;
            justify-content: center;
            color: #111827;
            background: #ffffff;
            cursor: pointer;
        }
        .notif-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 18px;
            height: 18px;
            padding: 0 6px;
            margin-left: 6px;
            border-radius: 999px;
            background: #ff5b5b;
            color: #fff;
            font-size: 11px;
            line-height: 1;
            font-weight: 600;
        }
        .page-banner {
            position: relative;
            padding: 20px 0;
            min-height: 180px;
            margin: 90px 0 30px;
            background: url("{{ asset('frontend/images/banner.png') }}") center/cover no-repeat;
            border-radius: 18px;
            overflow: hidden;
            display: flex;
            align-items: center;
        }
        .page-banner--compact {
            margin-top: 24px;
        }
        .page-banner > .container {
            width: 100%;
        }
        .page-banner::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.35);
        }
        .page-banner__content {
            position: relative;
            z-index: 1;
        }
        .page-banner__title {
            color: #ffffff;
            font-weight: 800;
            margin-bottom: 0;
            font-size: clamp(26px, 4vw, 44px);
            line-height: 1.2;
        }
        .page-banner__subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 600;
            margin-top: 8px;
        }
        @media (max-width: 991.98px) {
            .page-banner {
                padding: 16px 0;
                min-height: 140px;
                margin: 70px 0 20px;
                border-radius: 14px;
            }
            .page-banner--compact {
                margin-top: 16px;
            }
        }
        .gigaland-pagination .pagination {
            gap: 6px;
            margin-top: 10px;
        }
        .gigaland-pagination .page-link {
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            background: rgba(255, 255, 255, 0.05);
            color: #f2f5f9;
            padding: 6px 12px;
            line-height: 1.2;
        }
        .gigaland-pagination .page-item.active .page-link {
            background: linear-gradient(90deg, #fbb040, #8a2be2);
            border-color: transparent;
            color: #fff;
        }
        .gigaland-pagination .page-item.disabled .page-link {
            opacity: 0.5;
        }
        .gigaland-pagination .page-link::before,
        .gigaland-pagination .page-link::after {
            display: none !important;
            content: none !important;
        }
        .popup-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        .popup-card {
            background: #101017;
            color: #f2f5f9;
            padding: 20px;
            border-radius: 12px;
            max-width: 520px;
            width: 92%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
        }
        .popup-card h3 {
            margin-top: 0;
            font-size: 20px;
        }
        .popup-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 16px;
        }
        .top-sellers-grid {
            margin: 0;
        }
        .seller-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 16px;
            box-shadow: 0 10px 24px rgba(24, 24, 31, 0.08);
        }
        .seller-avatar {
            position: relative;
            width: 52px;
            height: 52px;
            flex: 0 0 52px;
        }
        .seller-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 999px;
            object-fit: cover;
        }
        .seller-badge {
            position: absolute;
            right: -2px;
            bottom: -2px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #6b6bf1;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }
        .seller-meta {
            min-width: 0;
            flex: 1 1 auto;
        }
        .seller-name {
            font-weight: 700;
            color: #1a1b1e;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .seller-username {
            font-size: 13px;
            color: #6b7280;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .seller-volume {
            font-weight: 700;
            color: #1a1b1e;
            font-size: 13px;
            white-space: nowrap;
        }
        .seller-empty {
            padding: 12px 0;
            color: #6b7280;
        }
        .top-sellers-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        .top-sellers-title {
            margin: 0;
            font-size: clamp(20px, 3vw, 30px);
            letter-spacing: 0;
        }
        .top-sellers-filter .btn-selector {
            font-size: 14px;
            padding: 6px 12px;
        }
        .top-sellers-filter.dropdown.alt-2 {
            position: static !important;
            padding-top: 0;
        }
        .top-sellers-filter.dropdown.alt-2 > a {
            font-size: 18px;
            min-width: auto;
            padding: 4px 8px;
        }
        @media (max-width: 991.98px) {
            .header-bar {
                flex-wrap: wrap;
                gap: 10px;
                position: relative;
                padding-right: 48px;
            }
            header.scroll-dark,
            header.header-mobile {
                background: #000000;
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.35);
            }
            .header-left {
                width: 100%;
                justify-content: flex-start;
                flex-wrap: wrap;
                gap: 10px;
            }
            .header-left #menu-btn {
                display: inline-flex;
            }
            #logo img.logo,
            #logo img.logo-2 {
                height: 44px;
            }
            .header-search {
                width: 100%;
                order: 2;
            }
            .header-search input {
                width: 100%;
                min-width: 0;
            }
            .header-right {
                width: auto;
                min-width: 0;
                order: 2;
            }
            .header-right .btn-main {
                display: none;
            }
            .header-right #switch_scheme {
                display: none;
            }
            .header-right .mobile-theme-btn {
                display: inline-flex;
            }
            .header-center {
                width: 100%;
                order: 3;
            }
            #menu-btn {
                position: absolute !important;
                top: 14px !important;
                right: 16px !important;
                margin: 0 !important;
                float: none !important;
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
            }
            #mainmenu {
                flex-direction: column;
                align-items: flex-start;
                gap: 0;
                white-space: normal;
            }
            #mainmenu li > a.has-submenu {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            header.header-mobile #mainmenu > li > span {
                display: none !important;
            }
            header.header-mobile #mainmenu > li > ul > li > span {
                display: none !important;
            }
            .menu-mobile-only {
                display: block !important;
            }
            .menu-logout-btn {
                width: 100%;
                text-align: left;
            }
            #menu-btn:before {
                font-size: 22px;
            }
            #mainmenu li a {
                padding: 10px 0;
            }
            #mainmenu li ul li a {
                padding-left: 18px;
            }
            .seller-card {
                flex-wrap: wrap;
            }
            .seller-volume {
                width: 100%;
                text-align: left;
            }
            .top-sellers-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .top-sellers-filter {
                margin-top: 6px;
            }
        }
        @media (max-width: 575.98px) {
            .author_list.alt-2.d-col-3 {
                column-count: 1 !important;
            }
            .author_list.alt-2 li {
                width: 100%;
            }
            .reserve-table-card {
                padding: 8px 10px;
            }
            .reserve-table-card .table th,
            .reserve-table-card .table td {
                padding: 8px;
                font-size: 13px;
            }
        }

        .hero-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 18px;
        }
        .hero-card {
            background: linear-gradient(135deg, rgba(var(--secondary-color-rgb), 0.2) 0%, rgba(var(--secondary-color-rgb-2), 0.2) 100%);
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.08);
        }
        .dark-scheme .hero-card {
            background: linear-gradient(135deg, rgba(var(--primary-color-rgb), 0.85) 0%, rgba(var(--primary-color-2-rgb), 0.85) 100%);
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.28);
        }
        @media (max-width: 991.98px) {
            .hero-card {
                padding: 20px;
            }
            #section-hero h1 {
                font-size: clamp(26px, 6vw, 36px);
                text-align: center;
            }
            #section-hero .lead {
                text-align: center;
            }
            .hero-actions {
                justify-content: center;
            }
        }
    </style>
</head>

<body class="switch-scheme">
    <div id="wrapper">
        <!-- header begin -->
        @include('frontend.partials.header')
        <!-- content begin -->
        <div class="no-bottom no-top" id="content">
            <div id="top"></div>
            @yield('content')
        </div>
        <!-- content close -->

        <a href="#" id="back-to-top"></a>

        <!-- footer begin -->
        <footer>
            @php
                $settings = $settings ?? $siteSetting ?? null;
                $footerLinks = $footerLinks ?? collect();
                $defaults = [
                    'marketplace' => [
                        ['label' => 'All NFTs', 'url' => route('explore')],
                        ['label' => 'Art', 'url' => route('explore')],
                        ['label' => 'Music', 'url' => route('explore')],
                        ['label' => 'Domain Names', 'url' => route('explore')],
                        ['label' => 'Virtual World', 'url' => route('explore')],
                        ['label' => 'Collectibles', 'url' => route('explore')],
                    ],
                    'resources' => [
                        ['label' => 'Help Center', 'url' => '#'],
                        ['label' => 'Partners', 'url' => '#'],
                        ['label' => 'Suggestions', 'url' => '#'],
                        ['label' => 'Discord', 'url' => '#'],
                        ['label' => 'Docs', 'url' => '#'],
                        ['label' => 'Newsletter', 'url' => '#'],
                    ],
                    'community' => [
                        ['label' => 'Community', 'url' => '#'],
                        ['label' => 'Documentation', 'url' => '#'],
                        ['label' => 'Brand Assets', 'url' => '#'],
                        ['label' => 'Blog', 'url' => route('blog.index')],
                        ['label' => 'Forum', 'url' => '#'],
                        ['label' => 'Mailing List', 'url' => '#'],
                    ],
                ];
                $newsletterTitle = $settings?->footer_newsletter_title ?: 'Get the latest updates';
                $newsletterText = $settings?->footer_newsletter_text ?: 'Signup for our newsletter to get the latest updates in your inbox.';
                $newsletterPlaceholder = $settings?->footer_newsletter_placeholder ?: 'enter your email';
                $socials = [
                    ['url' => $settings?->footer_social_facebook, 'icon' => 'fa-facebook'],
                    ['url' => $settings?->footer_social_twitter, 'icon' => 'fa-twitter'],
                    ['url' => $settings?->footer_social_instagram, 'icon' => 'fa-instagram'],
                    ['url' => $settings?->footer_social_youtube, 'icon' => 'fa-youtube'],
                    ['url' => $settings?->footer_social_email ? 'mailto:' . $settings->footer_social_email : ($siteEmail ? 'mailto:' . $siteEmail : null), 'icon' => 'fa-envelope-o'],
                ];
            @endphp
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-sm-6 col-xs-1">
                        <div class="widget">
                            <h5>{{ $newsletterTitle }}</h5>
                            <p>{{ $newsletterText }}</p>
                            <form action="#" class="row form-dark" id="form_subscribe" method="post" name="form_subscribe">
                                <div class="col text-center">
                                    <input class="form-control" id="txt_subscribe" name="txt_subscribe" placeholder="{{ $newsletterPlaceholder }}" type="text" />
                                    <a href="#" id="btn-subscribe"><i class="arrow_right bg-color-secondary"></i></a>
                                    <div class="clearfix"></div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-lg-5 offset-lg-1">
                        <div class="row">
                            <div class="col-lg-4 col-sm-6 col-xs-1">
                                <div class="widget">
                                    <h5>Marketplace</h5>
                                    <ul>
                                        @php
                                            $links = $footerLinks->get('marketplace', collect());
                                            if ($links->isEmpty()) {
                                                $links = collect($defaults['marketplace']);
                                            }
                                        @endphp
                                        @foreach ($links as $link)
                                            @php
                                                $label = is_array($link) ? $link['label'] : $link->label;
                                                $url = is_array($link) ? $link['url'] : $link->url;
                                            @endphp
                                            <li><a href="{{ $url }}">{{ $label }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <div class="col-lg-4 col-sm-6 col-xs-1">
                                <div class="widget">
                                    <h5>Resources</h5>
                                    <ul>
                                        @php
                                            $links = $footerLinks->get('resources', collect());
                                            if ($links->isEmpty()) {
                                                $links = collect($defaults['resources']);
                                            }
                                        @endphp
                                        @foreach ($links as $link)
                                            @php
                                                $label = is_array($link) ? $link['label'] : $link->label;
                                                $url = is_array($link) ? $link['url'] : $link->url;
                                            @endphp
                                            <li><a href="{{ $url }}">{{ $label }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <div class="col-lg-4 col-sm-6 col-xs-1">
                                <div class="widget">
                                    <h5>Community</h5>
                                    <ul>
                                        @php
                                            $links = $footerLinks->get('community', collect());
                                            if ($links->isEmpty()) {
                                                $links = collect($defaults['community']);
                                            }
                                        @endphp
                                        @foreach ($links as $link)
                                            @php
                                                $label = is_array($link) ? $link['label'] : $link->label;
                                                $url = is_array($link) ? $link['url'] : $link->url;
                                            @endphp
                                            <li><a href="{{ $url }}">{{ $label }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6 col-xs-1">
                        <div class="widget">
                            <h5>Join the community</h5>
                            <div class="social-icons">
                                @foreach ($socials as $social)
                                    @if (!empty($social['url']))
                                        <a href="{{ $social['url'] }}" target="_blank" rel="noopener"><i class="fa {{ $social['icon'] }} fa-lg"></i></a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="subfooter">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('home') }}">
                                <span class="copy">
                                    {{ $settings?->footer_copyright_text ?: ('© Copyright ' . now()->year . ' - ' . ($siteName ?? 'PidayGo')) }}
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- footer close -->

    </div>

    <!-- Javascript Files
    ================================================== -->
    <script src="{{ asset('frontend/js/plugins.js') }}"></script>
    <script src="{{ asset('frontend/js/designesia.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mobileToggle = document.getElementById('mobile-theme-toggle');
            const desktopToggle = document.getElementById('switch_scheme');
            const mobileSwitch = document.getElementById('mobile-switch-scheme');
            if (mobileToggle && desktopToggle) {
                mobileToggle.addEventListener('click', function (event) {
                    event.preventDefault();
                    desktopToggle.click();
                });
            }
            if (mobileSwitch && desktopToggle) {
                mobileSwitch.addEventListener('click', function (event) {
                    event.preventDefault();
                    desktopToggle.click();
                });
            }

            const submenuLinks = document.querySelectorAll('#mainmenu > li > a.has-submenu');
            submenuLinks.forEach(function (link) {
                link.addEventListener('click', function (event) {
                    if (window.innerWidth <= 991) {
                        event.preventDefault();
                        const toggle = link.parentElement.querySelector(':scope > span');
                        if (toggle) {
                            toggle.click();
                        }
                    }
                });
            });
        });
    </script>

    @if (!empty($popupNotification) && $popupNotification->notification)
        <div class="popup-backdrop" id="notif-modal">
            <div class="popup-card">
                <h3>{{ $popupNotification->notification->title }}</h3>
                <p>{{ $popupNotification->notification->message }}</p>
                <div class="popup-actions">
                    <form method="POST" action="{{ route('notifications.dismiss', $popupNotification->notification) }}">
                        @csrf
                        <button type="submit" class="btn-main btn-light">Dismiss</button>
                    </form>
                    <form method="POST" action="{{ route('notifications.read', $popupNotification->notification) }}">
                        @csrf
                        <button type="submit" class="btn-main">Mark as Read</button>
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
