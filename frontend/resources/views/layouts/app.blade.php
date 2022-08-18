<?php
use App\Http\Controllers\HomeController;
?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Optin - Genion</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/genion-icon.png') }}">

    <link rel="stylesheet" href="{{ asset('site/style.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert/sweetalert.css') }}">

    <script src="{{ asset('site/js/app.js') }}" ></script>
    <style>
        #dynamic-content > div {
            display: block;
        }
    </style>
    @yield('styles')

</head>
<body>
    @php
        $application = HomeController::startapplication();
        $menus = $application['data']['menu'];
        $user = $application['data']['user'];
    @endphp
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>


    <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
        <header class="topbar" data-navbarbg="skin6">
            <nav class="navbar top-navbar navbar-expand-md">
                <div class="navbar-header" data-logobg="skin6">
                    <!-- This is for the sidebar toggle which is visible on mobile only -->
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)">
                        <i class="ti-menu ti-close"></i>
                    </a>
                    <!-- ============================================================== -->
                    <!-- Logo -->
                    <!-- ============================================================== -->
                    <div class="navbar-brand">
                        <!-- Logo icon -->
                        <a href="{{route('layout')}}">
                            <b class="logo-icon">
                                <!-- Dark Logo icon -->
                                <img src="{{ asset('assets/images/genion-icon.png') }}" alt="homepage" class="dark-logo-icon" />
                                <!-- Light Logo icon -->
                                <img src="{{ asset('assets/images/genion-icon.png') }}" alt="homepage" class="light-logo" />
                            </b>
                            <!--End Logo icon -->
                            <!-- Logo text -->
                            <span class="logo-text">
                                <!-- dark Logo text -->
                                <img src="{{ asset('assets/images/genion-text.png') }}" alt="homepage" class="dark-logo"/>
                                <!-- Light Logo text -->
                                <img src="{{ asset('assets/images/genion-text.png') }}" class="light-logo" alt="homepage" />
                            </span>
                        </a>
                    </div>
                    <!-- ============================================================== -->
                    <!-- End Logo -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- Toggle which is visible on mobile only -->
                    <!-- ============================================================== -->
                    <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="ti-more"></i>
                    </a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse collapse" id="navbarSupportedContent">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav float-left mr-auto ml-3 pl-1">
                        <!-- ============================================================== -->
                        <!-- create new -->
                        <!-- ============================================================== -->
                    </ul>
                    <!-- ============================================================== -->
                    <!-- Right side toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav float-right">
                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <span class="text-dark">{{$user['name']}}</span>
                                <i data-feather="chevron-down" class="svg-icon"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                                <a class="dropdown-item" onclick="changepage('{{route('profile.index')}}', 'none')"><i data-feather="user"
                                        class="svg-icon mr-2 ml-1"></i>
                                    Meu Perfil</a>
                                <a class="dropdown-item" href="{{route('logout')}}"><i data-feather="power"
                                        class="svg-icon mr-2 ml-1"></i>
                                    Sair</a>
                            </div>
                        </li>
                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                    </ul>
                </div>
            </nav>
        </header>

        <aside class="left-sidebar" data-sidebarbg="skin6">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar" data-sidebarbg="skin6">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        @foreach ($menus as $menu)
                            <li class="sidebar-item" id="sidebar-item-{{$menu['id']}}" onclick="changepage('{{URL::to($menu['href'])}}', this)" style="cursor: pointer;"> 
                                    @if ($menu['locate-icon'] == 'fontawesome')
                                        <a class="sidebar-link" id="sidebar-link-{{$menu['id']}}" aria-expanded="false">
                                            <i class="{{$menu['icon']}}"></i>
                                            <span class="hide-menu">{{$menu['name']}}</span>
                                        </a>
                                    @else
                                        @if ($menu['locate-icon'] == 'feather-icon')
                                            <a class="sidebar-link" id="sidebar-link-{{$menu['id']}}" aria-expanded="false">
                                                <i data-feather="{{$menu['icon']}}" class="feather-icon"></i>
                                                <span class="hide-menu">{{$menu['name']}}</span>
                                            </a>
                                        @else
                                            <li class="nav-small-cap"><span class="hide-menu">{{$menu['name']}}</span></li>
                                        @endif
                                    @endif
                            </li>
                        @endforeach
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>

        <div id="dynamic-content">
            {{-- @yield('content') --}}
        </div>

    </div>

    <!-- Chat Movidesk -->
    <script type="text/javascript">var mdChatClient="A8AA6B8FF9D24054BA76055C1CDF9563";</script>
    <script src="https://chat.movidesk.com/Scripts/chat-widget.min.js"></script>
    <!-- Chat do Movidesk fim -->
    <script src="{{ asset('assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js') }}" ></script>
    <script src="{{ asset('site/dist/js/app-style-switcher.js') }}" ></script>
    <script src="{{ asset('assets/extra-libs/datatables.net/js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('site/dist/js/feather.min.js') }}" ></script>
    <script src="{{ asset('site/dist/js/sidebarmenu.js') }}" ></script>
    <script src="{{ asset('site/dist/js/custom.js') }}" ></script>
    <script src="{{ asset('assets/plugins/sweetalert/sweetalert-dev.js') }}"></script>
    <!--Custom JavaScript -->

    <script>
        var elementSelected = $("#sidebar-item-{{$firstMenus['id']}}");
        changepage();
        function changepage(url = "{{URL::to($firstMenus['href'])}}", element = elementSelected) {
            $(".preloader").fadeIn();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            data = {
                'validate_loged': 'loged',
                'request_server': 1
            }
            $('.daterangepicker').each(function(i, e){
                $(e).remove()
            })
            $.ajax({
                type: 'GET',
                url: url,
                data: data,
                success: function(result) {
                    if (result == 200) {
                        location.reload();
                        return false;
                    }
                    if (result.error == 500) {
                        swal(
                            `${result.data.title}`,
                            `${result.data.message}`,
                            'error'
                        );
                        $(".preloader").fadeOut();
                        return false;
                    }
                    $('#dynamic-content').html(result);
                    if (element) {
                        $('.sidebar-item').each(function(i, e){
                            $(e).removeClass('selected');                        
                        })
                        if (element != "none") {
                            $(element).addClass('selected');                        
                            elementSelected = element;
                        }
                    }
                    $(".preloader").fadeOut();
                    $(window).scrollTop(0);
                },
                error: function (result) {
                    $(".preloader").fadeOut();
                    swal(
                        `${result.status}`,
                        result.statusText,
                        'error'
                    );
                }
            })
        }
    </script>
    @yield('scripts')
</body>
</html>
