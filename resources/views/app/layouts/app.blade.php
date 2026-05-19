{{-- resources/views/app/layouts/app.blade.php --}}
@php
    // $workspace = $workspace ?? request()->route('workspace');

    // $publicUrl = $workspace ? route('public.business-page.show', $workspace) : url('/');

    $workspace = $workspace ?? request()->route('workspace');

    if ($workspace) {
        $workspace->loadMissing('specification');
    }

    $publicUrl = $workspace
        ? ($workspace->isSpecification('restaurant')
            ? route('public.restaurant-menu.workspace', $workspace)
            : route('public.business-page.show', $workspace))
        : url('/');

    $navGroups = $workspace ? app(\App\Services\App\WorkspaceNavigationService::class)->groups($workspace) : [];

    $navGroupsX = [
        [
            'title' => 'الصفحة',
            'items' => [
                [
                    'label' => 'نظرة عامة',
                    'icon' => 'bi-speedometer2',
                    'route' => route('app.analytics.index', $workspace),
                    'active' => request()->routeIs('app.analytics.*'),
                ],
                [
                    'label' => 'بيانات الصفحة',
                    'icon' => 'bi-person-badge',
                    'route' => route('app.business-profile.edit', $workspace),
                    'active' => request()->routeIs('app.business-profile.*'),
                ],
                [
                    'label' => 'الروابط',
                    'icon' => 'bi-link-45deg',
                    'route' => route('app.links.index', $workspace),
                    'active' => request()->routeIs('app.links.*'),
                ],
            ],
        ],
        [
            'title' => 'الكتالوج',
            'items' => [
                [
                    'label' => 'التصنيفات',
                    'icon' => 'bi-tags',
                    'route' => route('app.categories.index', $workspace),
                    'active' => request()->routeIs('app.categories.*'),
                ],
                [
                    'label' => 'المنتجات',
                    'icon' => 'bi-box-seam',
                    'route' => route('app.products.index', $workspace),
                    'active' => request()->routeIs('app.products.*'),
                ],
            ],
        ],
        [
            'title' => 'CRM والحجوزات',
            'items' => [
                [
                    'label' => 'العملاء',
                    'icon' => 'bi-people',
                    'route' => route('app.customers.index', $workspace),
                    'active' => request()->routeIs('app.customers.*'),
                ],
                [
                    'label' => 'الخدمات',
                    'icon' => 'bi-briefcase',
                    'route' => route('app.services.index', $workspace),
                    'active' => request()->routeIs('app.services.*'),
                ],
                [
                    'label' => 'المواعيد',
                    'icon' => 'bi-calendar-check',
                    'route' => route('app.appointments.index', $workspace),
                    'active' =>
                        request()->routeIs('app.appointments.index') ||
                        request()->routeIs('app.appointments.create') ||
                        request()->routeIs('app.appointments.edit'),
                ],
                [
                    'label' => 'تقويم المواعيد',
                    'icon' => 'bi-calendar3',
                    'route' => route('app.appointments.calendar', $workspace),
                    'active' => request()->routeIs('app.appointments.calendar'),
                ],
                [
                    'label' => 'إعدادات الحجز',
                    'icon' => 'bi-gear',
                    'route' => route('app.booking-settings.edit', $workspace),
                    'active' => request()->routeIs('app.booking-settings.*'),
                ],
            ],
        ],
        [
            'title' => 'منيو المطعم',
            'items' => [
                [
                    'label' => 'الفروع',
                    'icon' => 'bi-shop',
                    'route' => route('app.restaurant-menu.branches.index', $workspace),
                    'active' => request()->routeIs('app.restaurant-menu.branches.*'),
                ],
                [
                    'label' => 'تصنيفات المنيو',
                    'icon' => 'bi-list-ul',
                    'route' => route('app.restaurant-menu.categories.index', $workspace),
                    'active' => request()->routeIs('app.restaurant-menu.categories.*'),
                ],
                [
                    'label' => 'أصناف المنيو',
                    'icon' => 'bi-cup-hot',
                    'route' => route('app.restaurant-menu.items.index', $workspace),
                    'active' => request()->routeIs('app.restaurant-menu.items.*'),
                ],
                [
                    'label' => 'طلبات المنيو',
                    'icon' => 'bi-receipt',
                    'route' => route('app.restaurant-menu.orders.index', $workspace),
                    'active' => request()->routeIs('app.restaurant-menu.orders.*'),
                ],
                [
                    'label' => 'الطاولات و QR',
                    'icon' => 'bi-qr-code',
                    'route' => route('app.restaurant-menu.tables.index', $workspace),
                    'active' => request()->routeIs('app.restaurant-menu.tables.*'),
                ],
                [
                    'label' => 'عرض المنيو',
                    'icon' => 'bi-box-arrow-up-left',
                    'route' => route('public.restaurant-menu.workspace', $workspace),
                    'active' => false,
                ],
            ],
        ],
        [
            'title' => 'النظام',
            'items' => [
                [
                    'label' => 'الباقات والدفع',
                    'icon' => 'bi-credit-card',
                    'route' => route('billing.plans', $workspace),
                    'active' => request()->routeIs('billing.*'),
                ],
            ],
        ],
    ];
@endphp

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة البزنس')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --app-bg: #f6f7fb;
            --sidebar-bg: #0f172a;
            --sidebar-bg-2: #111827;
            --sidebar-text: rgba(255, 255, 255, .78);
            --sidebar-muted: rgba(255, 255, 255, .48);
            --sidebar-active: rgba(37, 99, 235, .18);
            --sidebar-active-border: #60a5fa;
            --card-border: #e5e7eb;
        }

        body {
            background: var(--app-bg);
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .app-shell {
            min-height: 100vh;
        }

        .app-sidebar {
            width: 280px;
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, .18), transparent 32%),
                linear-gradient(180deg, var(--sidebar-bg), var(--sidebar-bg-2));
            color: #fff;
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            overflow-y: auto;
            z-index: 1030;
            padding: 18px;
            transition: transform .25s ease;
        }

        .app-main {
            margin-right: 280px;
            min-height: 100vh;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 8px 18px;
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            display: grid;
            place-items: center;
            font-weight: 900;
            color: #fff;
            flex-shrink: 0;
        }

        .workspace-card {
            background: rgba(255, 255, 255, .07);
            border: 1px solid rgba(255, 255, 255, .10);
            border-radius: 18px;
            padding: 14px;
            margin-bottom: 18px;
        }

        .workspace-name {
            font-weight: 800;
            line-height: 1.3;
        }

        .workspace-slug {
            color: var(--sidebar-muted);
            font-size: 12px;
        }

        .public-link {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #fff;
            background: rgba(37, 99, 235, .25);
            border: 1px solid rgba(96, 165, 250, .28);
            border-radius: 14px;
            padding: 10px 12px;
            text-decoration: none;
            margin-top: 12px;
            font-size: 13px;
        }

        .public-link:hover {
            color: #fff;
            background: rgba(37, 99, 235, .35);
        }

        .nav-section {
            margin-bottom: 18px;
        }

        .nav-section-title {
            color: var(--sidebar-muted);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .04em;
            padding: 0 10px;
            margin-bottom: 8px;
        }

        .sidebar-link {
            color: var(--sidebar-text);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px 12px;
            border-radius: 14px;
            margin-bottom: 5px;
            border: 1px solid transparent;
            transition: .18s ease;
        }

        .sidebar-link i {
            width: 22px;
            text-align: center;
            font-size: 17px;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, .08);
            color: #fff;
        }

        .sidebar-link.active {
            background: var(--sidebar-active);
            color: #fff;
            border-color: rgba(96, 165, 250, .25);
            box-shadow: inset 3px 0 0 var(--sidebar-active-border);
        }

        .sidebar-footer {
            border-top: 1px solid rgba(255, 255, 255, .10);
            padding-top: 14px;
            margin-top: 14px;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 1020;
            background: rgba(246, 247, 251, .86);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(229, 231, 235, .75);
        }

        .content-card {
            border: 1px solid var(--card-border);
            border-radius: 18px;
        }

        .mobile-overlay {
            display: none;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
        }

        .page-public-url {
            max-width: 420px;
        }

        .page-public-url a {
            word-break: break-all;
        }

        @media (max-width: 991px) {
            .app-sidebar {
                transform: translateX(100%);
                width: 292px;
                box-shadow: -18px 0 45px rgba(15, 23, 42, .28);
            }

            body.sidebar-open .app-sidebar {
                transform: translateX(0);
            }

            .app-main {
                margin-right: 0;
            }

            .mobile-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, .45);
                z-index: 1025;
            }

            body.sidebar-open .mobile-overlay {
                display: block;
            }

            .page-header {
                flex-direction: column;
            }

            .page-public-url {
                width: 100%;
                background: #fff;
                border: 1px solid var(--card-border);
                border-radius: 14px;
                padding: 10px 12px;
            }

            main {
                padding: 18px !important;
            }
        }
    </style>

    @stack('head')
</head>

<body>

    <div class="mobile-overlay" id="sidebarOverlay"></div>

    <aside class="app-sidebar" id="appSidebar">
        <div class="sidebar-brand">
            <div class="brand-mark">SB</div>
            <div>
                <div class="fw-bold">Super Business</div>
                <small class="text-white-50">لوحة إدارة البزنس</small>
            </div>
        </div>

        <div class="workspace-card">
            <div class="workspace-name">
                {{ $workspace->name ?? 'Workspace' }}
            </div>

            <div class="workspace-slug">
                {{ $workspace->slug ?? '' }}
            </div>

            <a href="{{ $publicUrl }}" target="_blank" class="public-link">
                <i class="bi bi-box-arrow-up-left"></i>
                عرض الصفحة العامة
            </a>
        </div>

        <nav>

            @foreach($navGroups as $group)
    <div class="nav-section">
        <div class="nav-section-title">
            {{ $group['title'] }}
        </div>

        @foreach($group['items'] as $item)
            @continue(isset($item['exists']) && ! $item['exists'])

            <a href="{{ $item['route'] }}"
               target="{{ $item['target'] ?? '_self' }}"
               class="sidebar-link {{ $item['active'] ? 'active' : '' }}">
                <i class="bi {{ $item['icon'] }}"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
@endforeach

            {{-- @foreach ($navGroups as $group)
                <div class="nav-section">
                    <div class="nav-section-title">
                        {{ $group['title'] }}
                    </div>

                    @foreach ($group['items'] as $item)
                        <a href="{{ $item['route'] }}" class="sidebar-link {{ $item['active'] ? 'active' : '' }}">
                            <i class="bi {{ $item['icon'] }}"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            @endforeach --}}
        </nav>

        <div class="sidebar-footer">
            <a href="{{ url('/') }}" class="sidebar-link">
                <i class="bi bi-house"></i>
                <span>الرئيسية</span>
            </a>

            @if (Route::has('logout'))
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf

                    <button type="submit" class="sidebar-link w-100 bg-transparent text-start border-0">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>تسجيل الخروج</span>
                    </button>
                </form>
            @endif
        </div>
    </aside>

    <div class="app-main">
        <header class="topbar">
            <div class="container-fluid py-3 px-3 px-lg-4">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <button class="btn btn-outline-dark d-lg-none" type="button" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>

                    <div class="d-none d-lg-block">
                        <strong>{{ $workspace->name ?? 'Workspace' }}</strong>
                        <small class="text-muted">/ {{ $workspace->slug ?? '' }}</small>
                    </div>

                    <div class="d-flex align-items-center gap-2 ms-auto">
                        <a href="{{ route('billing.plans', $workspace) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-credit-card"></i>
                            الباقة
                        </a>

                        <a href="{{ $publicUrl }}" target="_blank" class="btn btn-sm btn-primary">
                            <i class="bi bi-eye"></i>
                            معاينة
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-4">
            <div class="page-header mb-4">
                <div>
                    <h1 class="h4 fw-bold mb-1">@yield('page_title', 'لوحة البزنس')</h1>
                    <p class="text-muted mb-0">@yield('page_description')</p>
                </div>

                <div class="page-public-url text-lg-end">
                    <small class="text-muted d-block">الرابط العام</small>
                    <a href="{{ $publicUrl }}" target="_blank">
                        {{ $publicUrl }}
                    </a>
                </div>
            </div>

            @include('app.partials.alerts')

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        (function() {
            const body = document.body;
            const toggle = document.getElementById('sidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');

            function closeSidebar() {
                body.classList.remove('sidebar-open');
            }

            function toggleSidebar() {
                body.classList.toggle('sidebar-open');
            }

            toggle?.addEventListener('click', toggleSidebar);
            overlay?.addEventListener('click', closeSidebar);

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeSidebar();
                }
            });

            document.querySelectorAll('.sidebar-link').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 992) {
                        closeSidebar();
                    }
                });
            });
        })();
    </script>

    @stack('scripts')
</body>

</html>
