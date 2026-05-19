@extends('admin.layout.admin_app')

@section('title', 'الباقات')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">الباقات</h1>
        <p class="text-body-secondary mb-0">إدارة خطط الاشتراك وحدود الخصائص لكل باقة.</p>
    </div>

    @can('plans.create')
        <a href="{{ route('admin.plans.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            إضافة باقة
        </a>
    @endcan
</div>

@include('admin.sections.plans.partials.alerts')

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.plans.index') }}" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label">بحث</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="ابحث باسم الباقة أو slug"
                >
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-dark">
                    <i class="bi bi-search"></i>
                    بحث
                </button>

                <a href="{{ route('admin.plans.index') }}" class="btn btn-outline-secondary">
                    إعادة ضبط
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <strong>قائمة الباقات</strong>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الباقة</th>
                        <th>Slug</th>
                        <th>شهري</th>
                        <th>سنوي</th>
                        <th>العملة</th>
                        <th>الخصائص</th>
                        <th>الحالة</th>
                        <th>مميزة</th>
                        <th>الترتيب</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($plans as $plan)
                        <tr>
                            <td>{{ $plan->id }}</td>

                            <td>
                                <div class="fw-semibold">{{ $plan->name }}</div>

                                @if($plan->description)
                                    <small class="text-body-secondary">
                                        {{ \Illuminate\Support\Str::limit($plan->description, 70) }}
                                    </small>
                                @endif

                                @if($plan->is_free)
                                    <div>
                                        <span class="badge bg-info mt-1">مجانية</span>
                                    </div>
                                @endif
                            </td>

                            <td>
                                <code>{{ $plan->slug }}</code>
                            </td>

                            <td>
                                {{ number_format((float) $plan->monthly_price, 2) }}
                            </td>

                            <td>
                                {{ $plan->yearly_price !== null ? number_format((float) $plan->yearly_price, 2) : '-' }}
                            </td>

                            <td>{{ $plan->currency }}</td>

                            <td>
                                <span class="badge bg-secondary">
                                    {{ $plan->features_count }}
                                </span>
                            </td>

                            <td>
                                @if($plan->is_active)
                                    <span class="badge bg-success">نشطة</span>
                                @else
                                    <span class="badge bg-danger">غير نشطة</span>
                                @endif
                            </td>

                            <td>
                                @if($plan->is_featured)
                                    <span class="badge bg-warning text-dark">مميزة</span>
                                @else
                                    <span class="text-body-secondary">-</span>
                                @endif
                            </td>

                            <td>{{ $plan->sort_order }}</td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    @can('plans.edit')
                                        <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-sm btn-outline-primary">
                                            تعديل
                                        </a>

                                        <form method="POST" action="{{ route('admin.plans.toggle-status', $plan) }}">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit" class="btn btn-sm btn-outline-warning">
                                                {{ $plan->is_active ? 'تعطيل' : 'تفعيل' }}
                                            </button>
                                        </form>
                                    @endcan

                                    @can('plans.delete')
                                        <form
                                            method="POST"
                                            action="{{ route('admin.plans.destroy', $plan) }}"
                                            onsubmit="return confirm('هل أنت متأكد من حذف هذه الباقة؟')"
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
                            <td colspan="11" class="text-center text-body-secondary py-4">
                                لا توجد باقات.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $plans->links() }}
        </div>
    </div>
</div>
@endsection