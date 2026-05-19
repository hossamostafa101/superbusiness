@extends('admin.layout.admin_app')

@section('title', 'مساحات العمل')

@section('content')

@php
    $admin = auth('admin')->user();

    $permissions = $admin
        ? $admin->getAllPermissions()
            ->where('guard_name', 'admin')
            ->pluck('name')
            ->toArray()
        : [];

    $canCreateWorkspace = in_array('workspaces.create', $permissions, true);
    $canEditWorkspace = in_array('workspaces.edit', $permissions, true);
    $canDeleteWorkspace = in_array('workspaces.delete', $permissions, true);
@endphp


<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">مساحات العمل</h1>
        <p class="text-body-secondary mb-0">إدارة البزنسات أو المشاريع المرتبطة بالمستخدمين والباقات.</p>
    </div>

    @if('canCreateWorkspace')
        <a href="{{ route('admin.workspaces.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            إضافة مساحة عمل
        </a>
    @endif
</div>

@include('admin.sections.workspaces.partials.alerts')

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.workspaces.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">بحث</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="اسم المساحة، الرابط، المالك"
                >
            </div>

            <div class="col-md-3">
                <label class="form-label">النوع</label>
                <select name="type" class="form-select">
                    <option value="">كل الأنواع</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" @selected(request('type') === $type)>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">الحالة</label>
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    <option value="active" @selected(request('status') === 'active')>نشطة</option>
                    <option value="pending" @selected(request('status') === 'pending')>قيد الانتظار</option>
                    <option value="suspended" @selected(request('status') === 'suspended')>موقوفة</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>ملغاة</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark">
                    بحث
                </button>

                <a href="{{ route('admin.workspaces.index') }}" class="btn btn-outline-secondary">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <strong>قائمة مساحات العمل</strong>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>مساحة العمل</th>
                        <th>الرابط</th>
                        <th>المالك</th>
                        <th>النوع</th>
                        <th>الاشتراك</th>
                        <th>الأعضاء</th>
                        <th>الحالة</th>
                        <th>Trial</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($workspaces as $workspace)
                        <tr>
                            <td>{{ $workspace->id }}</td>

                            <td>
                                <div class="fw-semibold">{{ $workspace->name }}</div>
                                <small class="text-body-secondary">
                                    تم الإنشاء: {{ $workspace->created_at?->format('Y-m-d') }}
                                </small>
                            </td>

                            <td>
                                <code>{{ $workspace->slug }}</code>
                            </td>

                            <td>
                                @if($workspace->owner)
                                    <div class="fw-semibold">{{ $workspace->owner->name }}</div>
                                    <small class="text-body-secondary">{{ $workspace->owner->email }}</small>
                                @else
                                    <span class="text-danger">بدون مالك</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-secondary">
                                    {{ $workspace->type }}
                                </span>
                            </td>

                            <td>
                                @if($workspace->activeSubscription?->plan)
                                    <div class="fw-semibold">
                                        {{ $workspace->activeSubscription->plan->name }}
                                    </div>
                                    <small class="text-body-secondary">
                                        {{ $workspace->activeSubscription->status }}
                                        /
                                        {{ $workspace->activeSubscription->billing_cycle }}
                                    </small>
                                @else
                                    <span class="text-body-secondary">بدون اشتراك</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-info">
                                    {{ $workspace->users_count }}
                                </span>
                            </td>

                            <td>
                                @if($workspace->status === 'active')
                                    <span class="badge bg-success">نشطة</span>
                                @elseif($workspace->status === 'pending')
                                    <span class="badge bg-warning text-dark">قيد الانتظار</span>
                                @elseif($workspace->status === 'suspended')
                                    <span class="badge bg-danger">موقوفة</span>
                                @else
                                    <span class="badge bg-dark">ملغاة</span>
                                @endif
                            </td>

                            <td>
                                @if($workspace->trial_ends_at)
                                    {{ $workspace->trial_ends_at->format('Y-m-d') }}
                                @else
                                    <span class="text-body-secondary">-</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    @if('canEditWorkspace')
                                        <a href="{{ route('admin.workspaces.edit', $workspace) }}" class="btn btn-sm btn-outline-primary">
                                            تعديل
                                        </a>

                                        <form method="POST" action="{{ route('admin.workspaces.toggle-status', $workspace) }}">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit" class="btn btn-sm btn-outline-warning">
                                                {{ $workspace->status === 'active' ? 'إيقاف' : 'تفعيل' }}
                                            </button>
                                        </form>
                                    @endif

                                    @if('canDeleteWorkspace')
                                        <form
                                            method="POST"
                                            action="{{ route('admin.workspaces.destroy', $workspace) }}"
                                            onsubmit="return confirm('هل أنت متأكد من حذف مساحة العمل؟ سيتم حذف الاشتراكات والمدفوعات المرتبطة بها.')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                حذف
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-body-secondary py-4">
                                لا توجد مساحات عمل.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $workspaces->links() }}
        </div>
    </div>
</div>
@endsection