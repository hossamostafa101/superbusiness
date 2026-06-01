@extends('app.layouts.app')

@section('title', 'إضافة عضو فريق')
@section('page_title', 'إضافة عضو فريق')
@section('page_description', 'إضافة طبيب أو فني أو موظف جديد.')

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.medical.staff.store', $workspace) }}">
            @csrf

            @include('medical::dashboard.staff.partials.form', [
                'staff' => null,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.medical.staff.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    حفظ عضو الفريق
                </button>
            </div>
        </form>
    </div>
</div>
@endsection