{{-- resources/views/app/business-appointments/index.blade.php --}}
@extends('app.layouts.app')

@section('title', 'المواعيد')
@section('page_title', 'المواعيد')
@section('page_description', 'إدارة مواعيد العملاء والحجوزات اليومية.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <strong>
            عدد المواعيد:
            {{ $appointments->total() }}
            /
            {{ $isUnlimited ? 'غير محدود' : $appointmentsLimit }}
        </strong>
    </div>

    <div class="d-flex gap-2">
    <a href="{{ route('app.appointments.calendar', $workspace) }}" class="btn btn-outline-primary">
        <i class="bi bi-calendar3"></i>
        عرض التقويم
    </a>

    <a href="{{ route('app.appointments.create', $workspace) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        إضافة موعد
    </a>
</div>

    {{-- <a href="{{ route('app.appointments.create', $workspace) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        إضافة موعد
    </a> --}}
</div>

<div class="card content-card mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('app.appointments.index', $workspace) }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">بحث</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="اسم العميل، الهاتف، البريد"
                >
            </div>

            <div class="col-md-2">
                <label class="form-label">الحالة</label>
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    <option value="pending" @selected(request('status') === 'pending')>قيد الانتظار</option>
                    <option value="confirmed" @selected(request('status') === 'confirmed')>مؤكد</option>
                    <option value="completed" @selected(request('status') === 'completed')>مكتمل</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>ملغي</option>
                    <option value="no_show" @selected(request('status') === 'no_show')>لم يحضر</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">من تاريخ</label>
                <input
                    type="date"
                    name="date_from"
                    value="{{ request('date_from') }}"
                    class="form-control"
                >
            </div>

            <div class="col-md-2">
                <label class="form-label">إلى تاريخ</label>
                <input
                    type="date"
                    name="date_to"
                    value="{{ request('date_to') }}"
                    class="form-control"
                >
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark">
                    بحث
                </button>

                <a href="{{ route('app.appointments.index', $workspace) }}" class="btn btn-outline-secondary">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card content-card">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>العميل</th>
                        <th>الخدمة</th>
                        <th>التاريخ</th>
                        <th>الوقت</th>
                        <th>المصدر</th>
                        <th>الحالة</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($appointments as $appointment)
                        <tr>
                            <td>{{ $appointment->id }}</td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $appointment->customer?->name ?: $appointment->customer_name ?: '-' }}
                                </div>

                                @if($appointment->customer?->phone || $appointment->customer_phone)
                                    <small class="text-muted">
                                        {{ $appointment->customer?->phone ?: $appointment->customer_phone }}
                                    </small>
                                @endif
                            </td>

                            <td>
                                @if($appointment->service)
                                    <div class="fw-semibold">{{ $appointment->service->name }}</div>
                                    <small class="text-muted">
                                        {{ $appointment->service->duration_minutes }} دقيقة
                                    </small>
                                @else
                                    <span class="text-muted">بدون خدمة</span>
                                @endif
                            </td>

                            <td>
                                {{ $appointment->appointment_date?->format('Y-m-d') }}
                            </td>

                            <td>
                                <div>
                                    {{ \Illuminate\Support\Str::of($appointment->start_time)->substr(0, 5) }}
                                    @if($appointment->end_time)
                                        -
                                        {{ \Illuminate\Support\Str::of($appointment->end_time)->substr(0, 5) }}
                                    @endif
                                </div>
                            </td>

                            <td>
                                <span class="badge bg-secondary">
                                    {{ $appointment->source }}
                                </span>
                            </td>

                            <td>
                                @include('app.business-appointments.partials.status-badge', [
                                    'status' => $appointment->status
                                ])
                            </td>

                            <td>
                                
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    @include('app.business-appointments.partials.whatsapp-actions', [
    'appointment' => $appointment,
])
                                    <form method="POST" action="{{ route('app.appointments.update-status', [$workspace, $appointment]) }}">
                                        @csrf
                                        @method('PATCH')

                                        <select
                                            name="status"
                                            class="form-select form-select-sm"
                                            onchange="this.form.submit()"
                                            style="min-width: 130px;"
                                        >
                                            <option value="pending" @selected($appointment->status === 'pending')>قيد الانتظار</option>
                                            <option value="confirmed" @selected($appointment->status === 'confirmed')>مؤكد</option>
                                            <option value="completed" @selected($appointment->status === 'completed')>مكتمل</option>
                                            <option value="cancelled" @selected($appointment->status === 'cancelled')>ملغي</option>
                                            <option value="no_show" @selected($appointment->status === 'no_show')>لم يحضر</option>
                                        </select>
                                    </form>

                                    <a
                                        href="{{ route('app.appointments.edit', [$workspace, $appointment]) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        تعديل
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('app.appointments.destroy', [$workspace, $appointment]) }}"
                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا الموعد؟')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                لا توجد مواعيد بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $appointments->links() }}
        </div>
    </div>
</div>
@endsection