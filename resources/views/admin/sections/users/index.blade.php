@extends('admin.layout.admin_app')

@section('title', 'المستخدمون')

@section('content')
@php
    $admin = auth('admin')->user();

    $permissions = $admin
        ? $admin->getAllPermissions()
            ->where('guard_name', 'admin')
            ->pluck('name')
            ->toArray()
        : [];

    $canCreateUser = in_array('users.create', $permissions, true);
    $canEditUser = in_array('users.edit', $permissions, true);
    $canDeleteUser = in_array('users.delete', $permissions, true);
@endphp
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">المستخدمون</h1>
        <p class="text-body-secondary mb-0">إدارة حسابات لوحة التحكم والصلاحيات.</p>
    </div>

    @if('canCreateUser')
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            إضافة مستخدم
        </a>
    @endif
</div>

@include('admin.sections.users.partials.alerts')

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">بحث</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="ابحث بالاسم أو البريد أو الهاتف"
                >
            </div>

            <div class="col-md-3">
                <label class="form-label">الحالة</label>
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    <option value="active" @selected(request('status') === 'active')>نشط</option>
                    <option value="pending" @selected(request('status') === 'pending')>قيد الانتظار</option>
                    <option value="suspended" @selected(request('status') === 'suspended')>موقوف</option>
                </select>
            </div>

            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-dark">
                    <i class="bi bi-search"></i>
                    بحث
                </button>

                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                    إعادة ضبط
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <strong>قائمة المستخدمين</strong>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الهاتف</th>
                        <th>الأدوار</th>
                        <th>الحالة</th>
                        <th>تاريخ الإنشاء</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>

                            <td>
                                <div class="fw-semibold">{{ $user->name }}</div>
                                @if($user->adminProfile?->job_title)
                                    <small class="text-body-secondary">
                                        {{ $user->adminProfile->job_title }}
                                    </small>
                                @endif
                            </td>

                            <td>{{ $user->email }}</td>

                            <td>{{ $user->phone ?: '-' }}</td>

                            <td>
                                @forelse($user->roles->where('guard_name', 'admin') as $role)
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                                        {{ $role->name }}
                                    </span>
                                @empty
                                    <span class="text-body-secondary">بدون دور</span>
                                @endforelse
                            </td>

                            <td>
                                @if($user->status === 'active')
                                    <span class="badge bg-success">نشط</span>
                                @elseif($user->status === 'pending')
                                    <span class="badge bg-warning text-dark">قيد الانتظار</span>
                                @else
                                    <span class="badge bg-danger">موقوف</span>
                                @endif
                            </td>

                            <td>{{ $user->created_at?->format('Y-m-d') }}</td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    @can('users.edit')
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                            تعديل
                                        </a>

                                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit" class="btn btn-sm btn-outline-warning">
                                                {{ $user->status === 'active' ? 'إيقاف' : 'تفعيل' }}
                                            </button>
                                        </form>
                                    @endcan

                                    @can('users.delete')
                                        <form
                                            method="POST"
                                            action="{{ route('admin.users.destroy', $user) }}"
                                            onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                حذف
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-body-secondary py-4">
                                لا توجد بيانات.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection