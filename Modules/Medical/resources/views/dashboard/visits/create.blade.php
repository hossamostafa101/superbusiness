@extends('app.layouts.app')

@section('title', 'إضافة زيارة')
@section('page_title', 'إضافة زيارة')
@section('page_description', 'إنشاء زيارة فعلية للمريض.')

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.medical.visits.store', $workspace) }}">
            @csrf

            @include('medical::dashboard.visits.partials.form', [
                'visit' => null,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.medical.visits.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    حفظ الزيارة
                </button>
            </div>
        </form>
    </div>
</div>
@endsection