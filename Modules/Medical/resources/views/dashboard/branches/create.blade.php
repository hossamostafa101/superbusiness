@extends('app.layouts.app')

@section('title', 'إضافة فرع طبي')
@section('page_title', 'إضافة فرع طبي')
@section('page_description', 'إضافة فرع جديد للمنشأة الطبية.')

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.medical.branches.store', $workspace) }}">
            @csrf

            @include('medical::dashboard.branches.partials.form', [
                'branch' => null,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.medical.branches.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    حفظ الفرع
                </button>
            </div>
        </form>
    </div>
</div>
@endsection