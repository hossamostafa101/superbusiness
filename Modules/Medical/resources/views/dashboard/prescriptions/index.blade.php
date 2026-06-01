@extends('app.layouts.app')

@section('title', 'الروشتات')
@section('page_title', 'الروشتات')
@section('page_description', 'إدارة روشتات المرضى.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <strong>
        عدد الروشتات:
        {{ $prescriptions->total() }}
    </strong>
</div>

<div class="card content-card mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-7">
                <label class="form-label">بحث</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="رقم الروشتة، اسم المريض، الطبيب"
                >
            </div>

            <div class="col-md-3">
                <label class="form-label">الحالة</label>
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    <option value="draft" @selected(request('status') === 'draft')>مسودة</option>
                    <option value="issued" @selected(request('status') === 'issued')>صادرة</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>ملغية</option>
                </select>
            </div>

            <div class="col-md-2 d-grid">
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
                        <th>الروشتة</th>
                        <th>المريض</th>
                        <th>الطبيب</th>
                        <th>الزيارة</th>
                        <th>التاريخ</th>
                        <th>الحالة</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($prescriptions as $prescription)
                        <tr>
                            <td>
                                <div class="fw-bold" dir="ltr">
                                    {{ $prescription->prescription_number }}
                                </div>
                            </td>

                            <td>
                                <div class="fw-bold">
                                    {{ $prescription->patient?->full_name ?: $prescription->patient_name }}
                                </div>

                                @if($prescription->patient?->phone)
                                    <div class="small text-muted" dir="ltr">
                                        {{ $prescription->patient->phone }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                {{ $prescription->staff?->name ?: $prescription->staff_name ?: '-' }}
                            </td>

                            <td>
                                @if($prescription->visit)
                                    <a href="{{ route('app.medical.visits.show', [$workspace, $prescription->visit]) }}">
                                        {{ $prescription->visit->visit_number }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>

                            <td dir="ltr">
                                {{ $prescription->issued_at?->format('Y-m-d H:i') }}
                            </td>

                            <td>
                                <span class="badge {{ $prescription->statusBadgeClass() }}">
                                    {{ $prescription->statusLabel() }}
                                </span>
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('app.medical.prescriptions.show', [$workspace, $prescription]) }}" class="btn btn-sm btn-outline-dark">
                                        عرض
                                    </a>

                                    <a href="{{ route('app.medical.prescriptions.edit', [$workspace, $prescription]) }}" class="btn btn-sm btn-outline-primary">
                                        تعديل
                                    </a>

                                    <a href="{{ route('app.medical.prescriptions.print', [$workspace, $prescription]) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                        طباعة
                                    </a>

                                    <form method="POST" action="{{ route('app.medical.prescriptions.destroy', [$workspace, $prescription]) }}" onsubmit="return confirm('حذف الروشتة؟')">
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
                                لا توجد روشتات بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $prescriptions->links() }}
    </div>
</div>
@endsection