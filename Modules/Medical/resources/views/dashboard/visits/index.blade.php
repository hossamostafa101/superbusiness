@extends('app.layouts.app')

@section('title', 'الزيارات')
@section('page_title', 'الزيارات')
@section('page_description', 'إدارة الزيارات الفعلية للمرضى.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <strong>
        عدد الزيارات:
        {{ $visits->total() }}
    </strong>

    <a href="{{ route('app.medical.visits.create', $workspace) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        إضافة زيارة
    </a>
</div>

<div class="card content-card mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">بحث</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="رقم الزيارة، المريض، الطبيب، الخدمة"
                >
            </div>

            <div class="col-md-3">
                <label class="form-label">التاريخ</label>
                <input
                    type="date"
                    name="date"
                    value="{{ request('date', today()->format('Y-m-d')) }}"
                    class="form-control"
                >
            </div>

            <div class="col-md-3">
                <label class="form-label">الحالة</label>
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    <option value="open" @selected(request('status') === 'open')>مفتوحة</option>
                    <option value="in_progress" @selected(request('status') === 'in_progress')>جاري الكشف</option>
                    <option value="completed" @selected(request('status') === 'completed')>مكتملة</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>ملغية</option>
                </select>
            </div>

            <div class="col-md-1 d-grid">
                <button class="btn btn-dark">
                    بحث
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card content-card">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>الزيارة</th>
                        <th>المريض</th>
                        <th>الخدمة</th>
                        <th>الطبيب / المختص</th>
                        <th>النوع</th>
                        <th>الحالة</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($visits as $visit)
                        <tr>
                            <td>
                                <div class="fw-bold" dir="ltr">
                                    {{ $visit->visit_number }}
                                </div>

                                <div class="small text-muted">
                                    {{ $visit->visit_date?->format('Y-m-d') }}
                                </div>

                                @if($visit->started_at)
                                    <div class="small text-muted" dir="ltr">
                                        {{ $visit->started_at->format('H:i') }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                <div class="fw-bold">
                                    {{ $visit->patient?->full_name ?: $visit->patient_name }}
                                </div>

                                @if($visit->patient?->phone)
                                    <div class="small text-muted" dir="ltr">
                                        {{ $visit->patient->phone }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                {{ $visit->service?->name ?: $visit->service_name ?: '-' }}
                            </td>

                            <td>
                                {{ $visit->staff?->name ?: $visit->staff_name ?: '-' }}
                            </td>

                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $visit->visitTypeLabel() }}
                                </span>
                            </td>

                            <td>
                                <span class="badge {{ $visit->statusBadgeClass() }}">
                                    {{ $visit->statusLabel() }}
                                </span>
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a
                                        href="{{ route('app.medical.visits.show', [$workspace, $visit]) }}"
                                        class="btn btn-sm btn-outline-dark"
                                    >
                                        عرض
                                    </a>

                                    <a
                                        href="{{ route('app.medical.visits.edit', [$workspace, $visit]) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        تعديل
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('app.medical.visits.destroy', [$workspace, $visit]) }}"
                                        onsubmit="return confirm('حذف الزيارة؟')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-sm btn-outline-danger">
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                لا توجد زيارات.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $visits->links() }}
    </div>
</div>
@endsection