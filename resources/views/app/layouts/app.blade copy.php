{{-- resources/views/app/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة البزنس')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    

    <style>
        body {
            background: #f6f7fb;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .app-sidebar {
            min-height: 100vh;
            background: #111827;
            color: #fff;
        }

        .app-sidebar a {
            color: rgba(255,255,255,.78);
            text-decoration: none;
            display: block;
            padding: 10px 14px;
            border-radius: 10px;
            margin-bottom: 6px;
        }

        .app-sidebar a:hover,
        .app-sidebar a.active {
            background: rgba(255,255,255,.1);
            color: #fff;
        }

        .content-card {
            border: 1px solid #e5e7eb;
            border-radius: 18px;
        }

        @media (max-width: 991px) {
            .app-sidebar {
                min-height: auto;
            }
        }
    </style>

    @stack('head')
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <aside class="col-lg-3 col-xl-2 app-sidebar p-4">
            <div class="mb-4">
                <h5 class="fw-bold mb-1">{{ $workspace->name ?? 'Workspace' }}</h5>
                <small class="text-white-50">{{ $workspace->slug ?? '' }}</small>
            </div>

            <nav>
                <a href="{{ route('app.business-profile.edit', $workspace) }}"
                   class="{{ request()->routeIs('app.business-profile.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge"></i>
                    بيانات الصفحة
                </a>

                <a href="{{ route('app.links.index', $workspace) }}"
                   class="{{ request()->routeIs('app.links.*') ? 'active' : '' }}">
                    <i class="bi bi-link-45deg"></i>
                    الروابط
                </a>

                <a href="{{ route('billing.plans', $workspace) }}">
                    <i class="bi bi-credit-card"></i>
                    الباقات والدفع
                </a>

                <a href="{{ route('public.business-page.show', $workspace) }}" target="_blank">
                    <i class="bi bi-box-arrow-up-left"></i>
                    عرض الصفحة
                </a>

                <a href="{{ route('app.categories.index', $workspace) }}"
   class="{{ request()->routeIs('app.categories.*') ? 'active' : '' }}">
    <i class="bi bi-tags"></i>
    التصنيفات
</a>

<a href="{{ route('app.products.index', $workspace) }}"
   class="{{ request()->routeIs('app.products.*') ? 'active' : '' }}">
    <i class="bi bi-box-seam"></i>
    المنتجات
</a>


<a href="{{ route('app.analytics.index', $workspace) }}"
   class="{{ request()->routeIs('app.analytics.*') ? 'active' : '' }}">
    <i class="bi bi-graph-up"></i>
    التحليلات
</a>


<a href="{{ route('app.customers.index', $workspace) }}"
   class="{{ request()->routeIs('app.customers.*') ? 'active' : '' }}">
    <i class="bi bi-people"></i>
    العملاء
</a>

<a href="{{ route('app.services.index', $workspace) }}"
   class="{{ request()->routeIs('app.services.*') ? 'active' : '' }}">
    <i class="bi bi-briefcase"></i>
    الخدمات
</a>

<a href="{{ route('app.appointments.index', $workspace) }}"
   class="{{ request()->routeIs('app.appointments.*') ? 'active' : '' }}">
    <i class="bi bi-calendar-check"></i>
    المواعيد
</a>

<a href="{{ route('app.booking-settings.edit', $workspace) }}"
   class="{{ request()->routeIs('app.booking-settings.*') ? 'active' : '' }}">
    <i class="bi bi-gear"></i>
    إعدادات الحجز
</a>


<a href="{{ route('app.appointments.calendar', $workspace) }}"
   class="{{ request()->routeIs('app.appointments.calendar') ? 'active' : '' }}">
    <i class="bi bi-calendar3"></i>
    تقويم المواعيد
</a>













<a href="{{ route('app.restaurant-menu.branches.index', $workspace) }}"
   class="{{ request()->routeIs('app.restaurant-menu.branches.*') ? 'active' : '' }}">
    <i class="bi bi-shop"></i>
    فروع المطعم
</a>

<a href="{{ route('app.restaurant-menu.categories.index', $workspace) }}"
   class="{{ request()->routeIs('app.restaurant-menu.categories.*') ? 'active' : '' }}">
    <i class="bi bi-list-ul"></i>
    تصنيفات المنيو
</a>

<a href="{{ route('app.restaurant-menu.items.index', $workspace) }}"
   class="{{ request()->routeIs('app.restaurant-menu.items.*') ? 'active' : '' }}">
    <i class="bi bi-cup-hot"></i>
    أصناف المنيو
</a>

<a href="{{ route('public.restaurant-menu.workspace', $workspace) }}" target="_blank">
    <i class="bi bi-box-arrow-up-left"></i>
    عرض المنيو
</a>





            </nav>

            <hr class="border-secondary">

            <a href="{{ url('/') }}">
                <i class="bi bi-house"></i>
                الرئيسية
            </a>
        </aside>

        <main class="col-lg-9 col-xl-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h4 fw-bold mb-1">@yield('page_title', 'لوحة البزنس')</h1>
                    <p class="text-muted mb-0">@yield('page_description')</p>
                </div>

                <div class="text-end">
                    <small class="text-muted d-block">الرابط العام</small>
                    <a href="{{ route('public.business-page.show', $workspace) }}" target="_blank">
                        {{ route('public.business-page.show', $workspace) }}
                    </a>
                </div>
            </div>

            @include('app.partials.alerts')

            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')
</body>
</html>