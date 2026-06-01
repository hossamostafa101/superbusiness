@extends('app.layouts.app')

@section('title', 'الحجوزات')
@section('page_title', 'الحجوزات')
@section('page_description', 'إدارة حجوزات ومواعيد المرضى.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <strong>
        عدد الحجوزات:
        {{ $appointments->total() }}
    </strong>

    <a href="{{ route('app.medical.appointments.create', $workspace) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        إضافة حجز
    </a>
</div>

<div class="card content-card mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">بحث</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="رقم الحجز، المريض، الهاتف، الخدمة، الطبيب"
                >
            </div>

            <div class="col-md-2">
                <label class="form-label">التاريخ</label>
                <input
                    type="date"
                    name="date"
                    value="{{ request('date', today()->format('Y-m-d')) }}"
                    class="form-control"
                >
            </div>

            <div class="col-md-2">
                <label class="form-label">الحالة</label>
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    <option value="pending" @selected(request('status') === 'pending')>في الانتظار</option>
                    <option value="confirmed" @selected(request('status') === 'confirmed')>مؤكد</option>
                    <option value="checked_in" @selected(request('status') === 'checked_in')>وصل</option>
                    <option value="in_progress" @selected(request('status') === 'in_progress')>جاري الكشف</option>
                    <option value="completed" @selected(request('status') === 'completed')>مكتمل</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>ملغي</option>
                    <option value="no_show" @selected(request('status') === 'no_show')>لم يحضر</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">الطبيب / عضو الفريق</label>
                <select name="staff_id" class="form-select">
                    <option value="">الكل</option>
                    @foreach($staffMembers as $member)
                        <option value="{{ $member->id }}" @selected((string) request('staff_id') === (string) $member->id)>
                            {{ $member->name }}
                        </option>
                    @endforeach
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
                        <th>الحجز</th>
                        <th>المريض</th>
                        <th>الخدمة</th>
                        <th>الطبيب / الفريق</th>
                        <th>الموعد</th>
                        <th>الحالة</th>
                        <th>الدفع</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($appointments as $appointment)
                        <tr>
                            <td>
                                <div class="fw-bold" dir="ltr">
                                    {{ $appointment->appointment_number }}
                                </div>

                                <div class="small text-muted">
                                    {{ $appointment->branch?->name ?: '-' }}
                                </div>
                            </td>

                            <td>
                                <div class="fw-bold">
                                    {{ $appointment->patient?->full_name ?: $appointment->patient_name }}
                                </div>

                                @if($appointment->patient_phone)
                                    <div class="small text-muted" dir="ltr">
                                        {{ $appointment->patient_phone }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                <div>
                                    {{ $appointment->service?->name ?: $appointment->service_name }}
                                </div>

                                @if($appointment->price !== null)
                                    <div class="small text-muted">
                                        {{ number_format((float) $appointment->price, 2) }}
                                        {{ $appointment->currency }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                {{ $appointment->staff?->name ?: $appointment->staff_name ?: '-' }}
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $appointment->appointment_date?->format('Y-m-d') }}
                                </div>

                                <div class="small text-muted" dir="ltr">
                                    {{ \Illuminate\Support\Carbon::parse($appointment->starts_at)->format('H:i') }}
                                    @if($appointment->ends_at)
                                        -
                                        {{ \Illuminate\Support\Carbon::parse($appointment->ends_at)->format('H:i') }}
                                    @endif
                                </div>
                            </td>

                            <td>
                                <span class="badge {{ $appointment->statusBadgeClass() }}">
                                    {{ $appointment->statusLabel() }}
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $appointment->paymentStatusLabel() }}
                                </span>
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a
                                        href="{{ route('app.medical.appointments.show', [$workspace, $appointment]) }}"
                                        class="btn btn-sm btn-outline-dark"
                                    >
                                        عرض
                                    </a>

                                    <a
                                        href="{{ route('app.medical.appointments.edit', [$workspace, $appointment]) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        تعديل
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('app.medical.appointments.destroy', [$workspace, $appointment]) }}"
                                        onsubmit="return confirm('حذف الحجز؟')"
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
                            <td colspan="8" class="text-center text-muted py-4">
                                لا توجد حجوزات في هذا اليوم.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $appointments->links() }}
    </div>
</div>
@endsection