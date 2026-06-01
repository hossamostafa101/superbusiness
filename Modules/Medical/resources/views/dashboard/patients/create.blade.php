@extends('app.layouts.app')

@section('title', 'إضافة مريض')
@section('page_title', 'إضافة مريض')
@section('page_description', 'إضافة ملف مريض جديد.')

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.medical.patients.store', $workspace) }}">
            @csrf

            @include('medical::dashboard.patients.partials.form', [
                'patient' => null,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.medical.patients.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    حفظ المريض
                </button>
            </div>
        </form>
    </div>
</div>
@endsection