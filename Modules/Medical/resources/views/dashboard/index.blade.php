@extends('app.layouts.app')

@section('title', 'النظام الطبي')
@section('page_title', 'النظام الطبي')
@section('page_description', 'نظرة عامة على الحجوزات والمرضى والتشغيل اليومي.')

@section('content')
<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="{{ route('app.medical.appointments.create', $workspace) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        حجز جديد
    </a>

    <a href="{{ route('app.medical.patients.create', $workspace) }}" class="btn btn-outline-primary">
        <i class="bi bi-person-plus"></i>
        مريض جديد
    </a>

    <a href="{{ route('public.medical.booking.create', $workspace) }}" target="_blank" class="btn btn-outline-dark">
        <i class="bi bi-box-arrow-up-left"></i>
        صفحة الحجز العامة
    </a>
</div>


<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="medical-stat-card stat-primary">
            <div>
                <span>حجوزات اليوم</span>
                <strong>{{ $stats['today_total'] }}</strong>
            </div>

            <i class="bi bi-calendar-check"></i>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="medical-stat-card stat-warning">
            <div>
                <span>في الانتظار</span>
                <strong>{{ $stats['pending'] }}</strong>
            </div>

            <i class="bi bi-hourglass-split"></i>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="medical-stat-card stat-info">
            <div>
                <span>وصلوا اليوم</span>
                <strong>{{ $stats['checked_in'] }}</strong>
            </div>

            <i class="bi bi-person-check"></i>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="medical-stat-card stat-success">
            <div>
                <span>مكتمل اليوم</span>
                <strong>{{ $stats['completed'] }}</strong>
            </div>

            <i class="bi bi-check-circle"></i>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="medical-mini-card">
            <span>المرضى</span>
            <strong>{{ $stats['patients'] }}</strong>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="medical-mini-card">
            <span>الفريق النشط</span>
            <strong>{{ $stats['active_staff'] }}</strong>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="medical-mini-card">
            <span>الخدمات النشطة</span>
            <strong>{{ $stats['active_services'] }}</strong>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="medical-mini-card">
            <span>الفروع النشطة</span>
            <strong>{{ $stats['branches'] }}</strong>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card content-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h5 fw-bold mb-1">
                            مواعيد اليوم القادمة
                        </h2>

                        <p class="text-muted mb-0">
                            الحجوزات التي لم تكتمل بعد.
                        </p>
                    </div>

                    <a href="{{ route('app.medical.appointments.board', $workspace) }}" class="btn btn-sm btn-outline-dark">
                        لوحة اليوم
                    </a>
                </div>

                @forelse($upcomingAppointments as $appointment)
                    <div class="medical-appointment-row">
                        <div class="time" dir="ltr">
                            {{ \Illuminate\Support\Carbon::parse($appointment->starts_at)->format('H:i') }}
                        </div>

                        <div class="flex-grow-1">
                            <div class="fw-bold">
                                {{ $appointment->patient?->full_name ?: $appointment->patient_name }}
                            </div>

                            <div class="small text-muted">
                                {{ $appointment->service?->name ?: $appointment->service_name }}
                                @if($appointment->staff)
                                    · {{ $appointment->staff->name }}
                                @endif
                            </div>
                        </div>

                        <span class="badge {{ $appointment->statusBadgeClass() }}">
                            {{ $appointment->statusLabel() }}
                        </span>

                        <a href="{{ route('app.medical.appointments.show', [$workspace, $appointment]) }}" class="btn btn-sm btn-light border">
                            عرض
                        </a>
                    </div>
                @empty
                    <div class="text-center text-muted py-5">
                        لا توجد مواعيد قادمة اليوم.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card content-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h5 fw-bold mb-1">
                            أحدث المرضى
                        </h2>

                        <p class="text-muted mb-0">
                            آخر ملفات تم إضافتها.
                        </p>
                    </div>

                    <a href="{{ route('app.medical.patients.index', $workspace) }}" class="btn btn-sm btn-outline-dark">
                        الكل
                    </a>
                </div>

                @forelse($latestPatients as $patient)
                    <a
                        href="{{ route('app.medical.patients.show', [$workspace, $patient]) }}"
                        class="medical-patient-row"
                    >
                        <div class="avatar">
                            {{ mb_substr($patient->full_name, 0, 1) }}
                        </div>

                        <div>
                            <div class="fw-bold">
                                {{ $patient->full_name }}
                            </div>

                            <div class="small text-muted" dir="ltr">
                                {{ $patient->phone ?: $patient->patient_code ?: '-' }}
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center text-muted py-5">
                        لا يوجد مرضى بعد.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    .medical-stat-card {
        min-height: 128px;
        border-radius: 24px;
        padding: 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #fff;
        box-shadow: 0 16px 36px rgba(15, 23, 42, .10);
    }

    .medical-stat-card span {
        display: block;
        font-size: 13px;
        opacity: .86;
        margin-bottom: 8px;
    }

    .medical-stat-card strong {
        display: block;
        font-size: 34px;
        font-weight: 950;
        line-height: 1;
    }

    .medical-stat-card i {
        font-size: 34px;
        opacity: .9;
    }

    .stat-primary {
        background: linear-gradient(135deg, #2563eb, #1e40af);
    }

    .stat-warning {
        background: linear-gradient(135deg, #f59e0b, #b45309);
    }

    .stat-info {
        background: linear-gradient(135deg, #06b6d4, #0369a1);
    }

    .stat-success {
        background: linear-gradient(135deg, #16a34a, #166534);
    }

    .medical-mini-card {
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        padding: 18px;
        background: #fff;
        box-shadow: 0 10px 26px rgba(15, 23, 42, .04);
    }

    .medical-mini-card span {
        display: block;
        font-size: 13px;
        color: #64748b;
        margin-bottom: 8px;
    }

    .medical-mini-card strong {
        font-size: 26px;
        font-weight: 950;
        color: #0f172a;
    }

    .medical-appointment-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #edf0f4;
    }

    .medical-appointment-row:last-child {
        border-bottom: 0;
    }

    .medical-appointment-row .time {
        min-width: 58px;
        height: 34px;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 13px;
    }

    .medical-patient-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #edf0f4;
        color: inherit;
        text-decoration: none;
    }

    .medical-patient-row:last-child {
        border-bottom: 0;
    }

    .medical-patient-row .avatar {
        width: 42px;
        height: 42px;
        border-radius: 16px;
        background: #eff6ff;
        color: #1d4ed8;
        display: grid;
        place-items: center;
        font-weight: 950;
    }

    @media (max-width: 576px) {
        .medical-stat-card {
            min-height: 105px;
            padding: 18px;
        }

        .medical-stat-card strong {
            font-size: 28px;
        }

        .medical-appointment-row {
            align-items: flex-start;
            flex-wrap: wrap;
        }
    }
</style>
@endsection