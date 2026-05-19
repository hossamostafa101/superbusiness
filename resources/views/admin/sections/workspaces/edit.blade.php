@extends('admin.layout.admin_app')

@section('title', 'تعديل مساحة عمل')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">تعديل مساحة عمل</h1>
        <p class="text-body-secondary mb-0">{{ $workspace->name }}</p>
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
        <form method="POST" action="{{ route('admin.workspaces.update', $workspace) }}">
            @csrf
            @method('PUT')

            @include('admin.sections.workspaces.partials.form', [
                'workspace' => $workspace,
                'owners' => $owners,
                'isEdit' => true,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.workspaces.index') }}" class="btn btn-light">
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