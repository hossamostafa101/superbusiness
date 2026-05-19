@extends('admin.layout.admin_app')

@section('title', 'تعديل باقة')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">تعديل باقة</h1>
        <p class="text-body-secondary mb-0">{{ $plan->name }}</p>
    </div>

    <a href="{{ route('admin.plans.index') }}" class="btn btn-outline-secondary">
        رجوع
    </a>
</div>

@include('admin.sections.plans.partials.alerts')

<div class="card">
    <div class="card-header">
        <strong>بيانات الباقة</strong>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.plans.update', $plan) }}">
            @csrf
            @method('PUT')

            @include('admin.sections.plans.partials.form', [
                'plan' => $plan,
                'features' => $features,
                'featureValues' => old('features', $featureValues),
                'isEdit' => true,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.plans.index') }}" class="btn btn-light">
                    إلغاء
                </a>

                <button type="submit" class="btn btn-primary">
                    تحديث
                </button>
            </div>
        </form>
    </div>
</div>
@endsection