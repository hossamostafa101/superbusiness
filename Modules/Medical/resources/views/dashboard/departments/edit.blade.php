@extends('app.layouts.app')

@section('title', 'تعديل قسم')
@section('page_title', 'تعديل قسم')
@section('page_description', $department->name)

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.medical.departments.update', [$workspace, $department]) }}">
            @csrf
            @method('PUT')

            @include('medical::dashboard.departments.partials.form', [
                'department' => $department,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.medical.departments.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    تحديث القسم
                </button>
            </div>
        </form>
    </div>
</div>
@endsection