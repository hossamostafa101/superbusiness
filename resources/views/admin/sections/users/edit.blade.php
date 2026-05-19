@extends('admin.layout.admin_app')

@section('title', 'تعديل مستخدم')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">تعديل مستخدم</h1>
        <p class="text-body-secondary mb-0">{{ $user->name }}</p>
    </div>

    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
        رجوع
    </a>
</div>

@include('admin.sections.users.partials.alerts')

<div class="card">
    <div class="card-header">
        <strong>بيانات المستخدم</strong>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            @include('admin.sections.users.partials.form', [
                'user' => $user,
                'roles' => $roles,
                'selectedRoles' => old('roles', $selectedRoles),
                'isEdit' => true,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.users.index') }}" class="btn btn-light">
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