{{-- resources/views/app/restaurant-menu/items/index.blade.php --}}
@extends('app.layouts.app')

@section('title', 'أصناف المنيو')
@section('page_title', 'أصناف المنيو')
@section('page_description', 'إدارة أصناف الطعام والمشروبات لكل فرع داخل المنيو.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <strong>
            عدد الأصناف:
            {{ $items->total() }}
            /
            {{ $isUnlimited ? 'غير محدود' : $itemsLimit }}
        </strong>
    </div>

    <a href="{{ route('app.restaurant-menu.items.create', $workspace) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        إضافة صنف
    </a>
</div>

<div class="card content-card mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('app.restaurant-menu.items.index', $workspace) }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">بحث</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="اسم الصنف أو الوصف"
                >
            </div>

            <div class="col-md-3">
                <label class="form-label">الفرع</label>
                <select name="branch_id" class="form-select">
                    <option value="">كل الفروع</option>

                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected((string) request('branch_id') === (string) $branch->id)>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">التصنيف</label>
                <select name="category_id" class="form-select">
                    <option value="">كل التصنيفات</option>

                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">الإتاحة</label>
                <select name="availability" class="form-select">
                    <option value="">الكل</option>
                    <option value="available" @selected(request('availability') === 'available')>متاح</option>
                    <option value="unavailable" @selected(request('availability') === 'unavailable')>غير متاح</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">مميز</label>
                <select name="featured" class="form-select">
                    <option value="">الكل</option>
                    <option value="1" @selected(request('featured') === '1')>مميز</option>
                    <option value="0" @selected(request('featured') === '0')>غير مميز</option>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-dark">
                    بحث
                </button>

                <a href="{{ route('app.restaurant-menu.items.index', $workspace) }}" class="btn btn-outline-secondary">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card content-card">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الصورة</th>
                        <th>الصنف</th>
                        <th>الفرع</th>
                        <th>التصنيف</th>
                        <th>السعر</th>
                        <th>الحالة</th>
                        <th>مميز</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>{{ $item->id }}</td>

                            <td>
                                @if($item->image)
                                    <img
                                        src="{{ asset('storage/' . $item->image) }}"
                                        alt="{{ $item->name }}"
                                        class="rounded border"
                                        style="width: 64px; height: 64px; object-fit: cover;"
                                    >
                                @else
                                    <div class="bg-light border rounded d-flex align-items-center justify-content-center"
                                         style="width: 64px; height: 64px;">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                @endif
                            </td>

                            <td>
                                <div class="fw-semibold">{{ $item->name }}</div>

                                @if($item->description)
                                    <small class="text-muted">
                                        {{ \Illuminate\Support\Str::limit($item->description, 70) }}
                                    </small>
                                @endif

                                <div class="small text-muted mt-1">
                                    @if($item->calories)
                                        {{ $item->calories }} كالوري
                                    @endif

                                    @if($item->preparation_time_minutes)
                                        @if($item->calories) — @endif
                                        {{ $item->preparation_time_minutes }} دقيقة
                                    @endif
                                </div>
                            </td>

                            <td>{{ $item->branch?->name ?: '-' }}</td>

                            <td>{{ $item->category?->name ?: '-' }}</td>

                            <td>
                                @if($item->sale_price)
                                    <div class="fw-bold text-success">
                                        {{ number_format((float) $item->sale_price, 2) }}
                                        {{ $item->currency }}
                                    </div>

                                    <small class="text-muted text-decoration-line-through">
                                        {{ number_format((float) $item->price, 2) }}
                                        {{ $item->currency }}
                                    </small>
                                @else
                                    <div class="fw-bold">
                                        {{ number_format((float) $item->price, 2) }}
                                        {{ $item->currency }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                @if($item->is_available)
                                    <span class="badge bg-success">متاح</span>
                                @else
                                    <span class="badge bg-danger">غير متاح</span>
                                @endif
                            </td>

                            <td>
                                @if($item->is_featured)
                                    <span class="badge bg-warning text-dark">مميز</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
    <a
        href="{{ route('app.restaurant-menu.items.variants.index', [$workspace, $item]) }}"
        class="btn btn-sm btn-outline-secondary"
    >
        Variants
    </a>

    <a
        href="{{ route('app.restaurant-menu.items.option-groups.index', [$workspace, $item]) }}"
        class="btn btn-sm btn-outline-success"
    >
        Addons
    </a>

    <a
        href="{{ route('app.restaurant-menu.items.edit', [$workspace, $item]) }}"
        class="btn btn-sm btn-outline-primary"
    >
        تعديل
    </a>

    <form
        method="POST"
        action="{{ route('app.restaurant-menu.items.destroy', [$workspace, $item]) }}"
        onsubmit="return confirm('هل أنت متأكد من حذف هذا الصنف؟')"
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
                                لا توجد أصناف منيو بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection