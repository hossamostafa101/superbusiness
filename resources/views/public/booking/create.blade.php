{{-- resources/views/public/booking/create.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حجز موعد | {{ $profile->display_name }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --theme-color: {{ $profile->theme_color ?: '#111827' }};
            --button-color: {{ $profile->button_color ?: '#2563eb' }};
        }

        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.14), transparent 32%),
                linear-gradient(180deg, var(--theme-color), #f6f7fb 52%);
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .page-wrapper {
            max-width: 560px;
            margin: 0 auto;
            padding: 24px 14px 40px;
        }

        .booking-card {
            background: #fff;
            border-radius: 26px;
            border: 1px solid #eef0f3;
            box-shadow: 0 18px 50px rgba(0,0,0,.12);
            overflow: hidden;
        }

        .booking-header {
            background: linear-gradient(135deg, var(--theme-color), var(--button-color));
            color: #fff;
            padding: 26px;
        }

        .btn-main {
            background: var(--button-color);
            color: #fff;
            border: 0;
            border-radius: 14px;
            min-height: 48px;
            font-weight: 800;
        }

        .btn-main:hover {
            color: #fff;
            filter: brightness(.96);
        }

        .form-control,
        .form-select {
            min-height: 46px;
            border-radius: 14px;
        }
    </style>
</head>
<body>

<div class="page-wrapper">
    <div class="mb-3">
        <a href="{{ route('public.business-page.show', $workspace) }}" class="btn btn-light btn-sm">
            <i class="bi bi-arrow-right"></i>
            رجوع للصفحة
        </a>
    </div>

    <div class="booking-card">
        <div class="booking-header">
            <h1 class="h4 fw-bold mb-2">حجز موعد</h1>
            <p class="mb-0 opacity-75">
                {{ $profile->display_name }}
            </p>
        </div>

        <div class="p-4">
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>يرجى مراجعة البيانات:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('public.booking.store', $workspace) }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">الخدمة</label>
                    <select name="service_id" class="form-select @error('service_id') is-invalid @enderror">
                        <option value="">اختر الخدمة</option>

                        @foreach($services as $service)
                            <option
                                value="{{ $service->id }}"
                                @selected((int) old('service_id') === (int) $service->id)
                            >
                                {{ $service->name }}
                                —
                                {{ $service->duration_minutes }} دقيقة
                                @if($service->price !== null)
                                    —
                                    {{ number_format((float) $service->price, 2) }} {{ $service->currency }}
                                @endif
                            </option>
                        @endforeach
                    </select>

                    @error('service_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    @if($services->isEmpty())
                        <small class="text-muted">
                            يمكنك ترك الخدمة فارغة وسيتم التواصل معك للتحديد.
                        </small>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label">الاسم <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        name="customer_name"
                        value="{{ old('customer_name') }}"
                        class="form-control @error('customer_name') is-invalid @enderror"
                        required
                    >

                    @error('customer_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        name="customer_phone"
                        value="{{ old('customer_phone') }}"
                        class="form-control @error('customer_phone') is-invalid @enderror"
                        required
                        placeholder="2010xxxxxxxx"
                    >

                    @error('customer_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input
                        type="email"
                        name="customer_email"
                        value="{{ old('customer_email') }}"
                        class="form-control @error('customer_email') is-invalid @enderror"
                    >

                    @error('customer_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">تاريخ الموعد <span class="text-danger">*</span></label>
                        <input
    type="date"
    name="appointment_date"
    value="{{ old('appointment_date', now()->format('Y-m-d')) }}"
    min="{{ now()->format('Y-m-d') }}"
    max="{{ now()->addDays($settings['booking_advance_days'])->format('Y-m-d') }}"
    class="form-control @error('appointment_date') is-invalid @enderror"
    required
>

                        @error('appointment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">وقت الموعد <span class="text-danger">*</span></label>
                        <input
    type="time"
    name="start_time"
    value="{{ old('start_time') }}"
    min="{{ $settings['booking_start_time'] }}"
    max="{{ $settings['booking_end_time'] }}"
    step="{{ $settings['booking_slot_interval'] * 60 }}"
    class="form-control @error('start_time') is-invalid @enderror"
    required
>

<small class="text-muted">
    الحجز متاح من {{ $settings['booking_start_time'] }} إلى {{ $settings['booking_end_time'] }}.
</small>
                        @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">ملاحظات</label>
                    <textarea
                        name="notes"
                        rows="4"
                        class="form-control @error('notes') is-invalid @enderror"
                        placeholder="اكتب أي تفاصيل إضافية"
                    >{{ old('notes') }}</textarea>

                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-main w-100 mt-4">
                    إرسال طلب الحجز
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>