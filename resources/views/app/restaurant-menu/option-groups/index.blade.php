{{-- resources/views/app/restaurant-menu/option-groups/index.blade.php --}}
@extends('app.layouts.app')

@section('title', 'مجموعات الإضافات')
@section('page_title', 'مجموعات الإضافات')
@section('page_description', 'إدارة مجموعات الإضافات والاختيارات للصنف: ' . $restaurantMenuItem->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <a href="{{ route('app.restaurant-menu.items.index', $workspace) }}" class="btn btn-sm btn-light mb-2">
            <i class="bi bi-arrow-right"></i>
            رجوع للأصناف
        </a>

        <div>
            <strong>
                عدد المجموعات:
                {{ $groups->total() }}
                /
                {{ $isUnlimited ? 'غير محدود' : $groupsLimit }}
            </strong>
        </div>
    </div>

    <a href="{{ route('app.restaurant-menu.items.option-groups.create', [$workspace, $restaurantMenuItem]) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        إضافة مجموعة
    </a>
</div>

<div class="card content-card mb-4">
    <div class="card-body p-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-8">
                <div class="d-flex gap-3 align-items-center">
                    @if($restaurantMenuItem->image)
                        <img
                            src="{{ asset('storage/' . $restaurantMenuItem->image) }}"
                            alt="{{ $restaurantMenuItem->name }}"
                            class="rounded border"
                            style="width: 72px; height: 72px; object-fit: cover;"
                        >
                    @else
                        <div class="bg-light border rounded d-flex align-items-center justify-content-center"
                             style="width: 72px; height: 72px;">
                            <i class="bi bi-image text-muted"></i>
                        </div>
                    @endif

                    <div>
                        <h5 class="fw-bold mb-1">{{ $restaurantMenuItem->name }}</h5>

                        <div class="text-muted small">
                            الفرع:
                            {{ $restaurantMenuItem->branch?->name ?: '-' }}
                            —
                            التصنيف:
                            {{ $restaurantMenuItem->category?->name ?: '-' }}
                        </div>

                        <div class="small mt-1">
                            السعر الأساسي:
                            <strong>
                                {{ number_format((float) $restaurantMenuItem->price, 2) }}
                                {{ $restaurantMenuItem->currency }}
                            </strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <form method="GET" action="{{ route('app.restaurant-menu.items.option-groups.index', [$workspace, $restaurantMenuItem]) }}" class="row g-2">
                    <div class="col-12">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="form-control"
                            placeholder="بحث باسم المجموعة"
                        >
                    </div>

                    <div class="col-6">
                        <select name="type" class="form-select">
                            <option value="">كل الأنواع</option>
                            <option value="single" @selected(request('type') === 'single')>اختيار واحد</option>
                            <option value="multiple" @selected(request('type') === 'multiple')>اختيارات متعددة</option>
                        </select>
                    </div>

                    <div class="col-6">
                        <select name="status" class="form-select">
                            <option value="">كل الحالات</option>
                            <option value="active" @selected(request('status') === 'active')>نشط</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>غير نشط</option>
                        </select>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-dark btn-sm">
                            بحث
                        </button>

                        <a href="{{ route('app.restaurant-menu.items.option-groups.index', [$workspace, $restaurantMenuItem]) }}" class="btn btn-outline-secondary btn-sm">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card content-card">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المجموعة</th>
                        <th>النوع</th>
                        <th>إجباري</th>
                        <th>الاختيارات</th>
                        <th>Min / Max</th>
                        <th>الترتيب</th>
                        <th>الحالة</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($groups as $group)
                        <tr>
                            <td>{{ $group->id }}</td>

                            <td>
                                <div class="fw-semibold">{{ $group->name }}</div>
                            </td>

                            <td>
                                @if($group->type === 'single')
                                    <span class="badge bg-primary">اختيار واحد</span>
                                @else
                                    <span class="badge bg-info">اختيارات متعددة</span>
                                @endif
                            </td>

                            <td>
                                @if($group->is_required)
                                    <span class="badge bg-warning text-dark">إجباري</span>
                                @else
                                    <span class="text-muted">اختياري</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-secondary">
                                    {{ $group->options_count }}
                                </span>
                            </td>

                            <td>
                                <span class="small">
                                    {{ $group->min_choices ?? 0 }}
                                    /
                                    {{ $group->max_choices ?: 'غير محدد' }}
                                </span>
                            </td>

                            <td>{{ $group->sort_order }}</td>

                            <td>
                                @if($group->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">غير نشط</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    <a
                                        href="{{ route('app.restaurant-menu.items.option-groups.options.index', [$workspace, $restaurantMenuItem, $group]) }}"
                                        class="btn btn-sm btn-outline-success"
                                    >
                                        الخيارات
                                    </a>

                                    <a
                                        href="{{ route('app.restaurant-menu.items.option-groups.edit', [$workspace, $restaurantMenuItem, $group]) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        تعديل
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('app.restaurant-menu.items.option-groups.destroy', [$workspace, $restaurantMenuItem, $group]) }}"
                                        onsubmit="return confirm('هل أنت متأكد من حذف هذه المجموعة؟ سيتم حذف كل الخيارات التابعة لها.')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                لا توجد مجموعات إضافات لهذا الصنف بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $groups->links() }}
        </div>
    </div>
</div>
@endsection