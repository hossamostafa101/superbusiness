{{-- resources/views/app/booking-settings/edit.blade.php --}}
@extends('app.layouts.app')

@section('title', 'إعدادات الحجز')
@section('page_title', 'إعدادات الحجز')
@section('page_description', 'حدد أيام وساعات استقبال الحجوزات من الصفحة العامة.')

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.booking-settings.update', $workspace) }}">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input type="hidden" name="booking_enabled" value="0">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="booking_enabled"
                            value="1"
                            id="booking_enabled"
                            @checked(old('booking_enabled', $settings['booking_enabled']) == '1')
                        >

                        <label class="form-check-label" for="booking_enabled">
                            تفعيل الحجز من الصفحة العامة
                        </label>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">أيام العمل</label>

                    @php
                        $days = [
                            'sat' => 'السبت',
                            'sun' => 'الأحد',
                            'mon' => 'الإثنين',
                            'tue' => 'الثلاثاء',
                            'wed' => 'الأربعاء',
                            'thu' => 'الخميس',
                            'fri' => 'الجمعة',
                        ];

                        $selectedDays = old('booking_days', $settings['booking_days']);
                    @endphp

                    <div class="row g-2">
                        @foreach($days as $key => $label)
                            <div class="col-md-3 col-6">
                                <label class="border rounded-3 p-3 w-100">
                                    <input
                                        type="checkbox"
                                        name="booking_days[]"
                                        value="{{ $key }}"
                                        @checked(in_array($key, $selectedDays))
                                    >
                                    {{ $label }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                    @error('booking_days')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">بداية العمل</label>
                    <input
                        type="time"
                        name="booking_start_time"
                        value="{{ old('booking_start_time', $settings['booking_start_time']) }}"
                        class="form-control @error('booking_start_time') is-invalid @enderror"
                        required
                    >

                    @error('booking_start_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">نهاية العمل</label>
                    <input
                        type="time"
                        name="booking_end_time"
                        value="{{ old('booking_end_time', $settings['booking_end_time']) }}"
                        class="form-control @error('booking_end_time') is-invalid @enderror"
                        required
                    >

                    @error('booking_end_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">الفاصل بين الأوقات بالدقائق</label>
                    <input
                        type="number"
                        name="booking_slot_interval"
                        value="{{ old('booking_slot_interval', $settings['booking_slot_interval']) }}"
                        class="form-control @error('booking_slot_interval') is-invalid @enderror"
                        min="5"
                        max="240"
                        required
                    >

                    @error('booking_slot_interval')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">الحجز مقدمًا بعدد أيام</label>
                    <input
                        type="number"
                        name="booking_advance_days"
                        value="{{ old('booking_advance_days', $settings['booking_advance_days']) }}"
                        class="form-control @error('booking_advance_days') is-invalid @enderror"
                        min="1"
                        max="365"
                        required
                    >

                    @error('booking_advance_days')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">فاصل بين المواعيد بالدقائق</label>
                    <input
                        type="number"
                        name="booking_buffer_minutes"
                        value="{{ old('booking_buffer_minutes', $settings['booking_buffer_minutes']) }}"
                        class="form-control @error('booking_buffer_minutes') is-invalid @enderror"
                        min="0"
                        max="240"
                        required
                    >

                    @error('booking_buffer_minutes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('public.booking.create', $workspace) }}" target="_blank" class="btn btn-outline-secondary">
                    معاينة صفحة الحجز
                </a>

                <button type="submit" class="btn btn-primary">
                    حفظ الإعدادات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection