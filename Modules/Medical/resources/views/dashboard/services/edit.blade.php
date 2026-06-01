@extends('app.layouts.app')

@section('title', 'تعديل خدمة')
@section('page_title', 'تعديل خدمة')
@section('page_description', $service->name)

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.medical.services.update', [$workspace, $service]) }}">
            @csrf
            @method('PUT')

            @include('medical::dashboard.services.partials.form', [
                'service' => $service,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.medical.services.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    تحديث الخدمة
                </button>
            </div>
        </form>
    </div>
</div>
@endsection