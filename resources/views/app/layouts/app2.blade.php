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
                {{-- <h5 class="fw-bold mb-1">{{ $workspace->name ?? 'Workspace' }}</h5> --}}
                {{-- <small class="text-white-50">{{ $workspace->slug ?? '' }}</small> --}}
            </div>

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
                    <a href="" target="_blank">
                        {{-- {{ route('public.business-page.show', $workspace) }} --}}
                    </a>
                </div>
            </div>

            @include('app.partials.alerts')

            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>