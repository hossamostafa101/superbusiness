@php
    $compact = $compact ?? false;
@endphp

<div class="patient-visit-row">
    <div class="patient-visit-date">
        <strong>
            {{ $visit->visit_date?->format('Y-m-d') }}
        </strong>

        <span>
            {{ $visit->visit_number }}
        </span>
    </div>

    <div class="flex-grow-1">
        <div class="fw-bold">
            {{ $visit->service?->name ?: $visit->service_name ?: $visit->visitTypeLabel() }}
        </div>

        <div class="small text-muted">
            @if($visit->staff)
                {{ $visit->staff->name }}
            @elseif($visit->staff_name)
                {{ $visit->staff_name }}
            @else
                بدون مختص
            @endif

            @if($visit->branch)
                · {{ $visit->branch->name }}
            @endif
        </div>

        @if(! $compact && $visit->diagnosis)
            <div class="small mt-1">
                التشخيص:
                {{ \Illuminate\Support\Str::limit($visit->diagnosis, 100) }}
            </div>
        @endif

        @if(! $compact && $visit->chief_complaint)
            <div class="small text-muted mt-1">
                الشكوى:
                {{ \Illuminate\Support\Str::limit($visit->chief_complaint, 100) }}
            </div>
        @endif
    </div>

    <span class="badge {{ $visit->statusBadgeClass() }}">
        {{ $visit->statusLabel() }}
    </span>

    <a
        href="{{ route('app.medical.visits.show', [$workspace, $visit]) }}"
        class="btn btn-sm btn-light border"
    >
        عرض
    </a>
</div>