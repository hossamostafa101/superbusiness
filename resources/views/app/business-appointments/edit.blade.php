{{-- resources/views/app/business-appointments/edit.blade.php --}}
@extends('app.layouts.app')

@section('title', 'تعديل موعد')
@section('page_title', 'تعديل موعد')
@section('page_description', ($businessAppointment->customer?->name ?: $businessAppointment->customer_name ?: 'موعد'))

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.appointments.update', [$workspace, $businessAppointment]) }}">
            @csrf
            @method('PUT')

            @include('app.business-appointments.partials.form', [
                'workspace' => $workspace,
                'businessAppointment' => $businessAppointment,
                'customers' => $customers,
                'services' => $services,
                'isEdit' => true,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.appointments.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button type="submit" class="btn btn-primary">
                    تحديث
                </button>
            </div>
        </form>
    </div>
</div>
@endsection