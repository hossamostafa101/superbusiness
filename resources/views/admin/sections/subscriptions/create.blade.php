@extends('admin.layout.admin_app')

@section('title', 'إضافة اشتراك')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">إضافة اشتراك</h1>
        <p class="text-body-secondary mb-0">إنشاء اشتراك يدوي لمساحة عمل وربطه بباقة.</p>
    </div>

    <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-secondary">
        رجوع
    </a>
</div>

@include('admin.sections.subscriptions.partials.alerts')

<div class="card">
    <div class="card-header">
        <strong>بيانات الاشتراك</strong>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.subscriptions.store') }}">
            @csrf

            @include('admin.sections.subscriptions.partials.form', [
                'subscription' => null,
                'workspaces' => $workspaces,
                'plans' => $plans,
                'isEdit' => false,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-light">
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