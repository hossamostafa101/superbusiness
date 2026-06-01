@extends('app.layouts.app')

@section('title', 'تعديل عضو فريق')
@section('page_title', 'تعديل عضو فريق')
@section('page_description', $staff->name)

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.medical.staff.update', [$workspace, $staff]) }}">
            @csrf
            @method('PUT')

            @include('medical::dashboard.staff.partials.form', [
                'staff' => $staff,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.medical.staff.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    تحديث عضو الفريق
                </button>
            </div>
        </form>
    </div>
</div>
@endsection