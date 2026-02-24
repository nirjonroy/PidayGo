<!DOCTYPE html>
<html lang="zxx">

<head>
    @php
        $settings = $settings ?? $siteSetting ?? null;
        $siteName = $settings?->site_name ?: config('app.name', 'PidayGo');
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
    <style>
        #logo img.logo,
        #logo img.logo-2 {
            height: 46px;
            width: auto;
        }
        @media (max-width: 991.98px) {
            #logo img.logo,
            #logo img.logo-2 {
                height: 38px;
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
            flex-wrap: wrap;
        }
        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
            justify-content: flex-end;
            min-width: 220px;
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
        @media (max-width: 991.98px) {
            .header-center {
                display: none;
            }
            .header-left {
                flex: 1;
            }
            .header-right {
                min-width: auto;
            }
        }
    </style>
</head>

<body class="switch-scheme dark-scheme">
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
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-sm-6 col-xs-1">
                        <div class="widget">
                            <h5>Get the latest updates</h5>
                            <p>Signup for our newsletter to get the latest updates in your inbox.</p>
                            <form action="#" class="row form-dark" id="form_subscribe" method="post" name="form_subscribe">
                                <div class="col text-center">
                                    <input class="form-control" id="txt_subscribe" name="txt_subscribe" placeholder="enter your email" type="text" />
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
                                        <li><a href="#">All NFTs</a></li>
                                        <li><a href="#">Art</a></li>
                                        <li><a href="#">Music</a></li>
                                        <li><a href="#">Domain Names</a></li>
                                        <li><a href="#">Virtual World</a></li>
                                        <li><a href="#">Collectibles</a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-lg-4 col-sm-6 col-xs-1">
                                <div class="widget">
                                    <h5>Resources</h5>
                                    <ul>
                                        <li><a href="#">Help Center</a></li>
                                        <li><a href="#">Partners</a></li>
                                        <li><a href="#">Suggestions</a></li>
                                        <li><a href="#">Discord</a></li>
                                        <li><a href="#">Docs</a></li>
                                        <li><a href="#">Newsletter</a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-lg-4 col-sm-6 col-xs-1">
                                <div class="widget">
                                    <h5>Community</h5>
                                    <ul>
                                        <li><a href="#">Community</a></li>
                                        <li><a href="#">Documentation</a></li>
                                        <li><a href="#">Brand Assets</a></li>
                                        <li><a href="#">Blog</a></li>
                                        <li><a href="#">Forum</a></li>
                                        <li><a href="#">Mailing List</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6 col-xs-1">
                        <div class="widget">
                            <h5>Join the community</h5>
                            <div class="social-icons">
                                <a href="#"><i class="fa fa-facebook fa-lg"></i></a>
                                <a href="#"><i class="fa fa-twitter fa-lg"></i></a>
                                <a href="#"><i class="fa fa-instagram fa-lg"></i></a>
                                <a href="#"><i class="fa fa-youtube fa-lg"></i></a>
                                <a href="#"><i class="fa fa-envelope-o fa-lg"></i></a>
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
                                <span class="copy">&copy; Copyright 2026 - PidayGo</span>
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
