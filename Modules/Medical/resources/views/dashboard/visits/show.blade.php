@extends('app.layouts.app')

@section('title', 'تفاصيل الزيارة')
@section('page_title', 'تفاصيل الزيارة')
@section('page_description', $visit->visit_number)

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <a href="{{ route('app.medical.visits.index', $workspace) }}" class="btn btn-light">
        رجوع
    </a>

    <div class="d-flex flex-wrap gap-2">
        @if($visit->patient)
            <a href="{{ route('app.medical.patients.show', [$workspace, $visit->patient]) }}" class="btn btn-outline-dark">
                ملف المريض
            </a>
        @endif



        <a href="{{ route('app.medical.prescriptions.create', $workspace) }}?visit_id={{ $visit->id }}" class="btn btn-outline-success">
    <i class="bi bi-capsule"></i>
    روشتة جديدة
</a>




        <a href="{{ route('app.medical.visits.edit', [$workspace, $visit]) }}" class="btn btn-primary">
            تعديل الزيارة
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card content-card mb-4">
            <div class="card-body p-4">
                <h2 class="h5 fw-bold mb-3">
                    بيانات الزيارة
                </h2>

                <div class="visit-info-list">
                    <div>
                        <span>رقم الزيارة</span>
                        <strong dir="ltr">{{ $visit->visit_number }}</strong>
                    </div>

                    <div>
                        <span>الحالة</span>
                        <strong>
                            <span class="badge {{ $visit->statusBadgeClass() }}">
                                {{ $visit->statusLabel() }}
                            </span>
                        </strong>
                    </div>

                    <div>
                        <span>نوع الزيارة</span>
                        <strong>{{ $visit->visitTypeLabel() }}</strong>
                    </div>

                    <div>
                        <span>التاريخ</span>
                        <strong>{{ $visit->visit_date?->format('Y-m-d') }}</strong>
                    </div>

                    <div>
                        <span>البداية</span>
                        <strong dir="ltr">{{ $visit->started_at?->format('Y-m-d H:i') ?: '-' }}</strong>
                    </div>

                    <div>
                        <span>النهاية</span>
                        <strong dir="ltr">{{ $visit->ended_at?->format('Y-m-d H:i') ?: '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="card content-card">
            <div class="card-body p-4">
                <h2 class="h5 fw-bold mb-3">
                    المريض والخدمة
                </h2>

                <div class="visit-info-list">
                    <div>
                        <span>المريض</span>
                        <strong>{{ $visit->patient?->full_name ?: $visit->patient_name }}</strong>
                    </div>

                    <div>
                        <span>الطبيب / المختص</span>
                        <strong>{{ $visit->staff?->name ?: $visit->staff_name ?: '-' }}</strong>
                    </div>

                    <div>
                        <span>الخدمة</span>
                        <strong>{{ $visit->service?->name ?: $visit->service_name ?: '-' }}</strong>
                    </div>

                    <div>
                        <span>الفرع</span>
                        <strong>{{ $visit->branch?->name ?: '-' }}</strong>
                    </div>

                    @if($visit->appointment)
                        <div>
                            <span>الحجز المرتبط</span>
                            <strong>
                                <a href="{{ route('app.medical.appointments.show', [$workspace, $visit->appointment]) }}">
                                    {{ $visit->appointment->appointment_number }}
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
                <h2 class="h5 fw-bold mb-4">
                    تفاصيل الكشف
                </h2>

                <div class="visit-clinical-section">
                    <h3>الشكوى الرئيسية</h3>
                    <p>{{ $visit->chief_complaint ?: '-' }}</p>
                </div>

                <div class="visit-clinical-section">
                    <h3>التشخيص</h3>
                    <p>{{ $visit->diagnosis ?: '-' }}</p>
                </div>

                <div class="visit-clinical-section">
                    <h3>خطة العلاج</h3>
                    <p>{{ $visit->treatment_plan ?: '-' }}</p>
                </div>

                <div class="visit-clinical-section">
                    <h3>ملاحظات عامة</h3>
                    <p>{{ $visit->notes ?: '-' }}</p>
                </div>

                <div class="visit-clinical-section mb-0">
                    <h3>ملاحظات داخلية</h3>
                    <p>{{ $visit->internal_notes ?: '-' }}</p>
                </div>
            </div>
        </div>


        <div class="card content-card mb-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h5 fw-bold mb-1">
                    الروشتات
                </h2>

                <p class="text-muted mb-0">
                    الروشتات المرتبطة بهذه الزيارة.
                </p>
            </div>

            <a href="{{ route('app.medical.prescriptions.create', $workspace) }}?visit_id={{ $visit->id }}" class="btn btn-sm btn-outline-success">
                روشتة جديدة
            </a>
        </div>

        @forelse($visit->prescriptions as $prescription)
            <div class="d-flex justify-content-between align-items-center border rounded-4 p-3 mb-2">
                <div>
                    <div class="fw-bold" dir="ltr">
                        {{ $prescription->prescription_number }}
                    </div>

                    <div class="small text-muted">
                        {{ $prescription->issued_at?->format('Y-m-d H:i') }}
                        ·
                        {{ $prescription->items->count() }} دواء
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('app.medical.prescriptions.show', [$workspace, $prescription]) }}" class="btn btn-sm btn-outline-dark">
                        عرض
                    </a>

                    <a href="{{ route('app.medical.prescriptions.print', [$workspace, $prescription]) }}" target="_blank" class="btn btn-sm btn-outline-success">
                        طباعة
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-4">
                لا توجد روشتات لهذه الزيارة.
            </div>
        @endforelse
    </div>
</div>

        <div class="card content-card">
            <div class="card-body p-4">
                <h2 class="h5 fw-bold mb-3">
                    ملاحظات الزيارة
                </h2>

                <form method="POST" action="{{ route('app.medical.visits.notes.store', [$workspace, $visit]) }}" class="mb-4">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">نوع الملاحظة</label>

                            <select name="type" class="form-select">
                                <option value="general">عام</option>
                                <option value="complaint">شكوى</option>
                                <option value="diagnosis">تشخيص</option>
                                <option value="treatment">خطة علاج</option>
                                <option value="follow_up">متابعة</option>
                                <option value="internal">داخلي</option>
                            </select>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">الملاحظة</label>

                            <textarea name="note" rows="3" class="form-control" required></textarea>
                        </div>

                        <div class="col-12 d-flex justify-content-end">
                            <button class="btn btn-dark">
                                إضافة ملاحظة
                            </button>
                        </div>
                    </div>
                </form>

                <div class="visit-notes-list">
                    @forelse($visit->visitNotes as $note)
                        <div class="visit-note-card">
                            <div class="d-flex justify-content-between gap-3 mb-2">
                                <div>
                                    <span class="badge bg-light text-dark border">
                                        {{ $note->typeLabel() }}
                                    </span>

                                    @if($note->staff)
                                        <span class="small text-muted ms-2">
                                            {{ $note->staff->name }}
                                        </span>
                                    @endif
                                </div>

                                <div class="small text-muted" dir="ltr">
                                    {{ $note->created_at?->format('Y-m-d H:i') }}
                                </div>
                            </div>

                            <div>
                                {{ $note->note }}
                            </div>

                            <form
                                method="POST"
                                action="{{ route('app.medical.visits.notes.destroy', [$workspace, $visit, $note]) }}"
                                class="mt-2"
                                onsubmit="return confirm('حذف الملاحظة؟')"
                            >
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-outline-danger">
                                    حذف
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            لا توجد ملاحظات بعد.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .visit-info-list {
        display: grid;
        gap: 12px;
    }

    .visit-info-list > div {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid #edf0f4;
    }

    .visit-info-list > div:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .visit-info-list span {
        color: #64748b;
        font-size: 13px;
    }

    .visit-info-list strong {
        font-size: 13px;
        text-align: end;
    }

    .visit-clinical-section {
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #edf0f4;
    }

    .visit-clinical-section:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .visit-clinical-section h3 {
        font-size: 14px;
        font-weight: 900;
        margin-bottom: 8px;
        color: #0f172a;
    }

    .visit-clinical-section p {
        margin: 0;
        color: #334155;
        white-space: pre-line;
        line-height: 1.8;
    }

    .visit-notes-list {
        display: grid;
        gap: 12px;
    }

    .visit-note-card {
        border: 1px solid #edf0f4;
        border-radius: 18px;
        padding: 14px;
        background: #fff;
    }
</style>
@endsection