{{-- resources/views/billing/checkout.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إتمام الدفع</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f6f7fb;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .checkout-card {
            border-radius: 18px;
            border: 1px solid #e5e7eb;
        }

        .payment-method {
            border: 1px solid #dee2e6;
            border-radius: 14px;
            padding: 14px;
            cursor: pointer;
        }

        .payment-method input {
            margin-left: 8px;
        }

        .payment-method:hover {
            border-color: #0d6efd;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="mb-4">
        <a href="{{ route('billing.plans', $workspace) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
            رجوع للباقات
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card checkout-card">
                <div class="card-body p-4">
                    <h1 class="h4 fw-bold mb-1">إتمام الدفع</h1>
                    <p class="text-muted mb-4">
                        اختر دورة الدفع وطريقة الدفع المناسبة.
                    </p>

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <strong>يرجى مراجعة الأخطاء التالية:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('billing.process', [$workspace, $plan]) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-semibold">دورة الدفع</label>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="payment-method w-100">
                                        <input
                                            type="radio"
                                            name="billing_cycle"
                                            value="monthly"
                                            @checked(old('billing_cycle', 'monthly') === 'monthly')
                                        >
                                        <strong>شهري</strong>
                                        <div class="text-muted small mt-1">
                                            {{ number_format((float) $plan->monthly_price, 2) }}
                                            {{ $plan->currency }}
                                        </div>
                                    </label>
                                </div>

                                <div class="col-md-6">
                                    <label class="payment-method w-100">
                                        <input
                                            type="radio"
                                            name="billing_cycle"
                                            value="yearly"
                                            @checked(old('billing_cycle') === 'yearly')
                                        >
                                        <strong>سنوي</strong>
                                        <div class="text-muted small mt-1">
                                            {{ number_format((float) ($plan->yearly_price ?? $plan->monthly_price * 10), 2) }}
                                            {{ $plan->currency }}
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">طريقة الدفع</label>

                            <div class="vstack gap-3">
                                <label class="payment-method">
                                    <input
                                        type="radio"
                                        name="provider"
                                        value="manual"
                                        @checked(old('provider', 'manual') === 'manual')
                                        onchange="toggleManualFields()"
                                    >
                                    <strong>تحويل يدوي</strong>
                                    <div class="text-muted small mt-1">
                                        ارفع صورة إثبات الدفع وسيتم مراجعتها من الإدارة.
                                    </div>
                                </label>

                                <label class="payment-method">
                                    <input
                                        type="radio"
                                        name="provider"
                                        value="kashier"
                                        @checked(old('provider') === 'kashier')
                                        onchange="toggleManualFields()"
                                    >
                                    <strong>Kashier</strong>
                                    <div class="text-muted small mt-1">
                                        دفع إلكتروني عبر Kashier.
                                    </div>
                                </label>

                                <label class="payment-method">
                                    <input
                                        type="radio"
                                        name="provider"
                                        value="paddle"
                                        @checked(old('provider') === 'paddle')
                                        onchange="toggleManualFields()"
                                    >
                                    <strong>Paddle</strong>
                                    <div class="text-muted small mt-1">
                                        دفع إلكتروني عبر Paddle.
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div id="manual-fields" class="border rounded p-3 mb-4">
                            <h6 class="fw-bold mb-3">بيانات الدفع اليدوي</h6>

                            <div class="alert alert-info small">
                                ضع هنا تعليمات الدفع اليدوي الخاصة بك لاحقًا، مثل رقم InstaPay أو Vodafone Cash أو بيانات التحويل البنكي.
                            </div>

                            <div class="mb-3">
                                <label class="form-label">رقم العملية أو المرجع</label>
                                <input
                                    type="text"
                                    name="reference"
                                    value="{{ old('reference') }}"
                                    class="form-control @error('reference') is-invalid @enderror"
                                    placeholder="مثال: رقم عملية InstaPay"
                                >

                                @error('reference')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">صورة إثبات الدفع <span class="text-danger">*</span></label>
                                <input
                                    type="file"
                                    name="receipt_image"
                                    class="form-control @error('receipt_image') is-invalid @enderror"
                                    accept="image/*"
                                >

                                @error('receipt_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <small class="text-muted">
                                    الصيغ المسموحة: JPG, PNG, WEBP. الحد الأقصى 4MB.
                                </small>
                            </div>

                            <div>
                                <label class="form-label">ملاحظات</label>
                                <textarea
                                    name="notes"
                                    rows="3"
                                    class="form-control @error('notes') is-invalid @enderror"
                                    placeholder="أي ملاحظات إضافية"
                                >{{ old('notes') }}</textarea>

                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            متابعة الدفع
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card checkout-card">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">ملخص الطلب</h2>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">مساحة العمل</span>
                        <strong>{{ $workspace->name }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">الباقة</span>
                        <strong>{{ $plan->name }}</strong>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <div class="text-muted small">سعر شهري</div>
                        <div class="fw-bold">
                            {{ number_format((float) $plan->monthly_price, 2) }}
                            {{ $plan->currency }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small">سعر سنوي</div>
                        <div class="fw-bold">
                            {{ number_format((float) ($plan->yearly_price ?? $plan->monthly_price * 10), 2) }}
                            {{ $plan->currency }}
                        </div>
                    </div>

                    <hr>

                    <h6 class="fw-bold mb-2">مميزات الباقة</h6>

                    <ul class="list-unstyled small mb-0">
                        @foreach($plan->features as $feature)
                            @php
                                $value = $feature->pivot->value;
                            @endphp

                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success"></i>
                                {{ $feature->name }}:

                                @if($feature->type === 'boolean')
                                    {{ (string) $value === '1' ? 'متاح' : 'غير متاح' }}
                                @elseif((string) $value === '-1')
                                    غير محدود
                                @else
                                    {{ $value }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleManualFields() {
        const selected = document.querySelector('input[name="provider"]:checked')?.value;
        const manualFields = document.getElementById('manual-fields');

        if (!manualFields) {
            return;
        }

        manualFields.style.display = selected === 'manual' ? 'block' : 'none';
    }

    document.addEventListener('DOMContentLoaded', toggleManualFields);
</script>

</body>
</html>