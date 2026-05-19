{{-- resources/views/app/business-products/index.blade.php --}}
@extends('app.layouts.app')

@section('title', 'المنتجات')
@section('page_title', 'المنتجات')
@section('page_description', 'أضف منتجاتك أو خدماتك لتظهر داخل الصفحة العامة مع زر طلب عبر واتساب.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <strong>
            عدد المنتجات:
            {{ $products->total() }}
            /
            {{ $isUnlimited ? 'غير محدود' : $productsLimit }}
        </strong>
    </div>

    <a href="{{ route('app.products.create', $workspace) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        إضافة منتج
    </a>
</div>

<div class="card content-card mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('app.products.index', $workspace) }}" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">بحث</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="اسم المنتج أو الوصف"
                >
            </div>

            <div class="col-md-4">
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

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-dark">
                    بحث
                </button>

                <a href="{{ route('app.products.index', $workspace) }}" class="btn btn-outline-secondary">
                    إعادة ضبط
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
                        <th>المنتج</th>
                        <th>التصنيف</th>
                        <th>السعر</th>
                        <th>الترتيب</th>
                        <th>الحالة</th>
                        <th>مميز</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>

                            <td>
                                @if($product->image)
                                    <img
                                        src="{{ asset('storage/' . $product->image) }}"
                                        alt="{{ $product->name }}"
                                        class="rounded border"
                                        style="width: 60px; height: 60px; object-fit: cover;"
                                    >
                                @else
                                    <div class="bg-light border rounded d-flex align-items-center justify-content-center"
                                         style="width: 60px; height: 60px;">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                @endif
                            </td>

                            <td>
                                <div class="fw-semibold">{{ $product->name }}</div>

                                @if($product->description)
                                    <small class="text-muted">
                                        {{ \Illuminate\Support\Str::limit($product->description, 70) }}
                                    </small>
                                @endif
                            </td>

                            <td>
                                {{ $product->category?->name ?: '-' }}
                            </td>

                            <td>
                                @if($product->sale_price)
                                    <div>
                                        <span class="fw-bold text-success">
                                            {{ number_format((float) $product->sale_price, 2) }}
                                            {{ $product->currency }}
                                        </span>
                                    </div>
                                    @if($product->price)
                                        <small class="text-muted text-decoration-line-through">
                                            {{ number_format((float) $product->price, 2) }}
                                            {{ $product->currency }}
                                        </small>
                                    @endif
                                @elseif($product->price)
                                    <span class="fw-bold">
                                        {{ number_format((float) $product->price, 2) }}
                                        {{ $product->currency }}
                                    </span>
                                @else
                                    <span class="text-muted">بدون سعر</span>
                                @endif
                            </td>

                            <td>{{ $product->sort_order }}</td>

                            <td>
                                @if($product->is_available)
                                    <span class="badge bg-success">متاح</span>
                                @else
                                    <span class="badge bg-danger">غير متاح</span>
                                @endif
                            </td>

                            <td>
                                @if($product->is_featured)
                                    <span class="badge bg-warning text-dark">مميز</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('app.products.edit', [$workspace, $product]) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        تعديل
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('app.products.destroy', [$workspace, $product]) }}"
                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا المنتج؟')"
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
                                لا توجد منتجات بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection