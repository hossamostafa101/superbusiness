@extends('admin.layout.admin_app')

@section('title', 'إضافة مساحة عمل')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">إضافة مساحة عمل</h1>
        <p class="text-body-secondary mb-0">إنشاء مساحة عمل جديدة وربطها بمالك وخطة مجانية افتراضية.</p>
    </div>

    <a href="{{ route('admin.workspaces.index') }}" class="btn btn-outline-secondary">
        رجوع
    </a>
</div>

@include('admin.sections.workspaces.partials.alerts')

<div class="card">
    <div class="card-header">
        <strong>بيانات مساحة العمل</strong>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.workspaces.store') }}">
            @csrf

            @include('admin.sections.workspaces.partials.form', [
                'workspace' => null,
                'owners' => $owners,
                'isEdit' => false,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.workspaces.index') }}" class="btn btn-light">
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