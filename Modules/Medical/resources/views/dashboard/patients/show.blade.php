@extends('app.layouts.app')

@section('title', 'ملف المريض')
@section('page_title', 'ملف المريض')
@section('page_description', $patient->full_name)

@push('head')
    <style>
        .patient-visit-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #edf0f4;
        }

        .patient-visit-row:last-child {
            border-bottom: 0;
        }

        .patient-visit-date {
            min-width: 96px;
            border-radius: 16px;
            background: #f0fdf4;
            padding: 8px;
            text-align: center;
        }

        .patient-visit-date strong {
            display: block;
            font-size: 13px;
            color: #166534;
        }

        .patient-visit-date span {
            display: block;
            font-size: 12px;
            color: #64748b;
        }



        .patient-visit-row {
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .patient-visit-date {
            min-width: 76px;
        }
    </style>
@endpush
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('app.medical.patients.index', $workspace) }}" class="btn btn-light">
            رجوع
        </a>

        <a href="{{ route('app.medical.visits.create', $workspace) }}?patient_id={{ $patient->id }}"
            class="btn btn-outline-success">
            <i class="bi bi-journal-medical"></i>
            زيارة جديدة
        </a>

        <a href="{{ route('app.medical.patients.edit', [$workspace, $patient]) }}" class="btn btn-primary">
            تعديل البيانات
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card content-card h-100">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">
                        البيانات الأساسية
                    </h2>

                    <div class="mb-3">
                        <div class="text-muted small">كود المريض</div>
                        <div class="fw-bold" dir="ltr">{{ $patient->patient_code ?: '-' }}</div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small">الاسم</div>
                        <div class="fw-bold">{{ $patient->full_name }}</div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small">الهاتف</div>
                        <div dir="ltr">{{ $patient->phone ?: '-' }}</div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small">واتساب</div>
                        <div dir="ltr">{{ $patient->whatsapp_number ?: '-' }}</div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small">البريد</div>
                        <div dir="ltr">{{ $patient->email ?: '-' }}</div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small">النوع</div>
                        <div>{{ $patient->genderLabel() }}</div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small">تاريخ الميلاد</div>
                        <div>{{ $patient->birth_date?->format('Y-m-d') ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-lg-8">
            <div class="card content-card mb-4">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">
                        بيانات طبية
                    </h2>

                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <div class="patient-stat-card">
                                <span>كل الحجوزات</span>
                                <strong>{{ $stats['appointments_total'] }}</strong>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="patient-stat-card">
                                <span>كل الزيارات</span>
                                <strong>{{ $stats['visits_total'] }}</strong>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="patient-stat-card">
                                <span>حجوزات قادمة</span>
                                <strong>{{ $stats['upcoming'] }}</strong>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="patient-stat-card">
                                <span>زيارات مكتملة</span>
                                <strong>{{ $stats['completed_visits'] }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="text-muted small">فصيلة الدم</div>
                            <div class="fw-bold">{{ $patient->blood_type ?: '-' }}</div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">الرقم القومي</div>
                            <div dir="ltr">{{ $patient->national_id ?: '-' }}</div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">التأمين</div>
                            <div>{{ $patient->insurance_provider ?: '-' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">الحساسية</div>
                            <div>{{ $patient->allergies ?: '-' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">الأمراض المزمنة</div>
                            <div>{{ $patient->chronic_diseases ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card content-card">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">
                        ملاحظات وعنوان
                    </h2>

                    <div class="mb-3">
                        <div class="text-muted small">العنوان</div>
                        <div>{{ $patient->address ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="text-muted small">ملاحظات</div>
                        <div>{{ $patient->notes ?: '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="card content-card mb-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h5 fw-bold mb-1">
                    الزيارات الطبية
                </h2>

                <p class="text-muted mb-0">
                    آخر الزيارات الفعلية للمريض.
                </p>
            </div>

            <a href="{{ route('app.medical.visits.create', $workspace) }}?patient_id={{ $patient->id }}" class="btn btn-sm btn-outline-success">
                زيارة جديدة
            </a>
        </div>

        @forelse($latestVisits as $visit)
            @include('medical::dashboard.patients.partials.visit-row', [
                'visit' => $visit,
            ])
        @empty
            <div class="text-center text-muted py-4">
                لا توجد زيارات بعد.
            </div>
        @endforelse
    </div>
</div>
        </div>
    </div>
@endsection
