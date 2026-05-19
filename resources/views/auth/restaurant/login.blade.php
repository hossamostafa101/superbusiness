<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>تسجيل الدخول للمطعم</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        :root {
            --bg-gradient: radial-gradient(circle at top left, #1e293b, #020617);
            --card-bg: rgba(15, 23, 42, 0.9);
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
            background-attachment: fixed;
            background-size: cover;
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .auth-shell {
            width: 100%;
            max-width: 460px;
        }

        .auth-card {
            background:
                radial-gradient(circle at top, rgba(59,130,246,.16), transparent 55%),
                radial-gradient(circle at bottom, rgba(34,197,94,.16), transparent 55%),
                var(--card-bg);
            border-radius: 26px;
            padding: 26px 24px 22px;
            box-shadow:
                0 24px 60px rgba(15, 23, 42, .9),
                0 0 0 1px rgba(148, 163, 184, .2);
            backdrop-filter: blur(20px);
        }

        .logo-circle {
            width: 40px;
            height: 40px;
            border-radius: 999px;
            background: radial-gradient(circle at top, #4ade80, #22c55e);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #052e16;
            font-size: 20px;
            font-weight: 800;
            box-shadow: 0 12px 30px rgba(21, 128, 61, .7);
            margin-bottom: 10px;
        }

        .auth-title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .auth-subtitle {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 16px;
        }

        .alert {
            border-radius: 14px;
            padding: 9px 11px;
            font-size: 13px;
            margin-bottom: 12px;
        }

        .alert-error {
            background: rgba(248, 113, 113, .09);
            color: #fecaca;
            border: 1px solid rgba(248, 113, 113, .5);
        }

        .form-group {
            margin-bottom: 12px;
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
            border: 1px solid rgba(148, 163, 184, .65);
            padding: 9px 11px;
            font-size: 13px;
            background: rgba(15, 23, 42, .9);
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
            background: rgba(15, 23, 42, .96);
        }

        .invalid-feedback {
            font-size: 11px;
            color: #fecaca;
        }

        .form-row-inline {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }

        .form-check {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            font-size: 12px;
            color: var(--text-muted);
        }

        .form-check-input {
            width: 14px;
            height: 14px;
            border-radius: 4px;
            border: 1px solid rgba(148, 163, 184, .7);
            appearance: none;
            -webkit-appearance: none;
            background: transparent;
            position: relative;
        }

        .form-check-input:checked {
            background: var(--accent);
            border-color: var(--accent);
        }

        .form-check-input:checked::after {
            content: '✓';
            position: absolute;
            inset: -1px 0 0 0;
            font-size: 11px;
            color: #052e16;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .link-sm {
            font-size: 12px;
            color: var(--accent);
            text-decoration: none;
        }

        .link-sm:hover {
            text-decoration: underline;
        }

        .btn-primary {
            width: 100%;
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
            margin-bottom: 12px;
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
            text-align: center;
            margin-bottom: 6px;
        }

        .auth-alt a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
        }

        .auth-alt a:hover {
            text-decoration: underline;
        }

        .small-note {
            font-size: 11px;
            color: var(--text-muted);
            text-align: center;
            line-height: 1.6;
        }

        .small-note span {
            color: #e5e7eb;
        }
    </style>
</head>
<body>

<div class="auth-shell">
    <div class="auth-card">

        <div class="logo-circle">
            <!-- ممكن تحط أول حرف من اسم البراند -->
            م
        </div>

        <h1 class="auth-title">تسجيل الدخول إلى لوحة المطعم</h1>
        <p class="auth-subtitle">
            أدخل بريدك الإلكتروني وكلمة المرور الخاصة بحساب المطعم للوصول إلى لوحة التحكم وإدارة المنيو والفروع.
        </p>

        @if ($errors->has('email'))
            <div class="alert alert-error">
                {{ $errors->first('email') }}
            </div>
        @endif

        <form method="POST" action="{{ route('restaurant.login.attempt') }}">
            @csrf

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
                    autofocus
                >
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
            </div>

            <div class="form-row-inline">
                <label class="form-check">
                    <input
                        type="checkbox"
                        name="remember"
                        class="form-check-input"
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    <span>تذكرني على هذا الجهاز</span>
                </label>

                {{-- لو عملت route لنسيت كلمة المرور حطّه هنا --}}
                {{-- <a href="{{ route('password.request') }}" class="link-sm">نسيت كلمة المرور؟</a> --}}
            </div>

            <button type="submit" class="btn-primary">
                <span>دخول إلى لوحة المطعم</span>
                <span class="btn-primary-icon">→</span>
            </button>
        </form>

        <div class="auth-alt">
            لا تمتلك حساب مطعم بعد؟
            <a href="{{ route('restaurant.register') }}">إنشاء حساب مطعم جديد</a>
        </div>

        <div class="small-note">
            <span>نصيحة:</span> لا تشارك بيانات الدخول مع أي شخص غير موثوق، وخصص حسابات منفصلة للمديرين والموظفين من داخل لوحة المطعم.
        </div>

    </div>
</div>

</body>
</html>
