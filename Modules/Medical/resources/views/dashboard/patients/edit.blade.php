@extends('app.layouts.app')

@section('title', 'تعديل مريض')
@section('page_title', 'تعديل مريض')
@section('page_description', $patient->full_name)

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.medical.patients.update', [$workspace, $patient]) }}">
            @csrf
            @method('PUT')

            @include('medical::dashboard.patients.partials.form', [
                'patient' => $patient,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.medical.patients.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    تحديث المريض
                </button>
            </div>
        </form>
    </div>
</div>
@endsection