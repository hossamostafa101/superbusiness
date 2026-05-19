{{-- resources/views/restaurant/plans/index.blade.php --}}
@extends('restaurant.layouts.app')

@section('title', 'خطط الاشتراك للمطعم')

@section('content')
<div class="container py-5">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    <div class="text-center mb-4">
        <h1 class="h3 mb-2">خطط الاشتراك</h1>
        <p class="text-muted mb-0">
            مرحبًا {{ $user->name }}، اختر الخطة المناسبة لمطعمك
            <strong>{{ $restaurant->name }}</strong>.
        </p>
    </div>

    {{-- الاشتراك الحالي إن وجد --}}
    @if($restaurant->activeSubscription && $restaurant->activeSubscription->plan)
        @php
            $currentSub  = $restaurant->activeSubscription;
            $currentPlan = $currentSub->plan;
        @endphp

        <div class="alert alert-info d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-4">
            <div>
                <div class="fw-semibold mb-1">
                    الخطة الحالية: {{ $currentPlan->name }}
                </div>
                <div class="small text-muted">
                    الحالة: {{ $currentSub->status }} /
                    من {{ optional($currentSub->starts_at)->format('Y-m-d') ?: 'غير محدد' }}
                    إلى {{ optional($currentSub->ends_at)->format('Y-m-d') ?: 'غير محدد' }}
                </div>
            </div>
            <div class="small text-muted">
                يمكنك تغيير الخطة باختيار خطة أخرى وإتمام الدفع اليدوي لها.
            </div>
        </div>
    @endif

    @if($plans->isEmpty())
        <div class="alert alert-warning text-center">
            لا توجد خطط متاحة حاليًا. برجاء التواصل مع الإدارة.
        </div>
    @else
        <div class="row g-4">
            @foreach($plans as $plan)
                @php
                    $features = is_array($plan->features_json) ? $plan->features_json : [];

                    $activeSub      = $restaurant->activeSubscription;
                    $isCurrentPlan  = $activeSub && (int)$activeSub->plan_id === (int)$plan->id;

                    // هل يوجد طلب دفع pending لهذه الخطة؟
                    $planPendingRequests = $pendingRequests[$plan->id] ?? collect();
                    $hasPending          = $planPendingRequests->isNotEmpty();
                @endphp

                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column">

                            {{-- عنوان الخطة --}}
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <h2 class="h5 mb-1">{{ $plan->name }}</h2>

                                    @if($isCurrentPlan)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            الخطة الحالية
                                        </span>
                                    @elseif($hasPending)
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                            طلب دفع قيد المراجعة
                                        </span>
                                    @endif
                                </div>

                                <div class="d-flex align-items-baseline gap-1 mt-1">
                                    <span class="fw-bold fs-4">
                                        {{ number_format($plan->price, 2) }}
                                    </span>
                                    <span class="text-muted">
                                        / {{ $plan->duration_days }} يوم
                                    </span>
                                </div>
                            </div>

                            {{-- المميزات --}}
                            <div class="mb-3">
                                <h3 class="h6 text-muted mb-2">ما الذي ستستفيد به في هذه الخطة؟</h3>

                                @if(!empty($features))
                                    <ul class="list-unstyled small mb-0">
                                        @foreach($features as $feature)
                                            <li class="d-flex align-items-start mb-1">
                                                <span class="me-2 text-success" style="margin-top: 3px;">✓</span>
                                                <span>{{ $feature }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted small mb-0">
                                        لا توجد مميزات مفصّلة مسجَّلة لهذه الخطة بعد.
                                    </p>
                                @endif
                            </div>

                            <div class="mt-auto pt-3">
                                @if(!$plan->is_active)
                                    <button class="btn btn-outline-secondary w-100" disabled>
                                        هذه الخطة غير متاحة حاليًا
                                    </button>
                                @elseif($isCurrentPlan)
                                    <button class="btn btn-success w-100" disabled>
                                        هذه هي الخطة الحالية
                                    </button>
                                @elseif($hasPending)
                                    <button class="btn btn-outline-warning w-100" disabled>
                                        طلب الدفع لهذه الخطة قيد المراجعة
                                    </button>
                                @else
                                    {{-- الذهاب لصفحة الـ Checkout لاختيار طريقة الدفع ورفع الإيصال --}}
                                    <a href="{{ route('restaurant.plans.checkout', $plan) }}"
                                       class="btn btn-primary w-100">
                                        الاشتراك في هذه الخطة
                                    </a>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
