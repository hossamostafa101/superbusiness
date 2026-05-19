{{-- resources/views/auth/register.blade.php --}}
@extends('auth.layout')

@section('title', 'إنشاء حساب | Super Business')

@section('auth-body')
<div class="mb-4">
    <span class="badge rounded-pill text-bg-primary bg-opacity-10 text-primary border border-primary-subtle mb-3">
        <i class="bi bi-rocket-takeoff"></i>
        ابدأ مجانًا
    </span>

    <h1 class="h3 fw-bold mb-2">أنشئ صفحة بزنس ذكية</h1>
    <p class="text-muted mb-0">
        حساب واحد لإدارة صفحتك، روابطك، منتجاتك، واستقبال الطلبات عبر واتساب.
    </p>
</div>

@include('auth.partials.session-status', ['status' => session('status')])

@if($errors->any())
    <div class="alert alert-danger auth-alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('register') }}" novalidate>
    @csrf

    <div class="mb-3">
        <label for="name" class="form-label fw-semibold">اسمك</label>

        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-person"></i>
            </span>

            <input
                id="name"
                type="text"
                name="name"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                placeholder="مثال: أحمد محمد"
            >
        </div>

        @include('auth.partials.input-error', ['messages' => $errors->get('name')])
    </div>

    <div class="mb-3">
        <label for="email" class="form-label fw-semibold">البريد الإلكتروني</label>

        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-envelope"></i>
            </span>

            <input
                id="email"
                type="email"
                name="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}"
                required
                autocomplete="username"
                placeholder="name@example.com"
            >
        </div>

        @include('auth.partials.input-error', ['messages' => $errors->get('email')])
    </div>

    <div class="mb-3">
        <label for="username" class="form-label fw-semibold">اسم المستخدم</label>

        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-at"></i>
            </span>

            <input
                id="username"
                type="text"
                name="username"
                class="form-control @error('username') is-invalid @enderror"
                value="{{ old('username') }}"
                placeholder="مثال: luna_store"
                autocomplete="username"
            >
        </div>

        @include('auth.partials.input-error', ['messages' => $errors->get('username')])

        <div class="form-text small">
            اختياري. يمكن استخدامه لاحقًا في رابط أو هوية حسابك.
        </div>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label fw-semibold">كلمة المرور</label>

        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-lock"></i>
            </span>

            <input
                id="password"
                type="password"
                name="password"
                class="form-control @error('password') is-invalid @enderror"
                required
                autocomplete="new-password"
                placeholder="••••••••"
            >

            <button class="btn btn-outline-secondary" type="button" data-toggle-password="password">
                <i class="bi bi-eye"></i>
            </button>
        </div>

        @include('auth.partials.input-error', ['messages' => $errors->get('password')])
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label fw-semibold">تأكيد كلمة المرور</label>

        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-shield-check"></i>
            </span>

            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                class="form-control @error('password_confirmation') is-invalid @enderror"
                required
                autocomplete="new-password"
                placeholder="••••••••"
            >

            <button class="btn btn-outline-secondary" type="button" data-toggle-password="password_confirmation">
                <i class="bi bi-eye"></i>
            </button>
        </div>

        @include('auth.partials.input-error', ['messages' => $errors->get('password_confirmation')])
    </div>

    <div class="rounded-4 border bg-light p-3 mb-3">
        <div class="form-check mb-2">
            <input
                class="form-check-input @error('accept_gdpr') is-invalid @enderror"
                type="checkbox"
                value="1"
                id="accept_gdpr"
                name="accept_gdpr"
                @checked(old('accept_gdpr'))
                required
            >

            <label class="form-check-label small" for="accept_gdpr">
                أوافق على معالجة بياناتي لتشغيل حسابي وخدماتي داخل المنصة.
            </label>

            @include('auth.partials.input-error', ['messages' => $errors->get('accept_gdpr')])
        </div>

        <div class="form-check">
            <input
                class="form-check-input @error('accept_terms') is-invalid @enderror"
                type="checkbox"
                value="1"
                id="accept_terms"
                name="accept_terms"
                @checked(old('accept_terms'))
                required
            >

            <label class="form-check-label small" for="accept_terms">
                أوافق على
                <a href="{{ url('/terms') }}" target="_blank" class="auth-link">الشروط</a>
                و
                <a href="{{ url('/privacy') }}" target="_blank" class="auth-link">سياسة الخصوصية</a>.
            </label>

            @include('auth.partials.input-error', ['messages' => $errors->get('accept_terms')])
        </div>
    </div>

    <div class="form-check mb-3">
        <input
            class="form-check-input"
            type="checkbox"
            value="1"
            id="marketing_opt_in"
            name="marketing_opt_in"
            @checked(old('marketing_opt_in'))
        >

        <label class="form-check-label small" for="marketing_opt_in">
            أريد استقبال تحديثات ونصائح لتحسين صفحة البزنس.
        </label>
    </div>

    <button id="registerBtn" class="btn btn-sb w-100" type="submit" disabled>
        إنشاء الحساب
        <i class="bi bi-arrow-left-short"></i>
    </button>

    <div class="text-center small mt-4">
        لديك حساب بالفعل؟
        <a href="{{ route('login') }}" class="auth-link">
            تسجيل الدخول
        </a>
    </div>
</form>

@push('scripts')
<script>
    (function () {
        const gdpr = document.getElementById('accept_gdpr');
        const terms = document.getElementById('accept_terms');
        const btn = document.getElementById('registerBtn');

        function toggleBtn() {
            btn.disabled = !(gdpr?.checked && terms?.checked);
        }

        toggleBtn();

        gdpr?.addEventListener('change', toggleBtn);
        terms?.addEventListener('change', toggleBtn);

        document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
            button.addEventListener('click', function () {
                const inputId = button.getAttribute('data-toggle-password');
                const input = document.getElementById(inputId);
                const icon = button.querySelector('i');

                if (!input) return;

                const isPassword = input.type === 'password';

                input.type = isPassword ? 'text' : 'password';
                icon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';

                input.focus();
            });
        });
    })();
</script>
@endpush
@endsection