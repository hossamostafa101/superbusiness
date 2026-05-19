{{-- resources/views/auth/login.blade.php --}}
@extends('auth.layout')

@section('title', 'تسجيل الدخول | Super Business')

@section('auth-body')
<div class="mb-4">
    <span class="badge rounded-pill text-bg-primary bg-opacity-10 text-primary border border-primary-subtle mb-3">
        <i class="bi bi-stars"></i>
        إدارة صفحة البزنس
    </span>

    <h1 class="h3 fw-black fw-bold mb-2">أهلًا بعودتك</h1>
    <p class="text-muted mb-0">
        سجل دخولك لإدارة الصفحة، الروابط، المنتجات، والتحليلات.
    </p>
</div>

@include('auth.partials.session-status', ['status' => session('status')])

@if(session('error'))
    <div class="alert alert-danger auth-alert">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger auth-alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('login') }}" novalidate>
    @csrf

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
                autofocus
                autocomplete="username"
                placeholder="name@example.com"
            >
        </div>

        @include('auth.partials.input-error', ['messages' => $errors->get('email')])
    </div>

    <div class="mb-2">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="password" class="form-label fw-semibold mb-0">كلمة المرور</label>

            @if (Route::has('password.request'))
                <a class="small auth-link" href="{{ route('password.request') }}">
                    نسيت كلمة المرور؟
                </a>
            @endif
        </div>

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
                autocomplete="current-password"
                placeholder="••••••••"
            >

            <button class="btn btn-outline-secondary" type="button" id="togglePasswordBtn" aria-label="إظهار كلمة المرور">
                <i class="bi bi-eye" id="togglePasswordIcon"></i>
            </button>
        </div>

        @include('auth.partials.input-error', ['messages' => $errors->get('password')])
    </div>

    <div class="d-flex align-items-center justify-content-between my-3">
        <div class="form-check">
            <input
                class="form-check-input"
                type="checkbox"
                value="1"
                id="remember_me"
                name="remember"
                @checked(old('remember'))
            >
            <label class="form-check-label small" for="remember_me">
                تذكرني
            </label>
        </div>
    </div>

    <button class="btn btn-sb w-100" type="submit">
        دخول إلى لوحة البزنس
        <i class="bi bi-arrow-left-short"></i>
    </button>

    @if (Route::has('register'))
        <div class="text-center small mt-4">
            ليس لديك حساب؟
            <a href="{{ route('register') }}" class="auth-link">
                أنشئ صفحة بزنس الآن
            </a>
        </div>
    @endif
</form>

@push('scripts')
<script>
    (function () {
        const input = document.getElementById('password');
        const btn = document.getElementById('togglePasswordBtn');
        const icon = document.getElementById('togglePasswordIcon');

        btn?.addEventListener('click', function () {
            const isPassword = input.type === 'password';

            input.type = isPassword ? 'text' : 'password';
            icon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';

            input.focus();
        });
    })();
</script>
@endpush
@endsection