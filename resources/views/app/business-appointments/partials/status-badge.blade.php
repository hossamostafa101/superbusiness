{{-- resources/views/app/business-appointments/partials/status-badge.blade.php --}}
@switch($status)
    @case('pending')
        <span class="badge bg-warning text-dark">قيد الانتظار</span>
        @break

    @case('confirmed')
        <span class="badge bg-primary">مؤكد</span>
        @break

    @case('completed')
        <span class="badge bg-success">مكتمل</span>
        @break

    @case('cancelled')
        <span class="badge bg-danger">ملغي</span>
        @break

    @case('no_show')
        <span class="badge bg-dark">لم يحضر</span>
        @break

    @default
        <span class="badge bg-secondary">{{ $status }}</span>
@endswitch