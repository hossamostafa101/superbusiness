{{-- resources/views/app/business-appointments/calendar.blade.php --}}
@extends('app.layouts.app')

@section('title', 'تقويم المواعيد')
@section('page_title', 'تقويم المواعيد')
@section('page_description', 'عرض المواعيد بشكل شهري أو أسبوعي أو يومي.')

@push('head')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
<style>
    #calendar {
        background: #fff;
        border-radius: 18px;
        padding: 16px;
    }

    .fc {
        direction: rtl;
    }

    .fc-toolbar-title {
        font-size: 1.2rem !important;
        font-weight: 800;
    }

    .fc-button {
        border-radius: 10px !important;
    }

    .fc-event {
        border-radius: 8px;
        padding: 2px 4px;
        font-size: 12px;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex gap-2">
        <a href="{{ route('app.appointments.index', $workspace) }}" class="btn btn-outline-secondary">
            <i class="bi bi-table"></i>
            عرض الجدول
        </a>

        <a href="{{ route('app.appointments.create', $workspace) }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            إضافة موعد
        </a>
    </div>

    <div class="d-flex flex-wrap gap-2 small">
        <span class="badge" style="background:#f59e0b;">قيد الانتظار</span>
        <span class="badge" style="background:#2563eb;">مؤكد</span>
        <span class="badge" style="background:#16a34a;">مكتمل</span>
        <span class="badge" style="background:#dc2626;">ملغي</span>
        <span class="badge" style="background:#111827;">لم يحضر</span>
    </div>
</div>

<div class="card content-card">
    <div class="card-body p-3 p-md-4">
        <div id="calendar"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/locales/ar.global.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');

        const calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'ar',
            direction: 'rtl',
            initialView: 'dayGridMonth',
            height: 'auto',
            firstDay: 6,

            headerToolbar: {
                start: 'prev,next today',
                center: 'title',
                end: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },

            buttonText: {
                today: 'اليوم',
                month: 'شهر',
                week: 'أسبوع',
                day: 'يوم',
                list: 'قائمة'
            },

            events: {
                url: "{{ route('app.appointments.calendar-events', $workspace) }}",
                method: 'GET',
                failure: function () {
                    alert('حدث خطأ أثناء تحميل المواعيد.');
                }
            },

            eventClick: function (info) {
                info.jsEvent.preventDefault();

                if (info.event.url) {
                    window.location.href = info.event.url;
                }
            },

            eventDidMount: function (info) {
                const phone = info.event.extendedProps.phone || '';
                const source = info.event.extendedProps.source || '';
                const status = info.event.extendedProps.status || '';

                info.el.title =
                    info.event.title +
                    (phone ? '\nالهاتف: ' + phone : '') +
                    (source ? '\nالمصدر: ' + source : '') +
                    (status ? '\nالحالة: ' + status : '');
            },

            nowIndicator: true,
            navLinks: true,
            editable: false,
            selectable: false,
        });

        calendar.render();
    });
</script>
@endpush