{{-- resources/views/app/business-appointments/create.blade.php --}}
@extends('app.layouts.app')

@section('title', 'إضافة موعد')
@section('page_title', 'إضافة موعد')
@section('page_description', 'أضف موعدًا جديدًا وربطه بعميل وخدمة.')

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.appointments.store', $workspace) }}">
            @csrf

            @include('app.business-appointments.partials.form', [
                'workspace' => $workspace,
                'businessAppointment' => null,
                'customers' => $customers,
                'services' => $services,
                'isEdit' => false,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.appointments.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button type="submit" class="btn btn-primary">
                    حفظ
                </button>
            </div>
        </form>
    </div>
</div>
@endsection