<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'لوحة المسوق')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: "Cairo", system-ui, sans-serif;
            background: #f5f7fb;
            color: #0f172a;
        }

        .affiliate-shell {
            min-height: 100vh;
        }

        .affiliate-sidebar {
            width: 260px;
            min-height: 100vh;
            background: #0f172a;
            color: #fff;
            position: fixed;
            top: 0;
            right: 0;
            padding: 20px 14px;
        }

        .affiliate-brand {
            font-weight: 900;
            font-size: 20px;
            padding: 10px 12px 22px;
        }

        .affiliate-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255,255,255,.75);
            text-decoration: none;
            padding: 11px 12px;
            border-radius: 14px;
            margin-bottom: 6px;
            font-weight: 700;
        }

        .affiliate-nav a:hover,
        .affiliate-nav a.active {
            background: rgba(255,255,255,.10);
            color: #fff;
        }

        .affiliate-main {
            margin-right: 260px;
            min-height: 100vh;
        }

        .affiliate-topbar {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            padding: 16px 24px;
        }

        .affiliate-content {
            padding: 24px;
        }

        .content-card {
            border: 0;
            border-radius: 22px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .06);
        }

        @media (max-width: 991px) {
            .affiliate-sidebar {
                position: static;
                width: 100%;
                min-height: auto;
            }

            .affiliate-main {
                margin-right: 0;
            }

            .affiliate-content {
                padding: 16px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

<div class="affiliate-shell">
    <aside class="affiliate-sidebar">
        <div class="affiliate-brand">
            برنامج المسوقين
        </div>

        <nav class="affiliate-nav">
            <a href="{{ route('affiliate.dashboard') }}" class="{{ request()->routeIs('affiliate.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                الرئيسية
            </a>

           <a href="{{ route('affiliate.links.index') }}" class="{{ request()->routeIs('affiliate.links.*') ? 'active' : '' }}">
    <i class="bi bi-link-45deg"></i>
    الروابط
</a>

<a href="{{ route('affiliate.commissions.index') }}" class="{{ request()->routeIs('affiliate.commissions.*') ? 'active' : '' }}">
    <i class="bi bi-cash-stack"></i>
    العمولات
</a>

<a href="{{ route('affiliate.withdrawals.index') }}" class="{{ request()->routeIs('affiliate.withdrawals.*') ? 'active' : '' }}">
    <i class="bi bi-wallet2"></i>
    السحوبات
</a>

<a href="{{ route('affiliate.resources.index') }}" class="{{ request()->routeIs('affiliate.resources.*') ? 'active' : '' }}">
    <i class="bi bi-folder2-open"></i>
    أدوات التسويق
</a>
            <a href="{{ url('/') }}">
                <i class="bi bi-house"></i>
                الرجوع للموقع
            </a>
        </nav>
    </aside>

    <main class="affiliate-main">
        <header class="affiliate-topbar">
            <div class="d-flex justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="h5 fw-bold mb-1">
                        @yield('page_title', 'لوحة المسوق')
                    </h1>

                    <div class="text-muted small">
                        @yield('page_description', 'إدارة الروابط والعمولات والسحوبات.')
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <span class="small text-muted">
                        {{ auth()->user()->name }}
                    </span>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button class="btn btn-sm btn-outline-danger">
                            خروج
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <div class="affiliate-content">
            @if(session('success'))
                <div class="alert alert-success rounded-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger rounded-4">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')
</body>
</html>