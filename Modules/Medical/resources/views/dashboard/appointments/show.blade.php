@extends('app.layouts.app')

@section('title', 'تفاصيل الحجز')
@section('page_title', 'تفاصيل الحجز')
@section('page_description', $appointment->appointment_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('app.medical.appointments.index', $workspace) }}" class="btn btn-light">
        رجوع
    </a>

    

    <div class="d-flex gap-2">

        {{-- @if($appointment->patient_id)
    <form method="POST" action="{{ route('app.medical.appointments.start-visit', [$workspace, $appointment]) }}">
        @csrf

        <button class="btn btn-success">
            <i class="bi bi-play-circle"></i>
            بدء الزيارة
        </button>
    </form>
@endif --}}

@if($appointment->visit)
    <a href="{{ route('app.medical.visits.show', [$workspace, $appointment->visit]) }}" class="btn btn-success">
        <i class="bi bi-journal-medical"></i>
        عرض الزيارة
    </a>
@elseif($appointment->patient_id)
    <form method="POST" action="{{ route('app.medical.appointments.start-visit', [$workspace, $appointment]) }}">
        @csrf

        <button class="btn btn-success">
            <i class="bi bi-play-circle"></i>
            بدء الزيارة
        </button>
    </form>
@endif

        <a href="{{ route('app.medical.appointments.edit', [$workspace, $appointment]) }}" class="btn btn-primary">
            تعديل الحجز
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card content-card h-100">
            <div class="card-body p-4">
                <h2 class="h5 fw-bold mb-3">
                    بيانات الحجز
                </h2>

                <div class="mb-3">
                    <div class="text-muted small">رقم الحجز</div>
                    <div class="fw-bold" dir="ltr">
                        {{ $appointment->appointment_number }}
                    </div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small">الحالة</div>
                    <span class="badge {{ $appointment->statusBadgeClass() }}">
                        {{ $appointment->statusLabel() }}
                    </span>
                </div>

                <div class="mb-3">
                    <div class="text-muted small">حالة الدفع</div>
                    <span class="badge bg-light text-dark border">
                        {{ $appointment->paymentStatusLabel() }}
                    </span>
                </div>

                <div class="mb-3">
                    <div class="text-muted small">التاريخ</div>
                    <div class="fw-bold">
                        {{ $appointment->appointment_date?->format('Y-m-d') }}
                    </div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small">الوقت</div>
                    <div dir="ltr">
                        {{ \Illuminate\Support\Carbon::parse($appointment->starts_at)->format('H:i') }}
                        @if($appointment->ends_at)
                            -
                            {{ \Illuminate\Support\Carbon::parse($appointment->ends_at)->format('H:i') }}
                        @endif
                    </div>
                </div>

                <div>
                    <div class="text-muted small">المصدر</div>
                    <div>
                        {{ $appointment->source }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card content-card mb-4">
            <div class="card-body p-4">
                <h2 class="h5 fw-bold mb-3">
                    المريض والخدمة
                </h2>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">المريض</div>
                        <div class="fw-bold">
                            {{ $appointment->patient?->full_name ?: $appointment->patient_name }}
                        </div>

                        @if($appointment->patient_phone)
                            <div class="small text-muted" dir="ltr">
                                {{ $appointment->patient_phone }}
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted small">الخدمة</div>
                        <div class="fw-bold">
                            {{ $appointment->service?->name ?: $appointment->service_name }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted small">الطبيب / عضو الفريق</div>
                        <div>
                            {{ $appointment->staff?->name ?: $appointment->staff_name ?: '-' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted small">الفرع</div>
                        <div>
                            {{ $appointment->branch?->name ?: '-' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted small">السعر</div>
                        <div>
                            @if($appointment->price !== null)
                                <strong>
                                    {{ number_format((float) $appointment->price, 2) }}
                                </strong>
                                {{ $appointment->currency }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card content-card mb-4">
            <div class="card-body p-4">
                <h2 class="h5 fw-bold mb-3">
                    تحديث الحالة
                </h2>

                <form method="POST" action="{{ route('app.medical.appointments.update-status', [$workspace, $appointment]) }}">
                    @csrf
                    @method('PATCH')

                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label">الحالة الجديدة</label>

                            <select name="status" class="form-select">
                                <option value="pending" @selected($appointment->status === 'pending')>في الانتظار</option>
                                <option value="confirmed" @selected($appointment->status === 'confirmed')>مؤكد</option>
                                <option value="checked_in" @selected($appointment->status === 'checked_in')>وصل</option>
                                <option value="in_progress" @selected($appointment->status === 'in_progress')>جاري الكشف</option>
                                <option value="completed" @selected($appointment->status === 'completed')>مكتمل</option>
                                <option value="cancelled" @selected($appointment->status === 'cancelled')>ملغي</option>
                                <option value="no_show" @selected($appointment->status === 'no_show')>لم يحضر</option>
                            </select>
                        </div>

                        <div class="col-md-4 d-grid">
                            <button class="btn btn-dark">
                                تحديث الحالة
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card content-card">
            <div class="card-body p-4">
                <h2 class="h5 fw-bold mb-3">
                    الملاحظات
                </h2>

                <div class="mb-3">
                    <div class="text-muted small">ملاحظات المريض</div>
                    <div>
                        {{ $appointment->notes ?: '-' }}
                    </div>
                </div>

                <div>
                    <div class="text-muted small">ملاحظات داخلية</div>
                    <div>
                        {{ $appointment->internal_notes ?: '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection