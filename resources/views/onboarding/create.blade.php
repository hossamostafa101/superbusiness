{{-- resources/views/onboarding/create.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعداد مساحة العمل</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(37,99,235,.12), transparent 34%),
                #f6f7fb;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .setup-wrapper {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .setup-card {
            width: 100%;
            max-width: 760px;
            background: #fff;
            border-radius: 28px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .08);
            overflow: hidden;
        }

        .setup-head {
            background: linear-gradient(135deg, #111827, #2563eb);
            color: #fff;
            padding: 34px;
        }

        .spec-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .spec-card {
            position: relative;
            cursor: pointer;
        }

        .spec-card input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .spec-box {
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 16px;
            height: 100%;
            background: #fff;
            transition: .18s ease;
        }

        .spec-card input:checked + .spec-box {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .12);
        }

        .spec-icon {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: #eff6ff;
            color: #2563eb;
            display: grid;
            place-items: center;
            font-size: 20px;
            margin-bottom: 12px;
        }

        @media (max-width: 575px) {
            .spec-grid {
                grid-template-columns: 1fr;
            }

            .setup-head {
                padding: 24px;
            }
        }
    </style>
</head>
<body>

<div class="setup-wrapper">
    <div class="setup-card">
        <div class="setup-head">
            <span class="badge bg-light text-dark mb-3">إعداد سريع</span>
            <h1 class="h3 fw-bold mb-2">اختر نوع نشاطك</h1>
            <p class="mb-0 opacity-75">
                سنجهز لوحة التحكم والموديولات المناسبة حسب نوع النشاط.
            </p>
        </div>

        <div class="p-4 p-md-5">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('onboarding.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="form-label">اسم البزنس <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        name="business_name"
                        value="{{ old('business_name') }}"
                        class="form-control form-control-lg"
                        required
                        placeholder="مثال: Luna Cafe"
                    >
                </div>

                <div class="mb-4">
                    <label class="form-label d-block">
                        نوع النشاط <span class="text-danger">*</span>
                    </label>

                    <div class="spec-grid">
                        @foreach($specifications as $specification)
                            @php
                                $icon = match ($specification->key) {
                                    'restaurant' => 'bi-cup-hot',
                                    'bio' => 'bi-link-45deg',
                                    'appointments' => 'bi-calendar-check',
                                    default => 'bi-briefcase',
                                };

                                $defaultChecked = old('specification_id')
                                    ? (int) old('specification_id') === (int) $specification->id
                                    : $specification->key === 'restaurant';
                            @endphp

                            <label class="spec-card">
                                <input
                                    type="radio"
                                    name="specification_id"
                                    value="{{ $specification->id }}"
                                    @checked($defaultChecked)
                                    required
                                >

                                <div class="spec-box">
                                    <div class="spec-icon">
                                        <i class="bi {{ $icon }}"></i>
                                    </div>

                                    <div class="fw-bold mb-1">
                                        {{ $specification->name }}
                                    </div>

                                    <div class="small text-muted">
                                        {{ $specification->description }}
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">رقم واتساب</label>
                    <input
                        type="text"
                        name="whatsapp_number"
                        value="{{ old('whatsapp_number') }}"
                        class="form-control"
                        placeholder="2010xxxxxxxx"
                    >
                    <div class="form-text">
                        اكتب الرقم بصيغة دولية بدون علامة +.
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-lg">
                    إنشاء مساحة العمل
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>