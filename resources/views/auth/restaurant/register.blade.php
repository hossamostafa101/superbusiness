<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>تسجيل مطعم جديد</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #0f172a, #1d4ed8, #6366f1);
            --card-bg: rgba(15, 23, 42, 0.88);
            --accent: #22c55e;
            --accent-soft: rgba(34, 197, 94, .1);
            --text-main: #f9fafb;
            --text-muted: #9ca3af;
            --danger: #f97373;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background-image: var(--bg-gradient);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .auth-shell {
            width: 100%;
            max-width: 1100px;
        }

        .auth-card {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(0, 1.1fr);
            gap: 0;
            background: radial-gradient(circle at top left, rgba(148,163,184,.25), transparent 60%),
                        var(--card-bg);
            border-radius: 28px;
            box-shadow:
                0 24px 60px rgba(15, 23, 42, .8),
                0 0 0 1px rgba(148, 163, 184, .15);
            overflow: hidden;
        }

        @media (max-width: 900px) {
            .auth-card {
                grid-template-columns: minmax(0, 1fr);
            }
            .auth-side--brand {
                display: none;
            }
        }

        .auth-side {
            padding: 32px 32px 28px;
        }

        .auth-side--brand {
            position: relative;
            background: radial-gradient(circle at top, rgba(59,130,246, .35), transparent 55%),
                        radial-gradient(circle at bottom, rgba(34,197,94,.25), transparent 55%);
            border-inline-end: 1px solid rgba(148, 163, 184, .35);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            isolation: isolate;
        }

        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(15, 23, 42, .75);
            color: var(--accent);
            font-size: 11px;
            letter-spacing: .06em;
            text-transform: uppercase;
            border: 1px solid rgba(148, 163, 184, .45);
        }

        .badge-dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: var(--accent);
            box-shadow: 0 0 0 5px rgba(34, 197, 94, .25);
        }

        .brand-title {
            margin-top: 28px;
            font-size: 26px;
            font-weight: 700;
        }

        .brand-subtitle {
            margin-top: 10px;
            font-size: 14px;
            color: var(--text-muted);
            max-width: 320px;
            line-height: 1.7;
        }

        .brand-highlight {
            margin-top: 32px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            font-size: 12px;
        }

        .brand-highlight-item {
            padding: 10px 12px;
            border-radius: 14px;
            background: rgba(15, 23, 42, .75);
            border: 1px solid rgba(148, 163, 184, .35);
        }

        .brand-highlight-label {
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .brand-highlight-value {
            font-weight: 600;
            font-size: 13px;
        }

        .brand-footer {
            font-size: 11px;
            color: var(--text-muted);
            opacity: .9;
        }

        .auth-side--form {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 28px 30px;
        }

        .auth-header-eyebrow {
            font-size: 11px;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 6px;
        }

        .auth-title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .auth-subtitle {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 18px;
        }

        .alert {
            border-radius: 14px;
            padding: 10px 12px;
            font-size: 13px;
            margin-bottom: 14px;
        }

        .alert-error {
            background: rgba(248, 113, 113, .09);
            color: #fecaca;
            border: 1px solid rgba(248, 113, 113, .5);
        }

        .form-grid {
            display: grid;
            grid-template-columns: minmax(0,1fr) minmax(0,1fr);
            gap: 14px 16px;
        }

        @media (max-width: 640px) {
            .form-grid {
                grid-template-columns: minmax(0,1fr);
            }
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .form-label {
            font-size: 12px;
            color: var(--text-muted);
        }

        .form-label span {
            color: #fca5a5;
            margin-inline-start: 3px;
        }

        .form-control {
            width: 100%;
            border-radius: 10px;
            border: 1px solid rgba(148, 163, 184, .6);
            padding: 9px 11px;
            font-size: 13px;
            background: rgba(15, 23, 42, .85);
            color: var(--text-main);
            outline: none;
            transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
        }

        .form-control::placeholder {
            color: rgba(148, 163, 184, .7);
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 1px rgba(34, 197, 94, .35);
            background: rgba(15, 23, 42, .92);
        }

        .invalid-feedback {
            font-size: 11px;
            color: #fecaca;
        }

        .form-footer-row {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 18px;
        }

        .btn-primary {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            border: none;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            background: radial-gradient(circle at top left, #4ade80, #22c55e);
            color: #052e16;
            box-shadow:
                0 16px 30px rgba(21, 128, 61, .45),
                0 0 0 1px rgba(190, 242, 100, .7);
            transition: transform .12s ease, box-shadow .12s ease, filter .12s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            filter: brightness(1.05);
            box-shadow:
                0 22px 40px rgba(21, 128, 61, .6),
                0 0 0 1px rgba(190, 242, 100, .9);
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow:
                0 10px 22px rgba(21, 128, 61, .7),
                0 0 0 1px rgba(190, 242, 100, .8);
        }

        .btn-primary-icon {
            font-size: 16px;
        }

        .auth-alt {
            font-size: 13px;
            color: var(--text-muted);
        }

        .auth-alt a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
        }

        .auth-alt a:hover {
            text-decoration: underline;
        }

        .divider {
            margin: 18px 0 8px;
            height: 1px;
            background: radial-gradient(circle, rgba(148, 163, 184, .7), transparent);
            opacity: .7;
        }

        .small-note {
            font-size: 11px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .small-note a {
            color: var(--accent);
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="auth-shell">
    <div class="auth-card">

        {{-- الجانب البراند / التسويق --}}
        <div class="auth-side auth-side--brand">
            <div>
                <div class="badge-pill">
                    <span class="badge-dot"></span>
                    <span>منصة قائمة رقمية للمطاعم</span>
                </div>

                <h1 class="brand-title">حوِّل منيو مطعمك لتجربة رقمية أنيقة</h1>
                <p class="brand-subtitle">
                    أنشئ حساب لمطعمك، أضف الفروع والعاملين، واعرض منيو تفاعلي لعملائك مع طلبات أونلاين بسهولة.
                </p>

                <div class="brand-highlight">
                    <div class="brand-highlight-item">
                        <div class="brand-highlight-label">إدارة الفروع</div>
                        <div class="brand-highlight-value">متعددة ومركزيّة</div>
                    </div>
                    <div class="brand-highlight-item">
                        <div class="brand-highlight-label">صلاحيات المستخدمين</div>
                        <div class="brand-highlight-value">مالك · مدير · كاشير · مطبخ</div>
                    </div>
                    <div class="brand-highlight-item">
                        <div class="brand-highlight-label">خطط مرنة</div>
                        <div class="brand-highlight-value">تناسب حجم عملك</div>
                    </div>
                </div>
            </div>

            <div class="brand-footer">
                لوحة المطعم مخصّصة لحسابك فقط، يمكنك تغيير الخطة، إضافة الفروع، والتحكّم في فريق العمل بحرية.
            </div>
        </div>

        {{-- الجانب الخاص بالفورم --}}
        <div class="auth-side auth-side--form">

            <div class="auth-header-eyebrow">تسجيل مطعم جديد</div>
            <h2 class="auth-title">ابدأ رحلتك معنا في دقائق</h2>
            <p class="auth-subtitle">
                أدخل بيانات المطعم الأساسية، وبعد التسجيل ستتمكن من اختيار خطة الاشتراك المناسبة.
            </p>

            @if ($errors->has('register'))
                <div class="alert alert-error">
                    {{ $errors->first('register') }}
                </div>
            @endif

            <form method="POST" action="{{ route('restaurant.register.store') }}">
                @csrf

                <div class="form-grid">
                    {{-- اسم المطعم --}}
                    <div class="form-group">
                        <label for="restaurant_name" class="form-label">
                            اسم المطعم <span>*</span>
                        </label>
                        <input
                            id="restaurant_name"
                            type="text"
                            name="restaurant_name"
                            class="form-control"
                            placeholder="مثال: مطعم الساحة"
                            value="{{ old('restaurant_name') }}"
                            required
                        >
                        @error('restaurant_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- البريد الإلكتروني --}}
                    <div class="form-group">
                        <label for="email" class="form-label">
                            البريد الإلكتروني <span>*</span>
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            class="form-control"
                            placeholder="example@restaurant.com"
                            value="{{ old('email') }}"
                            required
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- الهاتف --}}
                    <div class="form-group">
                        <label for="phone" class="form-label">
                            رقم الهاتف (اختياري)
                        </label>
                        <input
                            id="phone"
                            type="text"
                            name="phone"
                            class="form-control"
                            placeholder="مثال: 0123456789"
                            value="{{ old('phone') }}"
                        >
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- كلمة المرور --}}
                    <div class="form-group">
                        <label for="password" class="form-label">
                            كلمة المرور <span>*</span>
                        </label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="form-control"
                            placeholder="********"
                            required
                        >
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- تأكيد كلمة المرور --}}
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">
                            تأكيد كلمة المرور <span>*</span>
                        </label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            class="form-control"
                            placeholder="أعد إدخال كلمة المرور"
                            required
                        >
                    </div>
                </div>

                <div class="form-footer-row">
                    <button type="submit" class="btn-primary">
                        <span>إنشاء حساب المطعم</span>
                        <span class="btn-primary-icon">↗</span>
                    </button>

                    <div class="auth-alt">
                        لديك حساب مطعم بالفعل؟
                        <a href="{{ route('restaurant.login') }}">تسجيل الدخول</a>
                    </div>
                </div>

                <div class="divider"></div>

                <p class="small-note">
                    بالتسجيل، أنت توافق على شروط الاستخدام وسياسة الخصوصية الخاصة بالمنصة.
                    يمكنك لاحقًا تغيير خطة الاشتراك من داخل لوحة المطعم في أي وقت.
                </p>
            </form>

        </div>
    </div>
</div>

</body>
</html>
