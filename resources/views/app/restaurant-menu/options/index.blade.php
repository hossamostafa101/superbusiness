{{-- resources/views/app/restaurant-menu/options/index.blade.php --}}
@extends('app.layouts.app')

@section('title', 'خيارات الإضافات')
@section('page_title', 'خيارات الإضافات')
@section('page_description', 'إدارة خيارات المجموعة: ' . $restaurantItemOptionGroup->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <a href="{{ route('app.restaurant-menu.items.option-groups.index', [$workspace, $restaurantMenuItem]) }}"
           class="btn btn-sm btn-light mb-2">
            <i class="bi bi-arrow-right"></i>
            رجوع للمجموعات
        </a>

        <div>
            <strong>
                عدد الخيارات:
                {{ $options->total() }}
                /
                {{ $isUnlimited ? 'غير محدود' : $optionsLimit }}
            </strong>
        </div>
    </div>

    <a href="{{ route('app.restaurant-menu.items.option-groups.options.create', [$workspace, $restaurantMenuItem, $restaurantItemOptionGroup]) }}"
       class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        إضافة خيار
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
                            المجموعة:
                            <strong>{{ $restaurantItemOptionGroup->name }}</strong>
                            —
                            النوع:
                            @if($restaurantItemOptionGroup->type === 'single')
                                اختيار واحد
                            @else
                                اختيارات متعددة
                            @endif
                        </div>

                        <div class="small mt-1">
                            @if($restaurantItemOptionGroup->is_required)
                                <span class="badge bg-warning text-dark">إجباري</span>
                            @else
                                <span class="badge bg-secondary">اختياري</span>
                            @endif

                            <span class="ms-1">
                                Min:
                                {{ $restaurantItemOptionGroup->min_choices ?? 0 }}
                                /
                                Max:
                                {{ $restaurantItemOptionGroup->max_choices ?: 'غير محدد' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <form method="GET"
                      action="{{ route('app.restaurant-menu.items.option-groups.options.index', [$workspace, $restaurantMenuItem, $restaurantItemOptionGroup]) }}"
                      class="row g-2">
                    <div class="col-7">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="form-control"
                            placeholder="بحث باسم الخيار"
                        >
                    </div>

                    <div class="col-5">
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            <option value="active" @selected(request('status') === 'active')>نشط</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>غير نشط</option>
                        </select>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-dark btn-sm">
                            بحث
                        </button>

                        <a href="{{ route('app.restaurant-menu.items.option-groups.options.index', [$workspace, $restaurantMenuItem, $restaurantItemOptionGroup]) }}"
                           class="btn btn-outline-secondary btn-sm">
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
                        <th>الخيار</th>
                        <th>السعر الإضافي</th>
                        <th>الترتيب</th>
                        <th>الحالة</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($options as $option)
                        <tr>
                            <td>{{ $option->id }}</td>

                            <td>
                                <div class="fw-semibold">{{ $option->name }}</div>
                            </td>

                            <td>
                                @if((float) $option->price > 0)
                                    <strong>
                                        +{{ number_format((float) $option->price, 2) }}
                                        {{ $option->currency }}
                                    </strong>
                                @else
                                    <span class="text-muted">بدون تكلفة</span>
                                @endif
                            </td>

                            <td>{{ $option->sort_order }}</td>

                            <td>
                                @if($option->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">غير نشط</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a
                                        href="{{ route('app.restaurant-menu.items.option-groups.options.edit', [$workspace, $restaurantMenuItem, $restaurantItemOptionGroup, $option]) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        تعديل
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('app.restaurant-menu.items.option-groups.options.destroy', [$workspace, $restaurantMenuItem, $restaurantItemOptionGroup, $option]) }}"
                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا الخيار؟')"
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
                            <td colspan="6" class="text-center text-muted py-4">
                                لا توجد خيارات داخل هذه المجموعة بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $options->links() }}
        </div>
    </div>
</div>
@endsection