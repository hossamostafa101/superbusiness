@extends('app.layouts.app')

@section('title', 'إضافة خدمة')
@section('page_title', 'إضافة خدمة')
@section('page_description', 'إضافة خدمة طبية جديدة.')

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.medical.services.store', $workspace) }}">
            @csrf

            @include('medical::dashboard.services.partials.form', [
                'service' => null,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.medical.services.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    حفظ الخدمة
                </button>
            </div>
        </form>
    </div>
</div>
@endsection