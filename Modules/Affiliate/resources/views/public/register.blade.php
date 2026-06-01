<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل مسوق</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h1 class="h4 fw-bold mb-4">
                        تسجيل كمسوق
                    </h1>

                   <form method="POST" action="{{ route('public.affiliate.register.store') }}">
    @csrf

    <div class="mb-3">
        <label class="form-label">الاسم</label>
        <input
            type="text"
            name="name"
            value="{{ old('name') }}"
            class="form-control @error('name') is-invalid @enderror"
            required
        >

        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">البريد الإلكتروني</label>
        <input
            type="email"
            name="email"
            value="{{ old('email') }}"
            class="form-control @error('email') is-invalid @enderror"
            dir="ltr"
            required
        >

        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">رقم الهاتف</label>
        <input
            type="text"
            name="phone"
            value="{{ old('phone') }}"
            class="form-control @error('phone') is-invalid @enderror"
            dir="ltr"
        >

        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">رقم واتساب</label>
        <input
            type="text"
            name="whatsapp_number"
            value="{{ old('whatsapp_number') }}"
            class="form-control @error('whatsapp_number') is-invalid @enderror"
            dir="ltr"
        >

        @error('whatsapp_number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">كلمة المرور</label>
        <input
            type="password"
            name="password"
            class="form-control @error('password') is-invalid @enderror"
            required
        >

        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-4">
        <label class="form-label">تأكيد كلمة المرور</label>
        <input
            type="password"
            name="password_confirmation"
            class="form-control"
            required
        >
    </div>

    <button class="btn btn-primary w-100">
        إنشاء حساب مسوق
    </button>
</form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>