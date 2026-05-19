@extends('admin.layout.admin_app')

@section('title', 'إضافة باقة')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">إضافة باقة</h1>
        <p class="text-body-secondary mb-0">إنشاء باقة اشتراك جديدة وربطها بالخصائص.</p>
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
        <form method="POST" action="{{ route('admin.plans.store') }}">
            @csrf

            @include('admin.sections.plans.partials.form', [
                'plan' => null,
                'features' => $features,
                'featureValues' => old('features', []),
                'isEdit' => false,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.plans.index') }}" class="btn btn-light">
                    إلغاء
                </a>

                <button type="submit" class="btn btn-primary">
                    حفظ
                </button>
            </div>
        </form>
    </div>
</div>
@endsection