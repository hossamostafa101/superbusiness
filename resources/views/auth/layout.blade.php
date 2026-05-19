{{-- resources/views/auth/layout.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Super Business')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --sb-dark: #0f172a;
            --sb-primary: #2563eb;
            --sb-primary-2: #7c3aed;
            --sb-muted: #64748b;
            --sb-border: #e2e8f0;
            --sb-bg: #f8fafc;
        }

        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at 10% 10%, rgba(37, 99, 235, .16), transparent 28%),
                radial-gradient(circle at 90% 20%, rgba(124, 58, 237, .15), transparent 30%),
                linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--sb-dark);
        }

        .auth-page {
            min-height: 100vh;
            display: flex;
            align-items: stretch;
        }

        .auth-brand-panel {
            background:
                linear-gradient(145deg, rgba(15, 23, 42, .96), rgba(30, 41, 59, .96)),
                radial-gradient(circle at top right, rgba(37, 99, 235, .35), transparent 35%);
            color: #fff;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        .auth-brand-panel::before {
            content: "";
            position: absolute;
            width: 360px;
            height: 360px;
            border-radius: 999px;
            background: rgba(37, 99, 235, .25);
            top: -120px;
            left: -100px;
        }

        .auth-brand-panel::after {
            content: "";
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 999px;
            background: rgba(124, 58, 237, .22);
            bottom: -100px;
            right: -80px;
        }

        .brand-content {
            position: relative;
            z-index: 2;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px;
        }

        .brand-logo {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--sb-primary), var(--sb-primary-2));
            display: grid;
            place-items: center;
            font-weight: 900;
            color: #fff;
            box-shadow: 0 18px 40px rgba(37, 99, 235, .35);
        }

        .brand-title {
            font-size: 42px;
            font-weight: 900;
            letter-spacing: -1px;
            line-height: 1.2;
        }

        .brand-subtitle {
            color: rgba(255,255,255,.78);
            font-size: 16px;
            line-height: 1.9;
        }

        .feature-pill {
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 18px;
            padding: 14px 16px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
            backdrop-filter: blur(10px);
        }

        .feature-pill i {
            color: #93c5fd;
            font-size: 20px;
            margin-top: 2px;
        }

        .auth-form-panel {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px;
        }

        .auth-card {
            width: 100%;
            max-width: 500px;
            background: rgba(255,255,255,.86);
            border: 1px solid rgba(226, 232, 240, .9);
            border-radius: 28px;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .10);
            backdrop-filter: blur(14px);
            overflow: hidden;
        }

        .auth-card-body {
            padding: 34px;
        }

        .mobile-brand {
            display: none;
        }

        .form-control {
            min-height: 48px;
            border-radius: 14px;
            border-color: var(--sb-border);
        }

        .form-control:focus {
            border-color: var(--sb-primary);
            box-shadow: 0 0 0 .22rem rgba(37, 99, 235, .12);
        }

        .input-group .form-control {
            border-start-start-radius: 0;
            border-end-start-radius: 0;
        }

        .input-group .input-group-text,
        .input-group .btn {
            border-radius: 14px;
        }

        .input-group .input-group-text {
            border-start-end-radius: 0;
            border-end-end-radius: 0;
            background: #fff;
            color: var(--sb-muted);
        }

        .btn-sb {
            min-height: 48px;
            border-radius: 14px;
            border: 0;
            color: #fff;
            font-weight: 800;
            background: linear-gradient(135deg, var(--sb-primary), var(--sb-primary-2));
            box-shadow: 0 14px 30px rgba(37, 99, 235, .24);
        }

        .btn-sb:hover {
            color: #fff;
            filter: brightness(.98);
            transform: translateY(-1px);
        }

        .auth-link {
            color: var(--sb-primary);
            text-decoration: none;
            font-weight: 700;
        }

        .auth-link:hover {
            text-decoration: underline;
        }

        .mini-preview {
            background: #fff;
            border: 1px solid rgba(255,255,255,.18);
            color: var(--sb-dark);
            border-radius: 24px;
            padding: 16px;
            max-width: 330px;
            box-shadow: 0 26px 70px rgba(0,0,0,.25);
        }

        .mini-cover {
            height: 76px;
            border-radius: 18px;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
        }

        .mini-avatar {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            background: #fff;
            margin: -29px auto 10px;
            display: grid;
            place-items: center;
            box-shadow: 0 10px 25px rgba(15,23,42,.18);
            color: var(--sb-primary);
            font-size: 24px;
        }

        .mini-btn {
            height: 40px;
            border-radius: 14px;
            background: var(--sb-primary);
            color: #fff;
            display: grid;
            place-items: center;
            font-weight: 700;
            margin-top: 12px;
        }

        .auth-alert {
            border-radius: 16px;
        }

        @media (max-width: 991px) {
            .auth-brand-panel {
                display: none;
            }

            .auth-page,
            .auth-form-panel {
                min-height: 100vh;
            }

            .mobile-brand {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 24px;
            }

            .auth-card-body {
                padding: 26px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

<div class="auth-page">
    <div class="col-lg-6 auth-brand-panel d-none d-lg-block">
        <div class="brand-content">
            <div>
                <a href="{{ url('/') }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-3 mb-5">
                    <div class="brand-logo">SB</div>
                    <div>
                        <div class="fw-bold fs-5">Super Business</div>
                        <small class="text-white-50">Smart page for small business</small>
                    </div>
                </a>

                <h1 class="brand-title mb-3">
                    صفحتك الذكية<br>
                    ومنتجاتك وروابطك<br>
                    في مكان واحد
                </h1>

                <p class="brand-subtitle mb-4">
                    أنشئ صفحة بزنس احترافية، اعرض منتجاتك، واستقبل طلبات واستفسارات العملاء عبر واتساب بسهولة.
                </p>

                <div class="vstack gap-3">
                    <div class="feature-pill">
                        <i class="bi bi-link-45deg"></i>
                        <div>
                            <strong>Digital Bio</strong>
                            <div class="small text-white-50">روابطك، بياناتك، ومعلومات التواصل في صفحة واحدة.</div>
                        </div>
                    </div>

                    <div class="feature-pill">
                        <i class="bi bi-box-seam"></i>
                        <div>
                            <strong>Catalog</strong>
                            <div class="small text-white-50">اعرض منتجاتك أو خدماتك مع صور وأسعار وتصنيفات.</div>
                        </div>
                    </div>

                    <div class="feature-pill">
                        <i class="bi bi-whatsapp"></i>
                        <div>
                            <strong>WhatsApp Orders</strong>
                            <div class="small text-white-50">كل منتج له زر طلب مباشر عبر واتساب.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mini-preview">
                <div class="mini-cover"></div>
                <div class="mini-avatar">
                    <i class="bi bi-shop"></i>
                </div>
                <div class="text-center">
                    <div class="fw-bold">Luna Store</div>
                    <small class="text-muted">منتجات مختارة بعناية</small>
                </div>
                <div class="mini-btn">
                    <i class="bi bi-whatsapp me-1"></i>
                    اطلب عبر واتساب
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6 auth-form-panel">
        <div class="auth-card">
            <div class="auth-card-body">
                <div class="mobile-brand">
                    <div class="brand-logo">SB</div>
                    <div>
                        <div class="fw-bold fs-5">Super Business</div>
                        <small class="text-muted">صفحتك الذكية للبزنس</small>
                    </div>
                </div>

                @yield('auth-body')
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')
</body>
</html>