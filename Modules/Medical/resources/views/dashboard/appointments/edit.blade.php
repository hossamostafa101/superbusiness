@extends('app.layouts.app')

@section('title', 'تعديل حجز')
@section('page_title', 'تعديل حجز')
@section('page_description', $appointment->appointment_number)

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.medical.appointments.update', [$workspace, $appointment]) }}">
            @csrf
            @method('PUT')

            @include('medical::dashboard.appointments.partials.form', [
                'appointment' => $appointment,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.medical.appointments.show', [$workspace, $appointment]) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    تحديث الحجز
                </button>
            </div>
        </form>
    </div>
</div>
@endsection