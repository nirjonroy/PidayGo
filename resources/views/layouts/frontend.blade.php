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
    <meta content="PidayGo - PI Marketplace" name="description" />
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
            display: none !important;
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
        .menu-profile-dropdown > a.has-submenu {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
        }
        .desktop-profile-avatar,
        .desktop-profile-fallback {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            flex: 0 0 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .desktop-profile-avatar {
            object-fit: cover;
            border: 1px solid rgba(17, 24, 39, 0.08);
        }
        .desktop-profile-fallback {
            background: linear-gradient(135deg, #f0a83a, #6f33cc);
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
        }
        .menu-profile-dropdown > a.has-submenu .menu-label {
            display: inline !important;
            width: auto !important;
            color: inherit !important;
            font-size: inherit !important;
            line-height: inherit !important;
            text-transform: none !important;
            border-bottom: 0 !important;
        }
        .menu-profile-dropdown > a.has-submenu .mobile-dropdown-hint,
        .menu-profile-dropdown > a.has-submenu .menu-underline {
            display: none !important;
        }
        .mobile-dropdown-hint {
            display: none;
        }
        .menu-logout-btn {
            background: none;
            border: none;
            padding: 0;
            color: inherit;
            font: inherit;
            cursor: pointer;
        }
        .password-input {
            position: relative;
        }
        .password-input .form-control {
            padding-right: 44px;
        }
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 0;
            color: inherit;
            cursor: pointer;
            opacity: 0.75;
        }
        .password-toggle:hover {
            opacity: 1;
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
        .mobile-profile-dropdown-shell,
        .mobile-notification-btn {
            display: none;
            text-decoration: none;
        }
        .mobile-quick-actions {
            display: none;
        }
        .mobile-profile-btn {
            border: 0;
            background: transparent;
            padding: 0;
            cursor: pointer;
        }
        .mobile-profile-avatar,
        .mobile-profile-fallback {
            width: 100%;
            height: 100%;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .mobile-profile-avatar {
            object-fit: cover;
        }
        .mobile-profile-fallback {
            font-size: 13px;
            font-weight: 700;
        }
        .mobile-profile-menu {
            display: none;
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
        .notif-badge--floating {
            position: absolute;
            top: -4px;
            right: -6px;
            margin-left: 0;
            min-width: 16px;
            height: 16px;
            padding: 0 4px;
            font-size: 10px;
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
        .dark-scheme .page-banner,
        body.dark-scheme .page-banner {
            background: url("{{ asset('frontend/images/banner.png') }}") center/cover no-repeat !important;
            background-color: transparent !important;
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
            background: rgba(4, 6, 14, 0.72);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 1000;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
        .popup-card {
            background: #ffffff;
            color: #101828;
            padding: 24px;
            border-radius: 22px;
            max-width: 520px;
            width: 92%;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 24px 50px rgba(15, 23, 42, 0.18);
            animation: popup-card-rise 0.24s ease;
        }
        .dark-scheme .popup-card {
            background: #101017;
            color: #f2f5f9;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 24px 50px rgba(0, 0, 0, 0.35);
        }
        .popup-card__icon {
            width: 70px;
            height: 70px;
            border-radius: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            background: linear-gradient(135deg, rgba(240, 168, 58, 0.18), rgba(111, 51, 204, 0.24));
            border: 1px solid rgba(240, 168, 58, 0.2);
            box-shadow: 0 14px 28px rgba(111, 51, 204, 0.18);
        }
        .popup-card__icon img {
            width: 44px;
            height: 44px;
            object-fit: contain;
            animation: popup-pulse 1.2s ease-in-out infinite;
        }
        .popup-card h3 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 20px;
            color: inherit;
        }
        .popup-card p {
            margin-bottom: 0;
            color: inherit;
            line-height: 1.75;
        }
        .popup-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
            margin-top: 16px;
        }
        .popup-actions form,
        .popup-actions .btn-main {
            flex: 1 1 0;
        }
        .popup-actions form {
            margin: 0;
        }
        .popup-actions .btn-main {
            width: 100%;
            min-height: 54px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 18px;
            white-space: nowrap;
        }
        .popup-actions .btn-border {
            min-height: 54px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 10px 18px;
            border: 1px solid rgba(15, 23, 42, 0.14);
            color: inherit;
            background: rgba(15, 23, 42, 0.04);
        }
        .dark-scheme .popup-actions .btn-border {
            border-color: rgba(255, 255, 255, 0.14);
            background: rgba(255, 255, 255, 0.06);
            color: #f2f5f9;
        }
        @keyframes popup-card-rise {
            0% {
                opacity: 0;
                transform: translateY(14px) scale(0.97);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        @keyframes popup-pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.08);
            }
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
            body.has-mobile-quick-nav #wrapper {
                padding-bottom: 94px;
            }
            header.header-mobile .container {
                width: 100%;
                max-width: none;
                padding-left: 0;
                padding-right: 0;
                overflow: visible;
            }
            header.header-mobile {
                overflow: visible !important;
                z-index: 1300;
            }
            .header-bar {
                flex-wrap: nowrap;
                gap: 10px;
                position: relative;
                min-height: 68px;
                padding: 14px 154px 14px 18px;
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
            .mobile-profile-dropdown-shell {
                position: absolute;
                top: 14px;
                right: 106px;
                display: block !important;
                z-index: 1002;
            }
            .mobile-profile-btn {
                width: 38px;
                height: 38px;
                border-radius: 50%;
                padding: 0;
                border: 0;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                position: relative;
                cursor: pointer;
                appearance: none;
                -webkit-appearance: none;
                color: #111827;
                background: rgba(255, 255, 255, 0.92);
                border: 1px solid rgba(17, 24, 39, 0.14);
                box-shadow: 0 8px 18px rgba(17, 24, 39, 0.12);
            }
            .mobile-profile-caret {
                position: absolute;
                right: -2px;
                bottom: -3px;
                width: 16px;
                height: 16px;
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 10px;
                color: #ffffff;
                background: linear-gradient(135deg, #f0a83a, #6f33cc);
                box-shadow: 0 6px 12px rgba(111, 51, 204, 0.24);
                transition: transform 0.2s ease;
            }
            .mobile-profile-dropdown-shell.is-open .mobile-profile-caret {
                transform: rotate(180deg);
            }
            .mobile-profile-dropdown-shell.is-open {
                z-index: 1004;
            }
            .mobile-profile-menu {
                position: fixed;
                top: 64px;
                left: 16px;
                width: min(240px, calc(100vw - 32px));
                min-width: 0;
                padding: 12px;
                border-radius: 18px;
                display: none;
                z-index: 2505;
                opacity: 0;
                pointer-events: none;
                transform: translateY(-8px);
                transition: opacity 0.2s ease, transform 0.2s ease;
                background: rgba(255, 255, 255, 0.98);
                border: 1px solid rgba(17, 24, 39, 0.08);
                box-shadow: 0 18px 36px rgba(17, 24, 39, 0.16);
            }
            .mobile-profile-menu.is-open {
                display: block !important;
                opacity: 1;
                pointer-events: auto;
                transform: translateY(0);
            }
            .mobile-profile-menu a {
                display: flex;
                align-items: center;
                width: 100%;
                padding: 11px 12px;
                border-radius: 12px;
                color: #111827;
                font-weight: 600;
                text-decoration: none;
            }
            .mobile-profile-menu a + a {
                margin-top: 4px;
            }
            .mobile-profile-menu a:hover,
            .mobile-profile-menu a:focus {
                background: linear-gradient(135deg, rgba(var(--secondary-color-rgb), 0.14), rgba(var(--primary-color-rgb), 0.1));
            }
            .mobile-notification-btn {
                position: absolute;
                top: 14px;
                right: 60px;
                width: 38px;
                height: 38px;
                border-radius: 50%;
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                color: #111827;
                background: rgba(255, 255, 255, 0.92);
                border: 1px solid rgba(17, 24, 39, 0.14);
                box-shadow: 0 8px 18px rgba(17, 24, 39, 0.12);
                z-index: 6;
            }
            header.scroll-dark .mobile-notification-btn,
            header.header-mobile .mobile-notification-btn {
                color: #111827;
                background: rgba(255, 255, 255, 0.92);
                border: 1px solid rgba(17, 24, 39, 0.14);
            }
            header.header-mobile:not(.header-light) .mobile-notification-btn {
                color: #ffffff;
                background: rgba(255, 255, 255, 0.08);
                border: 1px solid rgba(255, 255, 255, 0.14);
                box-shadow: none;
            }
            .mobile-notification-btn i {
                font-size: 16px;
            }
            .mobile-notification-btn .notif-badge--floating {
                top: -3px;
                right: -3px;
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
                display: none !important;
            }
            .header-center {
                position: absolute;
                top: calc(100% + 10px);
                left: 12px;
                right: 12px;
                width: auto;
                order: 3;
                display: block;
                padding: 16px 18px;
                max-height: min(70vh, 420px);
                overflow-x: hidden;
                overflow-y: auto;
                opacity: 0;
                visibility: hidden;
                pointer-events: none;
                transform: translateY(-8px);
                background: rgba(255, 255, 255, 0.98);
                border: 1px solid rgba(17, 24, 39, 0.08);
                border-radius: 18px;
                box-shadow: 0 18px 36px rgba(17, 24, 39, 0.16);
                transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s ease;
                z-index: 1250;
            }
            header.header-mobile.mobile-nav-open .header-center,
            header.scroll-dark.header-mobile.mobile-nav-open .header-center,
            header.scroll-light.header-mobile.mobile-nav-open .header-center {
                opacity: 1;
                visibility: visible;
                pointer-events: auto;
                transform: translateY(0);
            }
            .dark-scheme header.header-mobile .header-center,
            header.header-mobile:not(.header-light) .header-center {
                background: rgba(11, 8, 20, 0.96);
                border-color: rgba(255, 255, 255, 0.08);
                box-shadow: 0 18px 36px rgba(0, 0, 0, 0.34);
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
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                gap: 0;
                white-space: normal;
                width: 100%;
            }
            .menu-profile-dropdown {
                display: none !important;
            }
            #mainmenu > li {
                width: 100%;
                border-bottom: 1px solid rgba(17, 24, 39, 0.08);
            }
            .dark-scheme #mainmenu > li,
            header.header-mobile:not(.header-light) #mainmenu > li {
                border-bottom-color: rgba(255, 255, 255, 0.08);
            }
            #mainmenu > li:last-child {
                border-bottom: 0;
            }
            #mainmenu > li > a {
                width: 100%;
            }
            header.header-mobile #mainmenu li a,
            header.scroll-light.header-mobile #mainmenu li a {
                color: #111827 !important;
            }
            .dark-scheme header.header-mobile #mainmenu li a,
            header.header-mobile:not(.header-light) #mainmenu li a {
                color: #f8fafc !important;
            }
            #mainmenu li > a.has-submenu {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            .menu-profile-dropdown > a.has-submenu {
                gap: 10px;
                padding: 14px 16px !important;
                border-radius: 16px;
                background: linear-gradient(135deg, rgba(17, 24, 39, 0.04), rgba(17, 24, 39, 0.08));
                border: 1px solid rgba(17, 24, 39, 0.08);
                box-shadow: 0 8px 18px rgba(17, 24, 39, 0.06);
            }
            .menu-profile-dropdown > a.has-submenu .menu-label {
                font-weight: 700;
            }
            .mobile-dropdown-hint {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                margin-left: auto;
                margin-right: 8px;
                padding: 4px 8px;
                border-radius: 999px;
                background: rgba(17, 24, 39, 0.06);
                color: #6b7280;
                font-size: 10px;
                font-weight: 700;
                line-height: 1;
                letter-spacing: 0.04em;
                text-transform: uppercase;
            }
            .menu-profile-dropdown .dropdown-caret {
                margin-left: 0;
                opacity: 0.9;
                transition: transform 0.2s ease;
            }
            .menu-profile-dropdown.mobile-submenu-open > a.has-submenu {
                background: linear-gradient(135deg, rgba(var(--secondary-color-rgb), 0.14), rgba(var(--primary-color-rgb), 0.12));
                border-color: rgba(17, 24, 39, 0.12);
            }
            .menu-profile-dropdown.mobile-submenu-open .mobile-dropdown-hint {
                color: #111827;
                background: rgba(255, 255, 255, 0.72);
            }
            .menu-profile-dropdown.mobile-submenu-open .dropdown-caret {
                transform: rotate(180deg);
            }
            .menu-profile-dropdown > ul {
                width: 100%;
                padding-top: 10px;
            }
            .menu-profile-dropdown > ul > li {
                width: 100%;
            }
            .menu-profile-dropdown > ul > li > a {
                width: 100%;
                padding-left: 18px !important;
            }
            .menu-notifications-item {
                display: none !important;
            }
            header.header-mobile #mainmenu > li > span,
            header.header-mobile #mainmenu > li > ul > li > span,
            #mainmenu > li > a > span:empty,
            #mainmenu > li > ul > li > a > span:empty {
                display: none !important;
            }
            header.header-mobile .menu-mobile-only,
            header.scroll-light.header-mobile .menu-mobile-only,
            header.scroll-dark.header-mobile .menu-mobile-only {
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
                padding: 12px 0;
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
            .mobile-quick-actions {
                position: fixed;
                left: 50%;
                bottom: 14px;
                transform: translateX(-50%);
                width: min(420px, calc(100vw - 16px));
                height: auto;
                min-height: 78px;
                display: grid;
                grid-template-columns: repeat(5, minmax(0, 1fr));
                gap: 6px;
                padding: 8px;
                border-radius: 22px;
                background: rgba(7, 8, 18, 0.62);
                border: 1px solid rgba(255, 255, 255, 0.08);
                box-shadow: 0 14px 30px rgba(0, 0, 0, 0.18);
                overflow: visible;
                box-sizing: border-box;
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                z-index: 1200;
            }
            .mobile-quick-actions__item {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 4px;
                min-height: 56px;
                padding: 8px 4px;
                border-radius: 16px;
                color: rgba(255, 255, 255, 0.78);
                text-decoration: none;
                font-size: 9px;
                font-weight: 700;
                letter-spacing: 0.01em;
                line-height: 1.15;
                transition: transform 0.18s ease, background 0.18s ease, color 0.18s ease;
            }
            .mobile-quick-actions__item i {
                font-size: 16px;
            }
            .mobile-quick-actions__item:hover,
            .mobile-quick-actions__item:focus {
                color: #ffffff;
                background: rgba(255, 255, 255, 0.08);
                transform: translateY(-1px);
            }
            .mobile-quick-actions__item.is-active {
                color: #ffffff;
                background: transparent;
                box-shadow: none;
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
    @stack('styles')
</head>

<body class="switch-scheme{{ auth()->check() ? ' has-mobile-quick-nav' : '' }}">
    <div id="wrapper">
        <!-- header begin -->
        @include('frontend.partials.header')
        <!-- content begin -->
        <div class="no-bottom no-top" id="content">
            <div id="top"></div>
            @yield('content')
        </div>
        <!-- content close -->

        @auth
            @php
                $menuLabel = function (string $field, string $default) use ($settings) {
                    $value = $settings?->{$field};

                    return filled($value) ? $value : $default;
                };
            @endphp
            <div class="mobile-quick-actions" role="navigation" aria-label="Quick actions">
                <a href="{{ route('dashboard') }}" class="mobile-quick-actions__item {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                    <i class="fa fa-th-large" aria-hidden="true"></i>
                    <span>{{ $menuLabel('nav_mobile_dashboard_label', 'PI') }}</span>
                </a>
                <a href="{{ route('marketplace') }}" class="mobile-quick-actions__item {{ request()->routeIs('marketplace') ? 'is-active' : '' }}">
                    <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                    <span>{{ $menuLabel('nav_mobile_marketplace_label', 'Market') }}</span>
                </a>
                <a href="{{ route('reserve.index') }}" class="mobile-quick-actions__item {{ request()->routeIs('reserve.*') ? 'is-active' : '' }}">
                    <i class="fa fa-lock" aria-hidden="true"></i>
                    <span>{{ $menuLabel('nav_mobile_reserve_label', 'Reserve') }}</span>
                </a>
                <a href="{{ route('stake.index') }}" class="mobile-quick-actions__item {{ request()->routeIs('stake.*', 'staking.*') ? 'is-active' : '' }}">
                    <i class="fa fa-line-chart" aria-hidden="true"></i>
                    <span>{{ $menuLabel('nav_mobile_stake_label', 'Stake') }}</span>
                </a>
                <a href="{{ route('wallet.index') }}" class="mobile-quick-actions__item {{ request()->routeIs('wallet.*') && !request()->routeIs('wallet.deposit*', 'wallet.withdrawals*') ? 'is-active' : '' }}">
                    <i class="fa fa-wallet" aria-hidden="true"></i>
                    <span>{{ $menuLabel('nav_mobile_wallet_label', 'Wallet') }}</span>
                </a>
            </div>
        @endauth

        <a href="#" id="back-to-top"></a>

        <!-- footer begin -->
        <footer>
            @php
                $settings = $settings ?? $siteSetting ?? null;
                $footerLinks = $footerLinks ?? collect();
                $defaults = [
                    'marketplace' => [
                        ['label' => 'All PI', 'url' => route('explore')],
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
            const mobileProfileShell = document.querySelector('.mobile-profile-dropdown-shell');
            const mobileProfileToggle = document.getElementById('mobile-profile-toggle');
            const mobileProfileMenu = document.getElementById('mobile-profile-menu');
            const positionMobileProfileMenu = function () {
                if (!mobileProfileToggle || !mobileProfileMenu || window.innerWidth > 991) {
                    return;
                }
                const rect = mobileProfileToggle.getBoundingClientRect();
                const viewportPadding = 16;
                const menuWidth = Math.min(240, Math.max(180, window.innerWidth - (viewportPadding * 2)));
                const left = Math.min(
                    Math.max(viewportPadding, rect.right - menuWidth),
                    window.innerWidth - menuWidth - viewportPadding
                );

                mobileProfileMenu.style.width = menuWidth + 'px';
                mobileProfileMenu.style.left = left + 'px';
                mobileProfileMenu.style.top = (rect.bottom + 12) + 'px';
                mobileProfileMenu.style.right = 'auto';
            };
            const closeMobileProfileMenu = function () {
                if (!mobileProfileShell || !mobileProfileToggle || !mobileProfileMenu) {
                    return;
                }
                mobileProfileShell.classList.remove('is-open');
                mobileProfileMenu.classList.remove('is-open');
                mobileProfileToggle.setAttribute('aria-expanded', 'false');
            };
            const openMobileProfileMenu = function () {
                if (!mobileProfileShell || !mobileProfileToggle || !mobileProfileMenu) {
                    return;
                }
                positionMobileProfileMenu();
                mobileProfileShell.classList.add('is-open');
                mobileProfileMenu.classList.add('is-open');
                mobileProfileToggle.setAttribute('aria-expanded', 'true');
            };
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
            if (mobileProfileShell && mobileProfileToggle) {
                if (mobileProfileMenu && mobileProfileMenu.parentElement !== document.body) {
                    document.body.appendChild(mobileProfileMenu);
                }
                mobileProfileToggle.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    const isOpen = !mobileProfileShell.classList.contains('is-open');
                    if (isOpen) {
                        openMobileProfileMenu();
                    } else {
                        closeMobileProfileMenu();
                    }
                });
                if (mobileProfileMenu) {
                    mobileProfileMenu.addEventListener('click', function (event) {
                        event.stopPropagation();
                    });
                }
                document.addEventListener('click', function (event) {
                    const clickedInsideShell = mobileProfileShell.contains(event.target);
                    const clickedInsideMenu = mobileProfileMenu && mobileProfileMenu.contains(event.target);
                    if (!clickedInsideShell && !clickedInsideMenu) {
                        closeMobileProfileMenu();
                    }
                });
                window.addEventListener('resize', function () {
                    if (mobileProfileMenu && mobileProfileMenu.classList.contains('is-open')) {
                        positionMobileProfileMenu();
                    }
                });
                window.addEventListener('scroll', function () {
                    if (mobileProfileMenu && mobileProfileMenu.classList.contains('is-open')) {
                        positionMobileProfileMenu();
                    }
                }, true);
            }

            const mobileHeaders = function () {
                return Array.from(document.querySelectorAll('header.header-mobile'));
            };
            const detachThemeMobileMenuHandler = function () {
                if (window.jQuery) {
                    window.jQuery('#menu-btn').off('click');
                }
            };
            const getClosedHeaderHeight = function (header) {
                const bar = header.querySelector('.header-bar');
                return bar ? Math.ceil(bar.getBoundingClientRect().height) : 76;
            };
            const syncMobileHeaderHeight = function (header) {
                if (!header) {
                    return;
                }
                if (window.innerWidth > 991) {
                    header.style.height = '';
                    return;
                }
                header.style.height = getClosedHeaderHeight(header) + 'px';
            };
            const closeMobileHeader = function (header) {
                header.classList.remove('mobile-nav-open');
                syncMobileHeaderHeight(header);
            };
            const openMobileHeader = function (header) {
                header.classList.add('mobile-nav-open');
                syncMobileHeaderHeight(header);
            };
            const toggleMobileHeader = function (header) {
                if (!header) {
                    return;
                }
                const shouldOpen = !header.classList.contains('mobile-nav-open');
                mobileHeaders().forEach(function (item) {
                    if (item !== header) {
                        closeMobileHeader(item);
                    }
                });
                if (shouldOpen) {
                    openMobileHeader(header);
                } else {
                    closeMobileHeader(header);
                }
            };
            const refreshMobileHeaderState = function () {
                if (window.innerWidth > 991) {
                    mobileHeaders().forEach(function (header) {
                        header.classList.remove('mobile-nav-open');
                        syncMobileHeaderHeight(header);
                    });
                    return;
                }

                mobileHeaders().forEach(function (header) {
                    syncMobileHeaderHeight(header);
                });
            };
            detachThemeMobileMenuHandler();
            document.addEventListener('click', function (event) {
                if (window.innerWidth > 991) {
                    return;
                }

                const menuButton = event.target.closest('#menu-btn');
                if (menuButton) {
                    event.preventDefault();
                    event.stopPropagation();
                    if (typeof event.stopImmediatePropagation === 'function') {
                        event.stopImmediatePropagation();
                    }
                    const activeHeader = menuButton.closest('header.header-mobile') || mobileHeaders()[0];
                    toggleMobileHeader(activeHeader);
                    return;
                }

                const clickedInsideMenuPanel = event.target.closest('header.header-mobile .header-center');
                const clickedInsideHeaderControls = event.target.closest('header.header-mobile .header-left');
                if (!clickedInsideMenuPanel && !clickedInsideHeaderControls) {
                    mobileHeaders().forEach(function (header) {
                        closeMobileHeader(header);
                    });
                }
            }, true);

            window.addEventListener('resize', refreshMobileHeaderState);
            window.addEventListener('load', function () {
                detachThemeMobileMenuHandler();
                refreshMobileHeaderState();
            });
            window.setTimeout(function () {
                detachThemeMobileMenuHandler();
                refreshMobileHeaderState();
            }, 120);
            refreshMobileHeaderState();

            const submenuLinks = document.querySelectorAll('#mainmenu > li > a.has-submenu');
            submenuLinks.forEach(function (link) {
                link.addEventListener('click', function (event) {
                    if (window.innerWidth <= 991) {
                        event.preventDefault();
                        const parentItem = link.parentElement;
                        if (parentItem) {
                            parentItem.classList.toggle('mobile-submenu-open');
                            const hint = link.querySelector('.mobile-dropdown-hint');
                            if (hint) {
                                hint.textContent = parentItem.classList.contains('mobile-submenu-open') ? 'Tap to close' : 'Tap to open';
                            }
                        }
                        const toggle = link.parentElement.querySelector(':scope > span');
                        if (toggle) {
                            toggle.click();
                        }
                        window.setTimeout(refreshMobileHeaderState, 20);
                    }
                });
            });

            document.querySelectorAll('#mainmenu > li > span, #mainmenu > li > ul > li > span').forEach(function (toggle) {
                toggle.addEventListener('click', function () {
                    window.setTimeout(refreshMobileHeaderState, 20);
                });
            });

            const passwordToggles = document.querySelectorAll('[data-password-toggle]');
            passwordToggles.forEach(function (toggle) {
                toggle.addEventListener('click', function () {
                    const wrapper = toggle.closest('.password-input');
                    const input = wrapper ? wrapper.querySelector('input') : null;
                    if (!input) {
                        return;
                    }
                    const isPassword = input.type === 'password';
                    input.type = isPassword ? 'text' : 'password';
                    toggle.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
                    const icon = toggle.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('fa-eye', !isPassword);
                        icon.classList.toggle('fa-eye-slash', isPassword);
                    }
                });
            });
        });
    </script>
    @stack('scripts')

    @if (!empty($popupNotification) && $popupNotification->notification)
        @php
            $notificationMeta = $popupNotification->notification->metadata ?? [];
            $popupIcon = $notificationMeta['popup_icon'] ?? null;
            $popupActionType = $notificationMeta['action_type'] ?? null;
            $popupActionLabel = $notificationMeta['action_label'] ?? null;
            $popupActionUrl = $notificationMeta['action_url'] ?? null;
            $popupActionTarget = $notificationMeta['action_target'] ?? null;
        @endphp
        <div class="popup-backdrop" id="notif-modal">
            <div class="popup-card">
                @if ($popupIcon === 'pi')
                    <div class="popup-card__icon">
                        <img src="{{ asset('frontend/images/icon.png') }}" alt="PI">
                    </div>
                @endif
                <h3>{{ $popupNotification->notification->title }}</h3>
                <p>{{ $popupNotification->notification->message }}</p>
                <div class="popup-actions">
                    @if ($popupActionLabel && $popupActionType === 'open_modal' && $popupActionTarget)
                        <button type="button" class="btn-main" data-popup-modal-target="{{ $popupActionTarget }}">{{ $popupActionLabel }}</button>
                    @elseif ($popupActionLabel && $popupActionUrl)
                        <a href="{{ $popupActionUrl }}" class="btn-main">{{ $popupActionLabel }}</a>
                    @endif
                    <form method="POST" action="{{ route('notifications.dismiss', $popupNotification->notification) }}">
                        @csrf
                        <button type="submit" class="btn-border">Dismiss</button>
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
                    modal.querySelectorAll('[data-popup-modal-target]').forEach(function (button) {
                        button.addEventListener('click', function () {
                            var targetId = button.getAttribute('data-popup-modal-target');
                            if (!targetId) {
                                return;
                            }
                            var target = document.getElementById(targetId);
                            if (target) {
                                modal.style.display = 'none';
                                target.classList.add('is-visible');
                                target.setAttribute('aria-hidden', 'false');
                                document.body.classList.add('reserve-modal-open');
                            }
                        });
                    });
                }
            });
        </script>
    @endif
</body>

</html>

