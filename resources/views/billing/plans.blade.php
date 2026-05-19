{{-- resources/views/billing/plans.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختيار الباقة</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f6f7fb;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .plan-card {
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            transition: .2s ease;
            height: 100%;
        }

        .plan-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0,0,0,.08);
        }

        .featured {
            border: 2px solid #0d6efd;
        }

        .badge-featured {
            position: absolute;
            top: 16px;
            left: 16px;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="mb-5 text-center">
        <h1 class="fw-bold mb-2">اختر الباقة المناسبة</h1>
        <p class="text-muted mb-1">
            مساحة العمل:
            <strong>{{ $workspace->name }}</strong>
        </p>

        @if($workspace->activeSubscription?->plan)
            <p class="text-muted">
                باقتك الحالية:
                <span class="badge bg-success">
                    {{ $workspace->activeSubscription->plan->name }}
                </span>
            </p>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row g-4">
        @foreach($plans as $plan)
            <div class="col-md-6 col-lg-3">
                <div class="card plan-card position-relative {{ $plan->is_featured ? 'featured' : '' }}">
                    @if($plan->is_featured)
                        <span class="badge bg-primary badge-featured">
                            الأكثر اختيارًا
                        </span>
                    @endif

                    <div class="card-body p-4">
                        <h3 class="h4 fw-bold mb-2">{{ $plan->name }}</h3>

                        @if($plan->description)
                            <p class="text-muted small">
                                {{ $plan->description }}
                            </p>
                        @endif

                        <div class="my-4">
                            <div class="d-flex align-items-end gap-1">
                                <span class="display-6 fw-bold">
                                    {{ number_format((float) $plan->monthly_price, 0) }}
                                </span>
                                <span class="text-muted mb-2">
                                    {{ $plan->currency }} / شهر
                                </span>
                            </div>

                            @if($plan->yearly_price)
                                <div class="text-muted small mt-1">
                                    سنويًا:
                                    {{ number_format((float) $plan->yearly_price, 0) }}
                                    {{ $plan->currency }}
                                </div>
                            @endif
                        </div>

                        @if($workspace->specification)
    <div class="alert alert-light border text-center">
        الباقات المعروضة مناسبة لنوع النشاط:
        <strong>{{ $workspace->specification->name }}</strong>
    </div>
@endif

                        <ul class="list-unstyled small mb-4">
                            @foreach($plan->features as $feature)
                                @php
                                    $value = $feature->pivot->value;
                                @endphp

                                <li class="mb-2 d-flex align-items-start gap-2">
                                    <i class="bi bi-check-circle text-success"></i>

                                    <span>
                                        {{ $feature->name }}:

                                        @if($feature->type === 'boolean')
                                            {{ (string) $value === '1' ? 'متاح' : 'غير متاح' }}
                                        @elseif((string) $value === '-1')
                                            غير محدود
                                        @else
                                            {{ $value }}
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>

                        @if($workspace->activeSubscription?->plan_id === $plan->id)
                            <button class="btn btn-outline-success w-100" disabled>
                                الباقة الحالية
                            </button>
                        @else
                            <a href="{{ route('billing.checkout', [$workspace, $plan->slug]) }}" class="btn btn-primary w-100">
    اختيار الباقة
</a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

</body>
</html>