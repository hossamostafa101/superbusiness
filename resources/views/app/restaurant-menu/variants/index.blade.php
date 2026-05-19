{{-- resources/views/app/restaurant-menu/variants/index.blade.php --}}
@extends('app.layouts.app')

@section('title', 'Variants الصنف')
@section('page_title', 'Variants الصنف')
@section('page_description', 'إدارة الأحجام أو النسخ المختلفة للصنف: ' . $restaurantMenuItem->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <a href="{{ route('app.restaurant-menu.items.index', $workspace) }}" class="btn btn-sm btn-light mb-2">
            <i class="bi bi-arrow-right"></i>
            رجوع للأصناف
        </a>

        <div>
            <strong>
                عدد الـ Variants:
                {{ $variants->total() }}
                /
                {{ $isUnlimited ? 'غير محدود' : $variantsLimit }}
            </strong>
        </div>
    </div>

    <a href="{{ route('app.restaurant-menu.items.variants.create', [$workspace, $restaurantMenuItem]) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        إضافة Variant
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
                <form method="GET" action="{{ route('app.restaurant-menu.items.variants.index', [$workspace, $restaurantMenuItem]) }}" class="row g-2">
                    <div class="col-7">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="form-control"
                            placeholder="بحث"
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

                        <a href="{{ route('app.restaurant-menu.items.variants.index', [$workspace, $restaurantMenuItem]) }}" class="btn btn-outline-secondary btn-sm">
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
                        <th>الاسم</th>
                        <th>السعر</th>
                        <th>الترتيب</th>
                        <th>الافتراضي</th>
                        <th>الحالة</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($variants as $variant)
                        <tr>
                            <td>{{ $variant->id }}</td>

                            <td>
                                <div class="fw-semibold">{{ $variant->name }}</div>
                            </td>

                            <td>
                                @if($variant->sale_price)
                                    <div class="fw-bold text-success">
                                        {{ number_format((float) $variant->sale_price, 2) }}
                                        {{ $variant->currency }}
                                    </div>

                                    <small class="text-muted text-decoration-line-through">
                                        {{ number_format((float) $variant->price, 2) }}
                                        {{ $variant->currency }}
                                    </small>
                                @else
                                    <div class="fw-bold">
                                        {{ number_format((float) $variant->price, 2) }}
                                        {{ $variant->currency }}
                                    </div>
                                @endif
                            </td>

                            <td>{{ $variant->sort_order }}</td>

                            <td>
                                @if($variant->is_default)
                                    <span class="badge bg-primary">افتراضي</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                @if($variant->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">غير نشط</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a
                                        href="{{ route('app.restaurant-menu.items.variants.edit', [$workspace, $restaurantMenuItem, $variant]) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        تعديل
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('app.restaurant-menu.items.variants.destroy', [$workspace, $restaurantMenuItem, $variant]) }}"
                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا الـ Variant؟')"
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
                            <td colspan="7" class="text-center text-muted py-4">
                                لا توجد Variants لهذا الصنف بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $variants->links() }}
        </div>
    </div>
</div>
@endsection