@extends('app.layouts.app')

@section('title', 'إضافة قسم')
@section('page_title', 'إضافة قسم')
@section('page_description', 'إضافة قسم طبي جديد.')

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.medical.departments.store', $workspace) }}">
            @csrf

            @include('medical::dashboard.departments.partials.form', [
                'department' => null,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.medical.departments.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    حفظ القسم
                </button>
            </div>
        </form>
    </div>
</div>
@endsection