@extends('app.layouts.app')

@section('title', 'لوحة حجوزات اليوم')
@section('page_title', 'لوحة حجوزات اليوم')
@section('page_description', 'متابعة وتشغيل حجوزات اليوم حسب الحالة.')

@section('content')
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
    <div>
        <h2 class="h5 fw-bold mb-1">
            حجوزات يوم {{ \Illuminate\Support\Carbon::parse($date)->format('Y-m-d') }}
        </h2>

        <div class="text-muted">
            استخدم هذه اللوحة في الاستقبال لمتابعة المرضى بسرعة.
        </div>
    </div>

    <a href="{{ route('app.medical.appointments.create', $workspace) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        إضافة حجز
    </a>
</div>

<div class="card content-card mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">التاريخ</label>
                <input
                    type="date"
                    name="date"
                    value="{{ $date }}"
                    class="form-control"
                >
            </div>

            <div class="col-md-4">
                <label class="form-label">الفرع</label>
                <select name="branch_id" class="form-select">
                    <option value="">كل الفروع</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected((string) request('branch_id') === (string) $branch->id)>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">الطبيب / المختص</label>
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
                    عرض
                </button>
            </div>
        </form>
    </div>
</div>

<div class="medical-board-scroll">
    <div class="medical-board-grid">
        @foreach($statuses as $status => $meta)
            @php
                $statusAppointments = $appointments->get($status, collect());
            @endphp

            <div class="medical-board-column">
                <div class="medical-board-column-head">
                    <div>
                        <span class="dot dot-{{ $meta['class'] }}"></span>
                        <strong>{{ $meta['label'] }}</strong>
                    </div>

                    <span class="badge bg-light text-dark border">
                        {{ $statusAppointments->count() }}
                    </span>
                </div>

                <div class="medical-board-list">
                    @forelse($statusAppointments as $appointment)
                        <div class="appointment-card">
                            <div class="d-flex justify-content-between gap-2 mb-2">
                                <div>
                                    <div class="fw-bold">
                                        {{ $appointment->patient?->full_name ?: $appointment->patient_name }}
                                    </div>

                                    <div class="small text-muted" dir="ltr">
                                        {{ $appointment->patient_phone ?: $appointment->patient?->phone }}
                                    </div>
                                </div>

                                <div class="time-pill" dir="ltr">
                                    {{ \Illuminate\Support\Carbon::parse($appointment->starts_at)->format('H:i') }}
                                </div>
                            </div>

                            <div class="small mb-2">
                                <i class="bi bi-clipboard2-pulse"></i>
                                {{ $appointment->service?->name ?: $appointment->service_name }}
                            </div>

                            <div class="small text-muted mb-2">
                                <i class="bi bi-person-badge"></i>
                                {{ $appointment->staff?->name ?: $appointment->staff_name ?: '-' }}
                            </div>

                            @if($appointment->branch)
                                <div class="small text-muted mb-2">
                                    <i class="bi bi-building"></i>
                                    {{ $appointment->branch->name }}
                                </div>
                            @endif

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <a
                                    href="{{ route('app.medical.appointments.show', [$workspace, $appointment]) }}"
                                    class="btn btn-sm btn-outline-dark"
                                >
                                    التفاصيل
                                </a>

                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        الحالة
                                    </button>

                                    <ul class="dropdown-menu">
                                        @foreach($statuses as $nextStatus => $nextMeta)
                                            <li>
                                                <form
                                                    method="POST"
                                                    action="{{ route('app.medical.appointments.board.update-status', [$workspace, $appointment]) }}"
                                                >
                                                    @csrf
                                                    @method('PATCH')

                                                    <input type="hidden" name="status" value="{{ $nextStatus }}">

                                                    <button class="dropdown-item @if($appointment->status === $nextStatus) active @endif">
                                                        {{ $nextMeta['label'] }}
                                                    </button>
                                                </form>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-column">
                            لا توجد حجوزات
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
    .medical-board-scroll {
        overflow-x: auto;
        padding-bottom: 12px;
    }

    .medical-board-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(270px, 1fr));
        gap: 14px;
        min-width: 1900px;
    }

    .medical-board-column {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        padding: 12px;
        min-height: 420px;
    }

    .medical-board-column-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
    }

    .medical-board-column-head > div {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .dot {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        display: inline-block;
    }

    .dot-warning { background: #f59e0b; }
    .dot-primary { background: #2563eb; }
    .dot-info { background: #06b6d4; }
    .dot-dark { background: #111827; }
    .dot-success { background: #16a34a; }
    .dot-danger { background: #dc2626; }
    .dot-secondary { background: #6b7280; }

    .medical-board-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .appointment-card {
        background: #fff;
        border: 1px solid #edf0f4;
        border-radius: 18px;
        padding: 12px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .04);
    }

    .time-pill {
        height: 30px;
        min-width: 54px;
        padding: 0 10px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        font-size: 12px;
        font-weight: 800;
    }

    .empty-column {
        border: 1px dashed #cbd5e1;
        border-radius: 18px;
        padding: 24px 12px;
        text-align: center;
        color: #64748b;
        background: rgba(255,255,255,.6);
    }

    @media (max-width: 768px) {
        .medical-board-grid {
            grid-template-columns: repeat(7, 280px);
            min-width: 1960px;
        }
    }
</style>
@endsection