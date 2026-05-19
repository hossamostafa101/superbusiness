<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="لوحة تحكم سونيك">
    <meta name="author" content="Łukasz Holeczek">
    <meta name="keyword" content="Bootstrap,Admin,Template,Open,Source,jQuery,CSS,HTML,RWD,Dashboard">
    <title>سونيك</title>

    <link rel="apple-touch-icon" sizes="57x57" href={{ asset('/backend/assets/favicon/apple-icon-57x57.png') }}>
    <link rel="apple-touch-icon" sizes="60x60" href={{ asset('/backend/assets/favicon/apple-icon-60x60.png') }}>
    <link rel="apple-touch-icon" sizes="72x72" href={{ asset('/backend/assets/favicon/apple-icon-72x72.png') }}>
    <link rel="apple-touch-icon" sizes="76x76" href={{ asset('/backend/assets/favicon/apple-icon-76x76.png') }}>
    <link rel="apple-touch-icon" sizes="114x114" href={{ asset('/backend/assets/favicon/apple-icon-114x114.png')
        }}>
    <link rel="apple-touch-icon" sizes="120x120" href={{ asset('/backend/assets/favicon/apple-icon-120x120.png')
        }}>
    <link rel="apple-touch-icon" sizes="144x144" href={{ asset('/backend/assets/favicon/apple-icon-144x144.png')
        }}>
    <link rel="apple-touch-icon" sizes="152x152" href={{ asset('/backend/assets/favicon/apple-icon-152x152.png')
        }}>
    <link rel="apple-touch-icon" sizes="180x180" href={{ asset('/backend/assets/favicon/apple-icon-180x180.png')
        }}>
    <link rel="icon" type="image/png" sizes="192x192" href={{
        asset('/backend/assets/favicon/android-icon-192x192.png') }}>
    <link rel="icon" type="image/png" sizes="32x32" href={{ asset('/backend/assets/favicon/favicon-32x32.png') }}>
    <link rel="icon" type="image/png" sizes="96x96" href={{ asset('/backend/assets/favicon/favicon-96x96.png') }}>
    <link rel="icon" type="image/png" sizes="16x16" href={{ asset('/backend/assets/favicon/favicon-16x16.png') }}>
    <link rel="manifest" href={{ asset('/backend/assets/favicon/manifest.json') }}>
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content={{ asset('/backend/assets/favicon/ms-icon-144x144.png') }}>
    <meta name="theme-color" content="#ffffff">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Vendors styles-->
    <link rel="stylesheet" href={{ asset('/backend/vendors/simplebar/css/simplebar.css') }}>
    <link rel="stylesheet" href={{ asset('/backend/css/vendors/simplebar.css') }}>

    <!-- CoreUI main styles -->
    <link href={{ asset('/backend/css/style.css') }} rel="stylesheet">

    <script src="{{ asset('/backend/js/config.js') }}"></script>
    <script src="{{ asset('/backend/js/color-modes.js') }}"></script>
    <link href="{{ url('/public/admin/vendors/@coreui/chartjs/css/coreui-chartjs.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>

    {{-- Google Font: Cairo (Arabic) --}}
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap RTL (يجب أن يأتي بعد ملفات Bootstrap/CoreUI الافتراضية لكي يُطبق الانعكاس) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">


    <!-- تحسينات RTL بسيطة إن لزم -->
    <style>
        body {
            direction: rtl;
            text-align: right;
        }

        .sidebar-nav .nav-link,
        .dropdown-menu {
            text-align: right;
        }

        /* لضبط مكان زر فتح القائمة في الاتجاه RTL باستخدام خصائص منطقية */
        .header .header-toggler {
            margin-inline-start: -14px;
        }

        /* عكس أي أسهم افتراضية إن ظهرت */
        .nav-group-toggle::after {
            transform: scaleX(-1);
        }

        <style>

        /* امنع أي pseudo-content أو خلفيات على Previous/Next الخاصة بالـ DataTables */
        .dataTables_wrapper .dataTables_paginate .paginate_button.previous,
        .dataTables_wrapper .dataTables_paginate .paginate_button.next {
            position: static !important;
            width: auto !important;
            height: auto !important;
            background: none !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.previous::before,
        .dataTables_wrapper .dataTables_paginate .paginate_button.previous::after,
        .dataTables_wrapper .dataTables_paginate .paginate_button.next::before,
        .dataTables_wrapper .dataTables_paginate .paginate_button.next::after {
            content: none !important;
        }

        /* شكل أزرار الترقيم */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            display: inline-block;
            padding: .375rem .625rem;
            margin: 0 .125rem;
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: .25rem;
            background: transparent;
            color: #9fb3ff;
            /* عدّل حسب ثيمك الداكن */
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: rgba(159, 179, 255, .15);
            color: #fff !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            opacity: .5;
            cursor: not-allowed;
            background: transparent;
        }

        /* لو الـ RTL قلب ترتيب الأرقام أو خلّى الأسهم يمين/يسار بشكل غريب */
        html[dir="rtl"] .dataTables_wrapper .dataTables_paginate {
            direction: ltr;
            /* يخلي الأرقام بترتيبها الطبيعي */
            text-align: left;
            /* مكان الأزرار */
        }
    </style>

    <script src={{ asset('/backend/js/config.js') }}></script>
    <script src={{ asset('/backend/js/color-modes.js') }}></script>
    <link href={{ url('/public/admin/vendors/@coreui/chartjs/css/coreui-chartjs.css') }} rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    @yield('style')
    @stack('head')
</head>

<body>
    <div class="sidebar sidebar-dark sidebar-fixed border-start" id="sidebar">
        <div class="sidebar-header border-bottom align-items-center justify-content-center">
            <div class="sidebar-brand">
                <img src={{ asset('/backend/assets/img/business-link.png') }} class="sidebar-brand-full"
                    width="auto" height="46" alt="سونيك">
            </div>
            <button class="btn-close d-lg-none" type="button" data-coreui-theme="dark" aria-label="إغلاق"
                onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()"></button>
        </div>
     @php
    $admin = auth('admin')->user();

    $adminPermissions = $admin
        ? $admin->getAllPermissions()
            ->where('guard_name', 'admin')
            ->pluck('name')
            ->toArray()
        : [];

    $canUsers = in_array('users.view', $adminPermissions, true);
    $canWorkspaces = in_array('workspaces.view', $adminPermissions, true);
    $canPlans = in_array('plans.view', $adminPermissions, true);
    $canFeatures = in_array('features.view', $adminPermissions, true);
    $canRoles = in_array('roles.view', $adminPermissions, true);
    $canPermissions = in_array('permissions.view', $adminPermissions, true);
    $vvvvvvvv = $admin->can('features.view');
    $canSubscriptions = $admin->can('subscriptions.view');
    $canPayments = $admin->can('payments.index');
@endphp

<ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.dashboard.index') ? 'active' : '' }}"
           href="{{ route('admin.dashboard') }}">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('/backend/vendors/@coreui/icons/svg/free.svg') }}#cil-speedometer"></use>
            </svg>
            لوحة التحكم
        </a>
    </li>

    <li class="nav-title">الإدارة</li>

    @if($canUsers && Route::has('admin.users.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
               href="{{ route('admin.users.index') }}">
                <i class="bi bi-people"></i>
                المستخدمون
            </a>
        </li>
    @endif

    @if($canWorkspaces && Route::has('admin.workspaces.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.workspaces.*') ? 'active' : '' }}"
               href="{{ route('admin.workspaces.index') }}">
                <i class="bi bi-grid"></i>
                مساحات العمل
            </a>
        </li>
    @endif

    @if($canPlans && Route::has('admin.plans.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}"
               href="{{ route('admin.plans.index') }}">
                <i class="bi bi-credit-card"></i>
                الباقات
            </a>
        </li>
    @endif

    @if($canFeatures && Route::has('admin.features.index'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.features.*') ? 'active' : '' }}"
               href="{{ route('admin.features.index') }}">
                <i class="bi bi-sliders"></i>
                الخصائص
            </a>
        </li>
    @endif

    @can('subscriptions.view')
<li class="nav-item">
    <a class="nav-link" href="{{ route('admin.subscriptions.index') }}">
        <i class="bi bi-arrow-repeat"></i>
        الاشتراكات
    </a>
</li>
@endcan

@can('payments.view')
<li class="nav-item">
    <a class="nav-link" href="{{ route('admin.payments.index') }}">
        <i class="bi bi-cash-stack"></i>
        المدفوعات
    </a>
</li>
@endcan


{{-- @can('payments.view') --}}
<li class="nav-item">
    <a class="nav-link" href="{{ route('admin.restaurant-menu-templates.index') }}">
        <i class="bi bi-cash-stack"></i>
        قوالب منيو المطاعم
    </a>
</li>
{{-- @endcan --}}
{{-- @can('payments.view') --}}
<li class="nav-item">
    <a class="nav-link" href="{{ route('admin.restaurant-menu-template-sections.index') }}">
        <i class="bi bi-cash-stack"></i>
        أقسام قوالب المنيو
    </a>
</li>
{{-- @endcan --}}




    {{-- @if($vvvvvvvv)
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.features.*') ? 'active' : '' }}"
               href="{{ route('admin.features.index') }}">
                <i class="bi bi-sliders"></i>
                cccccccccccccccccccccccccccccccccc
            </a>
        </li>
    @endif --}}
    @if(
        ($canRoles && Route::has('admin.roles.index')) ||
        ($canPermissions && Route::has('admin.permissions.index'))
    )
        <li class="nav-title">الإعدادات</li>

        <li class="nav-item nav-group {{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') ? 'show' : '' }}">
            <a class="nav-link nav-group-toggle" href="#">
                <i class="bi bi-gear"></i>
                الإعدادات
            </a>

            <ul class="nav-group-items">

                @if($canRoles && Route::has('admin.roles.index'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}"
                           href="{{ route('admin.roles.index') }}">
                            <i class="bi bi-shield-lock"></i>
                            الأدوار
                        </a>
                    </li>
                @endif

                @if($canPermissions && Route::has('admin.permissions.index'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}"
                           href="{{ route('admin.permissions.index') }}">
                            <i class="bi bi-key"></i>
                            الصلاحيات
                        </a>
                    </li>
                @endif

            </ul>
        </li>
    @endif

</ul>



        <div class="sidebar-footer border-top d-none d-md-flex">
            <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"
                aria-label="تبديل التصغير"></button>
        </div>
    </div>

    <div class="wrapper d-flex flex-column min-vh-100">
        <header class="header header-sticky p-0 mb-4">
            <div class="container-fluid border-bottom px-4">
                <button class="header-toggler" type="button"
                    onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()"
                    style="margin-inline-start: -14px;">
                    <svg class="icon icon-lg">
                        <use xlink:href="{{ asset('/backend/vendors/@coreui/icons/svg/free.svg') }}#cil-menu">
                        </use>
                    </svg>
                </button>

                <ul class="header-nav">
                    <li class="nav-item py-1">
                        <div class="vr h-100 mx-2 text-body text-opacity-75"></div>
                    </li>

                    <li class="nav-item dropdown">
                        <button class="btn btn-link nav-link py-2 px-2 d-flex align-items-center" type="button"
                            aria-expanded="false" data-coreui-toggle="dropdown" title="السِمة">
                            <svg class="icon icon-lg theme-icon-active">
                                <use
                                    xlink:href="{{ asset('/backend/vendors/@coreui/icons/svg/free.svg') }}#cil-contrast">
                                </use>
                            </svg>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" style="--cui-dropdown-min-width: 8rem;">
                            <li>
                                <button class="dropdown-item d-flex align-items-center" type="button"
                                    data-coreui-theme-value="light">
                                    <svg class="icon icon-lg me-3">
                                        <use
                                            xlink:href="{{ asset('/backend/vendors/@coreui/icons/svg/free.svg') }}#cil-sun">
                                        </use>
                                    </svg>فاتح
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item d-flex align-items-center" type="button"
                                    data-coreui-theme-value="dark">
                                    <svg class="icon icon-lg me-3">
                                        <use
                                            xlink:href="{{ asset('/backend/vendors/@coreui/icons/svg/free.svg') }}#cil-moon">
                                        </use>
                                    </svg>داكن
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item d-flex align-items-center active" type="button"
                                    data-coreui-theme-value="auto">
                                    <svg class="icon icon-lg me-3">
                                        <use
                                            xlink:href="{{ asset('/backend/vendors/@coreui/icons/svg/free.svg') }}#cil-contrast">
                                        </use>
                                    </svg>تلقائي
                                </button>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item py-1">
                        <div class="vr h-100 mx-2 text-body text-opacity-75"></div>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link py-0 pe-0" data-coreui-toggle="dropdown" href="#" role="button"
                            aria-haspopup="true" aria-expanded="false">
                            <div class="avatar avatar-md">
                                <img class="avatar-img"
                                    src="{{ asset('/backend/assets/img/avatars/Female-Avatar.png') }}"
                                    alt="المستخدم">
                            </div>
                        </a>
                        {{-- يمكنك إضافة قائمة حساب المستخدم هنا --}}
                    </li>
                </ul>
            </div>

            <div class="container-fluid px-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb my-0">
                        <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                        <li class="breadcrumb-item active"><span>لوحة التحكم</span></li>
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
            {{-- <div>© 2025 سونيك</div> --}}
        </footer>
    </div>

    <!-- CoreUI and necessary plugins-->
    <script src={{ asset('/backend/vendors/@coreui/coreui/js/coreui.bundle.min.js') }}></script>
    <script src={{ asset('/backend/vendors/simplebar/js/simplebar.min.js') }}></script>

    <script>
        const header = document.querySelector('header.header');
        document.addEventListener('scroll', () => {
            if (header) {
                header.classList.toggle('shadow-sm', document.documentElement.scrollTop > 0);
            }
        });
    </script>

    <!-- Plugins and scripts required by this view-->
    <script src={{ asset('/backend/vendors/chart.js/js/chart.umd.js') }}></script>
    <script src={{ asset('/backend/vendors/@coreui/chartjs/js/coreui-chartjs.js') }}></script>
    <script src={{ asset('/backend/vendors/@coreui/utils/js/index.js') }}></script>
    <script src={{ asset('/backend/js/main.js') }}></script>

    <!-- jQuery + DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>


    <!-- DataTables Buttons + تكامل Bootstrap 5 -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>

    <!-- التصدير إلى Excel/PDF والطباعة -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    @yield('scripts')
    @stack('scripts')
</body>

</html>