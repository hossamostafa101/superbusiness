{{-- resources/views/app/restaurant-menu/categories/index.blade.php --}}
@extends('app.layouts.app')

@section('title', 'تصنيفات المنيو')
@section('page_title', 'تصنيفات المنيو')
@section('page_description', 'إدارة أقسام المنيو مثل مشروبات، أطباق رئيسية، حلويات، عروض.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <strong>
            عدد التصنيفات:
            {{ $categories->total() }}
            /
            {{ $isUnlimited ? 'غير محدود' : $categoriesLimit }}
        </strong>
    </div>

    <a href="{{ route('app.restaurant-menu.categories.create', $workspace) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        إضافة تصنيف
    </a>
</div>

<div class="card content-card mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('app.restaurant-menu.categories.index', $workspace) }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">بحث</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="اسم التصنيف أو الوصف"
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
                <label class="form-label">الحالة</label>
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    <option value="active" @selected(request('status') === 'active')>نشط</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>غير نشط</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark">
                    بحث
                </button>

                <a href="{{ route('app.restaurant-menu.categories.index', $workspace) }}" class="btn btn-outline-secondary">
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
                        <th>التصنيف</th>
                        <th>الفرع</th>
                        <th>الأصناف</th>
                        <th>الترتيب</th>
                        <th>الحالة</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>

                            <td>
                                @if($category->image)
                                    <img
                                        src="{{ asset('storage/' . $category->image) }}"
                                        alt="{{ $category->name }}"
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
                                <div class="fw-semibold">{{ $category->name }}</div>

                                @if($category->description)
                                    <small class="text-muted">
                                        {{ \Illuminate\Support\Str::limit($category->description, 80) }}
                                    </small>
                                @endif
                            </td>

                            <td>
                                {{ $category->branch?->name ?: '-' }}
                            </td>

                            <td>
                                <span class="badge bg-info">
                                    {{ $category->items_count }}
                                </span>
                            </td>

                            <td>{{ $category->sort_order }}</td>

                            <td>
                                @if($category->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">غير نشط</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a
                                        href="{{ route('app.restaurant-menu.categories.edit', [$workspace, $category]) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        تعديل
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('app.restaurant-menu.categories.destroy', [$workspace, $category]) }}"
                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟ الأصناف المرتبطة به ستبقى بدون تصنيف.')"
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
                            <td colspan="8" class="text-center text-muted py-4">
                                لا توجد تصنيفات منيو بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $categories->links() }}
        </div>
    </div>
</div>
@endsection