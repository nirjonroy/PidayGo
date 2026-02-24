<!DOCTYPE html>
<html lang="zxx">

<head>
    @php
        $logoLight = $siteSetting?->logo_light_path ? asset('storage/' . $siteSetting->logo_light_path) : asset('frontend/images/logo-7-light.png');
        $logoDark = $siteSetting?->logo_dark_path ? asset('storage/' . $siteSetting->logo_dark_path) : asset('frontend/images/logo-7.png');
        $siteName = $siteSetting?->site_name ?: config('app.name', 'PidayGo');
    @endphp
    <title>{{ $siteName }}</title>
    @if ($siteSetting?->favicon_path)
        <link rel="icon" href="{{ asset('storage/' . $siteSetting->favicon_path) }}">
    @else
        <link rel="icon" href="{{ asset('frontend/images/icon.png') }}">
    @endif
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
    </style>
</head>

<body class="switch-scheme dark-scheme">
    <div id="wrapper">
        <!-- header begin -->
        <header class="scroll-dark">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="de-flex sm-pt10">
                            <div class="de-flex-col">
                                <div class="de-flex-col">
                                    <!-- logo begin -->
                                    <div id="logo">
                                        <a href="{{ route('home') }}">
                                            <img alt="" class="logo" src="{{ $logoLight }}" />
                                            <img alt="" class="logo-2" src="{{ $logoDark }}" />
                                        </a>
                                    </div>
                                    <!-- logo close -->
                                </div>
                                <div class="de-flex-col">
                                    <input id="quick_search" class="xs-hide" name="quick_search" placeholder="search item here..." type="text" />
                                </div>
                            </div>
                            <div class="de-flex-col header-col-mid">
                                <!-- mainmenu begin -->
                                <ul id="mainmenu">
                                    <li><a href="{{ route('home') }}">Home<span></span></a></li>
                                    <li><a href="{{ route('explore') }}">Explore<span></span></a></li>
                                    <li><a href="{{ route('rankings') }}">Rankings<span></span></a></li>
                                    @auth
                                        <li><a href="{{ route('dashboard') }}">Dashboard<span></span></a></li>
                                        <li><a href="{{ route('profile') }}">Profile<span></span></a></li>
                                        <li><a href="{{ route('wallet.index') }}">Wallet<span></span></a></li>
                                        <li><a href="{{ route('stake.index') }}">Stake<span></span></a></li>
                                        <li><a href="{{ route('notifications.index') }}">Notifications<span></span></a></li>
                                        <li><a href="{{ route('support.index') }}">Support<span></span></a></li>
                                    @endauth
                                </ul>
                                <div class="menu_side_area">
                                    @auth
                                        <a href="{{ route('wallet.index') }}" class="btn-main btn-wallet"><i class="icon_wallet_alt"></i><span>Wallet</span></a>
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
                    </div>
                </div>
            </div>
        </header>
        <!-- header close -->
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
</body>

</html>
