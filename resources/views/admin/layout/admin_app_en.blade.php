<!DOCTYPE html>
<html lang="en">

<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="CoreUI - Open Source Bootstrap Admin Template">
    <meta name="author" content="Łukasz Holeczek">
    <meta name="keyword" content="Bootstrap,Admin,Template,Open,Source,jQuery,CSS,HTML,RWD,Dashboard">
    <title>Qarenly</title>
    <link rel="apple-touch-icon" sizes="57x57" href={{ asset('/public/admin/assets/favicon/apple-icon-57x57.png') }}>
    <link rel="apple-touch-icon" sizes="60x60" href={{ asset('/public/admin/assets/favicon/apple-icon-60x60.png') }}>
    <link rel="apple-touch-icon" sizes="72x72" href={{ asset('/public/admin/assets/favicon/apple-icon-72x72.png') }}>
    <link rel="apple-touch-icon" sizes="76x76" href={{ asset('/public/admin/assets/favicon/apple-icon-76x76.png') }}>
    <link rel="apple-touch-icon" sizes="114x114"
        href={{ asset('/public/admin/assets/favicon/apple-icon-114x114.png') }}>
    <link rel="apple-touch-icon" sizes="120x120"
        href={{ asset('/public/admin/assets/favicon/apple-icon-120x120.png') }}>
    <link rel="apple-touch-icon" sizes="144x144"
        href={{ asset('/public/admin/assets/favicon/apple-icon-144x144.png') }}>
    <link rel="apple-touch-icon" sizes="152x152"
        href={{ asset('/public/admin/assets/favicon/apple-icon-152x152.png') }}>
    <link rel="apple-touch-icon" sizes="180x180"
        href={{ asset('/public/admin/assets/favicon/apple-icon-180x180.png') }}>
    <link rel="icon" type="image/png" sizes="192x192"
        href={{ asset('/public/admin/assets/favicon/android-icon-192x192.png') }}>
    <link rel="icon" type="image/png" sizes="32x32"
        href={{ asset('/public/admin/assets/favicon/favicon-32x32.png') }}>
    <link rel="icon" type="image/png" sizes="96x96"
        href={{ asset('/public/admin/assets/favicon/favicon-96x96.png') }}>
    <link rel="icon" type="image/png" sizes="16x16"
        href={{ asset('/public/admin/assets/favicon/favicon-16x16.png') }}>
    <link rel="manifest" href={{ asset('/public/admin/assets/favicon/manifest.json') }}>
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content={{ asset('/public/admin/assets/favicon/ms-icon-144x144.png') }}>
    <meta name="theme-color" content="#ffffff">
    <!-- Vendors styles-->
    <link rel="stylesheet" href={{ asset('/public/admin/vendors/simplebar/css/simplebar.css') }}>
    <link rel="stylesheet" href={{ asset('/public/admin/css/vendors/simplebar.css') }}>
    <!-- Main styles for this application-->
    <link href={{ asset('/public/admin/css/style.css') }} rel="stylesheet">
    <!-- We use those styles to show code examples, you should remove them in your application.-->
    {{-- <link href={{ asset('/public/admin/css/examples.css') }} rel="stylesheet"> --}}
    <script src={{ asset('/public/admin/js/config.js') }}></script>
    <script src={{ asset('/public/admin/js/color-modes.js') }}></script>
    <link href={{ url('/public/admin/vendors/@coreui/chartjs/css/coreui-chartjs.css') }} rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    @yield('style')
</head>

<body>
    <div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
        <div class="sidebar-header border-bottom align-items-center justify-content-center">
            <div class="sidebar-brand">
                <img src={{ asset('/public/admin/assets/brand/sisitravel_logo.png') }} class="sidebar-brand-full"
                    width="118" height="46" alt="CoreUI Logo">
            </div>
            <button class="btn-close d-lg-none" type="button" data-coreui-theme="dark" aria-label="Close"
                onclick="coreui.Sidebar.getInstance(document.querySelector(&quot;#sidebar&quot;)).toggle()"></button>
        </div>
        <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">
                    <svg class="nav-icon">
                        <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-speedometer"></use>
                    </svg> Dashboard</a>
            </li>

            <li class="nav-title">General</li>
            <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('/public/admin/vendors/@coreui/icons/svg/free.svg') }}#cil-puzzle">
                        </use>
                    </svg> Hotels</a>
                <ul class="nav-group-items compact">
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.hotel.index') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> All Hotels</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.hotel.create') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> Add Hotel</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.agents.index') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> All Agents</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.agents.create') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> Add Agents</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.ar.index') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> Availability Requests</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.ar.create') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> Add Availability Request</a></li>

                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.hotel_rates.index') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> All Hotel Rates</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.hotel_rates.create') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> Create Hotel Rates</a></li>

                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.hotel.book') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> Book Hotel</a></li>
                </ul>
            </li>


            <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                    <svg class="nav-icon">
                        <use
                            xlink:href="{{ asset('/public/admin/vendors/@coreui/coreui-icons-master/sprites/free.svg') }}#cil-flight-takeoff">
                        </use>
                    </svg> Flights</a>
                <ul class="nav-group-items compact">
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.hotel.index') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> All Flights</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.hotel.index') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> Add Flights</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.hotel.add') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> Add Airport</a></li>
                </ul>
            </li>


            <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('/public/admin/vendors/@coreui/icons/svg/free.svg') }}#cil-user">
                        </use>
                    </svg> Client</a>
                <ul class="nav-group-items compact">
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.hotel.index') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> All Clients</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.hotel.index') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> Add Client</a></li>
                </ul>
            </li>

            <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('/public/admin/vendors/@coreui/icons/svg/free.svg') }}#cil-puzzle">
                        </use>
                    </svg> Branchs</a>
                <ul class="nav-group-items compact">
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.branches.index') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> All Branchs</a></li>
                    {{-- <li class="nav-item"><a class="nav-link" href="{{ route('admin.branches.create') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> Add Branch</a></li> --}}
                </ul>
            </li>

            <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('/public/admin/vendors/@coreui/icons/svg/free.svg') }}#cil-short-text">
                        </use>
                    </svg> Programs</a>
                <ul class="nav-group-items compact">
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.programs.index') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> All Programs</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.programs.create') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> Add Program</a></li>
                </ul>
            </li>

            <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('/public/admin/vendors/@coreui/icons/svg/free.svg') }}#cil-cursor-move">
                        </use>
                    </svg> Tours</a>
                <ul class="nav-group-items compact">
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.tours.index') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> All Tours</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.tours.create') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> Add Tour</a></li>
                </ul>
            </li>

            <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('/public/admin/vendors/@coreui/icons/svg/free.svg') }}#cil-cursor-move">
                        </use>
                    </svg> Booking</a>
                <ul class="nav-group-items compact">
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.bookings.index') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> All Bookings</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.bookings.create') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> Add Booking</a></li>
                </ul>
            </li>

            <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('/public/admin/vendors/@coreui/icons/svg/free.svg') }}#cil-spreadsheet">
                        </use>
                    </svg> Reports</a>
                <ul class="nav-group-items compact">
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.tours.index') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> All Tours</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.tours.create') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> Add Tour</a></li>
                    {{-- <li class="nav-item"><a class="nav-link" href="{{ route('admin.tours.offer.edit') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> All Tours Offers</a></li> --}}
                    {{-- <li class="nav-item"><a class="nav-link" href="{{ route('admin.tours.offer.create') }}"><span
                                class="nav-icon"><span class="nav-icon-bullet"></span></span> Add Tour Offer</a></li> --}}
                </ul>
            </li>
























            <li class="nav-item"><a class="nav-link" href="{{ route('admin.flightsbooks') }}"><span
                        class="nav-icon"><span class="nav-icon-bullet"></span></span>Flights</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.hotelsbr') }}"><span
                        class="nav-icon"><span class="nav-icon-bullet"></span></span>Hotels Book Request</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.hotelsm') }}"><span
                        class="nav-icon"><span class="nav-icon-bullet"></span></span>Hotels Management</a></li>








            {{-- <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                    <svg class="nav-icon">
                        <use xlink:href="{{asset('/public/admin/vendors/@coreui/icons/svg/free.svg')}}#cil-puzzle"></use>
                    </svg> </a>
                <ul class="nav-group-items compact">
                    <li class="nav-item"><a class="nav-link" href="{{route('admin.user.index')}}"><span class="nav-icon"><span
                                    class="nav-icon-bullet"></span></span> All Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{route('admin.user.add')}}"><span class="nav-icon"><span
                                    class="nav-icon-bullet"></span></span> Add User</a></li>
                </ul>
            </li> --}}
            {{-- <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                    <svg class="nav-icon">
                        <use xlink:href="{{asset('/public/admin/vendors/@coreui/icons/svg/free.svg')}}#cil-puzzle"></use>
                    </svg> Users</a>
                <ul class="nav-group-items compact">
                    <li class="nav-item"><a class="nav-link" href="{{route('admin.user.index')}}"><span class="nav-icon"><span
                                    class="nav-icon-bullet"></span></span> All Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{route('admin.user.add')}}"><span class="nav-icon"><span
                                    class="nav-icon-bullet"></span></span> Add User</a></li>
                </ul>
            </li> --}}
            {{-- <li class="nav-title">Me</li> --}}

            {{-- <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                    <svg class="nav-icon">
                        <use xlink:href="{{asset('/public/admin/vendors/@coreui/icons/svg/free.svg')}}#cil-puzzle"></use>
                    </svg> Rides</a>
                <ul class="nav-group-items compact">
                    <li class="nav-item"><a class="nav-link" href="{{route('admin.rides.index')}}"><span class="nav-icon"><span
                                    class="nav-icon-bullet"></span></span> All Rides</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{route('admin.ride.add')}}"><span class="nav-icon"><span
                                    class="nav-icon-bullet"></span></span> Add Ride</a></li>
                </ul>
            </li> --}}
            {{-- <li class="nav-title">Me</li> --}}

            {{-- <li class="nav-item"><a class="nav-link" href="{{route('admin.send_notification_by_topic_get')}}"><span class="nav-icon"><span
                                    class="nav-icon-bullet"></span></span> push notify to all</a></li> --}}
            {{-- <li class="nav-item"><a class="nav-link" href="{{route('admin.map_settings')}}"><span class="nav-icon"><span
                                class="nav-icon-bullet"></span></span> Map</a></li> --}}
            {{-- <li class="nav-item"><a class="nav-link" href="{{route('admin.privacy_policy')}}"><span class="nav-icon"><span
                                class="nav-icon-bullet"></span></span> Privacy Policy</a></li>
                <li class="nav-item"><a class="nav-link" href="{{route('admin.add_payment')}}"><span class="nav-icon"><span
                                class="nav-icon-bullet"></span></span> Payments</a></li> --}}
            {{-- <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="{{asset('/public/admin/vendors/@coreui/icons/svg/free.svg')}}#cil-puzzle"></use>
                </svg> Settings</a>
            <ul class="nav-group-items compact">
            </ul>
        </li> --}}


        </ul>
        <div class="sidebar-footer border-top d-none d-md-flex">
            <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
        </div>
    </div>
    <div class="wrapper d-flex flex-column min-vh-100">
        <header class="header header-sticky p-0 mb-4">
            <div class="container-fluid border-bottom px-4">
                <button class="header-toggler" type="button"
                    onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()"
                    style="margin-inline-start: -14px;">
                    <svg class="icon icon-lg">
                        <use xlink:href="{{ asset('/public/admin/vendors/@coreui/icons/svg/free.svg') }}#cil-menu">
                        </use>
                    </svg>
                </button>
                {{-- <ul class="header-nav d-none d-lg-flex">
                    <li class="nav-item"><a class="nav-link" href="#">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Settings</a></li>
                </ul> --}}
                {{-- <ul class="header-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#">
                            <svg class="icon icon-lg">
                                <use xlink:href="{{asset('/public/admin/vendors/@coreui/icons/svg/free.svg')}}#cil-bell"></use>
                            </svg></a></li>
                    <li class="nav-item"><a class="nav-link" href="#">
                            <svg class="icon icon-lg">
                                <use xlink:href="{{asset('/public/admin/vendors/@coreui/icons/svg/free.svg')}}#cil-list-rich"></use>
                            </svg></a></li>
                    <li class="nav-item"><a class="nav-link" href="#">
                            <svg class="icon icon-lg">
                                <use xlink:href="{{asset('/public/admin/vendors/@coreui/icons/svg/free.svg')}}#cil-envelope-open"></use>
                            </svg></a></li>
                </ul> --}}
                <ul class="header-nav">
                    <li class="nav-item py-1">
                        <div class="vr h-100 mx-2 text-body text-opacity-75"></div>
                    </li>
                    <li class="nav-item dropdown">
                        <button class="btn btn-link nav-link py-2 px-2 d-flex align-items-center" type="button"
                            aria-expanded="false" data-coreui-toggle="dropdown">
                            <svg class="icon icon-lg theme-icon-active">
                                <use
                                    xlink:href="{{ asset('/public/admin/vendors/@coreui/icons/svg/free.svg') }}#cil-contrast">
                                </use>
                            </svg>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" style="--cui-dropdown-min-width: 8rem;">
                            <li>
                                <button class="dropdown-item d-flex align-items-center" type="button"
                                    data-coreui-theme-value="light">
                                    <svg class="icon icon-lg me-3">
                                        <use
                                            xlink:href="{{ asset('/public/admin/vendors/@coreui/icons/svg/free.svg') }}#cil-sun">
                                        </use>
                                    </svg>Light
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item d-flex align-items-center" type="button"
                                    data-coreui-theme-value="dark">
                                    <svg class="icon icon-lg me-3">
                                        <use
                                            xlink:href="{{ asset('/public/admin/vendors/@coreui/icons/svg/free.svg') }}#cil-moon">
                                        </use>
                                    </svg>Dark
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item d-flex align-items-center active" type="button"
                                    data-coreui-theme-value="auto">
                                    <svg class="icon icon-lg me-3">
                                        <use
                                            xlink:href="{{ asset('/public/admin/vendors/@coreui/icons/svg/free.svg') }}#cil-contrast">
                                        </use>
                                    </svg>Auto
                                </button>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item py-1">
                        <div class="vr h-100 mx-2 text-body text-opacity-75"></div>
                    </li>
                    <li class="nav-item dropdown"><a class="nav-link py-0 pe-0" data-coreui-toggle="dropdown"
                            href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            <div class="avatar avatar-md"><img class="avatar-img"
                                    src="{{ asset('/public/admin/assets/img/avatars/Female-Avatar.png') }}"
                                    alt="user@email.com"></div>
                        </a>
                        {{-- <div class="dropdown-menu dropdown-menu-end pt-0">
                            <div
                                class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold rounded-top mb-2">
                                Account</div><a class="dropdown-item" href="#">
                                <svg class="icon me-2">
                                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-bell"></use>
                                </svg> Updates<span class="badge badge-sm bg-info ms-2">42</span></a><a
                                class="dropdown-item" href="#">
                                <svg class="icon me-2">
                                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-envelope-open"></use>
                                </svg> Messages<span class="badge badge-sm bg-success ms-2">42</span></a><a
                                class="dropdown-item" href="#">
                                <svg class="icon me-2">
                                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-task"></use>
                                </svg> Tasks<span class="badge badge-sm bg-danger ms-2">42</span></a><a
                                class="dropdown-item" href="#">
                                <svg class="icon me-2">
                                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-comment-square"></use>
                                </svg> Comments<span class="badge badge-sm bg-warning ms-2">42</span></a>
                            <div class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold my-2">
                                <div class="fw-semibold">Settings</div>
                            </div><a class="dropdown-item" href="#">
                                <svg class="icon me-2">
                                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-user"></use>
                                </svg> Profile</a><a class="dropdown-item" href="#">
                                <svg class="icon me-2">
                                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-settings"></use>
                                </svg> Settings</a><a class="dropdown-item" href="#">
                                <svg class="icon me-2">
                                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-credit-card"></use>
                                </svg> Payments<span class="badge badge-sm bg-secondary ms-2">42</span></a><a
                                class="dropdown-item" href="#">
                                <svg class="icon me-2">
                                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-file"></use>
                                </svg> Projects<span class="badge badge-sm bg-primary ms-2">42</span></a>
                            <div class="dropdown-divider"></div><a class="dropdown-item" href="#">
                                <svg class="icon me-2">
                                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-lock-locked"></use>
                                </svg> Lock Account</a><a class="dropdown-item" href="#">
                                <svg class="icon me-2">
                                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-account-logout"></use>
                                </svg> Logout</a>
                        </div> --}}
                    </li>
                </ul>
            </div>
            <div class="container-fluid px-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb my-0">
                        <li class="breadcrumb-item"><a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item active"><span>Dashboard</span>
                        </li>
                    </ol>
                </nav>
            </div>
        </header>
        <div class="body flex-grow-1">
            <div class="container-lg px-4">
                @yield('content')
            </div>
        </div>
        <footer class="footer px-4">
            {{-- <div><a href="https://coreui.io">CoreUI </a><a
                    href="https://coreui.io/product/free-bootstrap-admin-template/">Bootstrap Admin Template</a> © 2025
                creativeLabs.</div>
            <div class="ms-auto">Powered by&nbsp;<a href="https://coreui.io/docs/">CoreUI UI Components</a></div> --}}
        </footer>
    </div>
    <!-- CoreUI and necessary plugins-->
    <script></script>

    <script src={{ asset('/public/admin/vendors/@coreui/coreui/js/coreui.bundle.min.js') }}></script>
    <script src={{ asset('/public/admin/vendors/simplebar/js/simplebar.min.js') }}></script>
    <script>
        const header = document.querySelector('header.header');

        document.addEventListener('scroll', () => {
            if (header) {
                header.classList.toggle('shadow-sm', document.documentElement.scrollTop > 0);
            }
        });
    </script>
    <!-- Plugins and scripts required by this view-->
    <script src={{ asset('/public/admin/vendors/chart.js/js/chart.umd.js') }}></script>
    <script src={{ asset('/public/admin/vendors/@coreui/chartjs/js/coreui-chartjs.js') }}></script>
    <script src={{ asset('/public/admin/vendors/@coreui/utils/js/index.js') }}></script>
    <script src={{ asset('/public/admin/js/main.js') }}></script>
    <script></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    @yield('scripts')
</body>

</html>
