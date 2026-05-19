@extends('admin.layout.admin_app')

@section('title', 'إضافة خاصية')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">إضافة خاصية</h1>
        <p class="text-body-secondary mb-0">إنشاء خاصية جديدة لاستخدامها داخل الباقات.</p>
    </div>

    <a href="{{ route('admin.features.index') }}" class="btn btn-outline-secondary">
        رجوع
    </a>
</div>

@include('admin.sections.features.partials.alerts')

<div class="card">
    <div class="card-header">
        <strong>بيانات الخاصية</strong>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.features.store') }}">
            @csrf

            @include('admin.sections.features.partials.form', [
                'feature' => null,
                'isEdit' => false,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.features.index') }}" class="btn btn-light">
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