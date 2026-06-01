@extends('app.layouts.app')

@section('title', 'إضافة حجز')
@section('page_title', 'إضافة حجز')
@section('page_description', 'إنشاء حجز جديد لمريض.')

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.medical.appointments.store', $workspace) }}">
            @csrf

            @include('medical::dashboard.appointments.partials.form', [
                'appointment' => null,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.medical.appointments.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    حفظ الحجز
                </button>
            </div>
        </form>
    </div>
</div>
@endsection