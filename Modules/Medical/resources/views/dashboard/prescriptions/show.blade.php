@extends('app.layouts.app')

@section('title', 'تفاصيل الروشتة')
@section('page_title', 'تفاصيل الروشتة')
@section('page_description', $prescription->prescription_number)

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <a href="{{ route('app.medical.prescriptions.index', $workspace) }}" class="btn btn-light">
        رجوع
    </a>

    <div class="d-flex flex-wrap gap-2">
        @if($prescription->visit)
            <a href="{{ route('app.medical.visits.show', [$workspace, $prescription->visit]) }}" class="btn btn-outline-dark">
                الزيارة
            </a>
        @endif

        <a href="{{ route('app.medical.prescriptions.print', [$workspace, $prescription]) }}" target="_blank" class="btn btn-success">
            <i class="bi bi-printer"></i>
            طباعة
        </a>

        <a href="{{ route('app.medical.prescriptions.edit', [$workspace, $prescription]) }}" class="btn btn-primary">
            تعديل
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card content-card">
            <div class="card-body p-4">
                <h2 class="h5 fw-bold mb-3">
                    بيانات الروشتة
                </h2>

                <div class="rx-info-list">
                    <div>
                        <span>رقم الروشتة</span>
                        <strong dir="ltr">{{ $prescription->prescription_number }}</strong>
                    </div>

                    <div>
                        <span>الحالة</span>
                        <strong>
                            <span class="badge {{ $prescription->statusBadgeClass() }}">
                                {{ $prescription->statusLabel() }}
                            </span>
                        </strong>
                    </div>

                    <div>
                        <span>تاريخ الإصدار</span>
                        <strong dir="ltr">{{ $prescription->issued_at?->format('Y-m-d H:i') }}</strong>
                    </div>

                    <div>
                        <span>المريض</span>
                        <strong>{{ $prescription->patient?->full_name ?: $prescription->patient_name }}</strong>
                    </div>

                    <div>
                        <span>الطبيب</span>
                        <strong>{{ $prescription->staff?->name ?: $prescription->staff_name ?: '-' }}</strong>
                    </div>

                    @if($prescription->visit)
                        <div>
                            <span>الزيارة</span>
                            <strong>
                                <a href="{{ route('app.medical.visits.show', [$workspace, $prescription->visit]) }}">
                                    {{ $prescription->visit->visit_number }}
                                </a>
                            </strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card content-card mb-4">
            <div class="card-body p-4">
                <h2 class="h5 fw-bold mb-3">
                    الأدوية
                </h2>

                <div class="rx-items-list">
                    @forelse($prescription->items as $item)
                        <div class="rx-item-card">
                            <div class="fw-bold fs-6 mb-2">
                                {{ $item->medicine_name }}
                            </div>

                            <div class="row g-2 small">
                                <div class="col-md-3">
                                    <span class="text-muted">الجرعة:</span>
                                    <strong>{{ $item->dosage ?: '-' }}</strong>
                                </div>

                                <div class="col-md-3">
                                    <span class="text-muted">التكرار:</span>
                                    <strong>{{ $item->frequency ?: '-' }}</strong>
                                </div>

                                <div class="col-md-3">
                                    <span class="text-muted">المدة:</span>
                                    <strong>{{ $item->duration ?: '-' }}</strong>
                                </div>

                                <div class="col-md-3">
                                    <span class="text-muted">الطريقة:</span>
                                    <strong>{{ $item->route ?: '-' }}</strong>
                                </div>
                            </div>

                            @if($item->instructions)
                                <div class="small mt-2">
                                    {{ $item->instructions }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            لا توجد أدوية.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card content-card">
            <div class="card-body p-4">
                <h2 class="h5 fw-bold mb-3">
                    ملاحظات وتعليمات
                </h2>

                <div class="mb-3">
                    <div class="text-muted small mb-1">ملخص التشخيص</div>
                    <div class="white-space-pre-line">{{ $prescription->diagnosis_summary ?: '-' }}</div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small mb-1">تعليمات عامة</div>
                    <div class="white-space-pre-line">{{ $prescription->instructions ?: '-' }}</div>
                </div>

                <div>
                    <div class="text-muted small mb-1">ملاحظات</div>
                    <div class="white-space-pre-line">{{ $prescription->notes ?: '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .rx-info-list {
        display: grid;
        gap: 12px;
    }

    .rx-info-list > div {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid #edf0f4;
    }

    .rx-info-list > div:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .rx-info-list span {
        color: #64748b;
        font-size: 13px;
    }

    .rx-info-list strong {
        font-size: 13px;
        text-align: end;
    }

    .rx-items-list {
        display: grid;
        gap: 12px;
    }

    .rx-item-card {
        border: 1px solid #edf0f4;
        border-radius: 18px;
        padding: 14px;
        background: #fff;
    }

    .white-space-pre-line {
        white-space: pre-line;
        line-height: 1.8;
    }
</style>
@endsection