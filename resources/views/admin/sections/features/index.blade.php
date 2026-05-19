@extends('admin.layout.admin_app')

@section('title', 'الخصائص')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">الخصائص</h1>
        <p class="text-body-secondary mb-0">إدارة خصائص وحدود الباقات مثل عدد المنتجات والروابط والعملاء.</p>
    </div>

    @can('features.create')
        <a href="{{ route('admin.features.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            إضافة خاصية
        </a>
    @endcan
</div>

@include('admin.sections.features.partials.alerts')

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.features.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">بحث</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="الاسم، المفتاح، الموديول"
                >
            </div>

            <div class="col-md-3">
                <label class="form-label">النوع</label>
                <select name="type" class="form-select">
                    <option value="">كل الأنواع</option>
                    <option value="limit" @selected(request('type') === 'limit')>Limit</option>
                    <option value="boolean" @selected(request('type') === 'boolean')>Boolean</option>
                    <option value="text" @selected(request('type') === 'text')>Text</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">الموديول</label>
                <select name="module" class="form-select">
                    <option value="">كل الموديولات</option>
                    @foreach($modules as $module)
                        <option value="{{ $module }}" @selected(request('module') === $module)>
                            {{ $module }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark">
                    بحث
                </button>

                <a href="{{ route('admin.features.index') }}" class="btn btn-outline-secondary">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <strong>قائمة الخصائص</strong>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>المفتاح</th>
                        <th>النوع</th>
                        <th>الموديول</th>
                        <th>الترتيب</th>
                        <th>الحالة</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($features as $feature)
                        <tr>
                            <td>{{ $feature->id }}</td>

                            <td>
                                <div class="fw-semibold">{{ $feature->name }}</div>
                                @if($feature->description)
                                    <small class="text-body-secondary">
                                        {{ \Illuminate\Support\Str::limit($feature->description, 70) }}
                                    </small>
                                @endif
                            </td>

                            <td>
                                <code>{{ $feature->key }}</code>
                            </td>

                            <td>
                                @if($feature->type === 'limit')
                                    <span class="badge bg-primary">Limit</span>
                                @elseif($feature->type === 'boolean')
                                    <span class="badge bg-info">Boolean</span>
                                @else
                                    <span class="badge bg-secondary">Text</span>
                                @endif
                            </td>

                            <td>{{ $feature->module ?: '-' }}</td>

                            <td>{{ $feature->sort_order }}</td>

                            <td>
                                @if($feature->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">غير نشط</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    @can('features.edit')
                                        <a href="{{ route('admin.features.edit', $feature) }}" class="btn btn-sm btn-outline-primary">
                                            تعديل
                                        </a>

                                        <form method="POST" action="{{ route('admin.features.toggle-status', $feature) }}">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit" class="btn btn-sm btn-outline-warning">
                                                {{ $feature->is_active ? 'تعطيل' : 'تفعيل' }}
                                            </button>
                                        </form>
                                    @endcan

                                    @can('features.delete')
                                        <form
                                            method="POST"
                                            action="{{ route('admin.features.destroy', $feature) }}"
                                            onsubmit="return confirm('هل أنت متأكد من حذف هذه الخاصية؟')"
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
                                لا توجد خصائص.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $features->links() }}
        </div>
    </div>
</div>
@endsection