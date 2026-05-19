{{-- resources/views/restaurant/categories/index.blade.php --}}
@extends('restaurant.layouts.app') {{-- غيّرها لو مسار الـ layout مختلف --}}

@section('title', 'تصنيفات المنيو')

@section('content')
<div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">تصنيفات المنيو</h1>
            <p class="text-muted small mb-0">
                المطعم: <strong>{{ $restaurant->name }}</strong> —
                الفرع الحالي: <strong>{{ $branch->name }}</strong>
            </p>
        </div>

        <a href="{{ route('restaurant.categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle ms-1"></i> إضافة تصنيف جديد
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>اسم التصنيف</th>
                            <th style="width: 120px;">الترتيب</th>
                            <th style="width: 120px;">الحالة</th>
                            <th style="width: 170px;" class="text-end">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>

                                <td>
                                    <div class="fw-semibold">{{ $category->name }}</div>
                                    @if($category->slug)
                                        <div class="small text-muted">
                                            slug: {{ $category->slug }}
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-light text-muted">
                                        {{ $category->sort_order ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    @if($category->is_active)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            مفعل
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                            معطل
                                        </span>
                                    @endif
                                </td>

                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('restaurant.categories.edit', $category) }}"
                                           class="btn btn-outline-primary">
                                            <i class="bi bi-pencil-square"></i> تعديل
                                        </a>

                                        <form method="POST"
                                              action="{{ route('restaurant.categories.destroy', $category) }}"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟ قد تتأثر المنتجات المرتبطة به.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger">
                                                <i class="bi bi-trash"></i> حذف
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    لا توجد تصنيفات حتى الآن لهذا الفرع.
                                    <br>
                                    <a href="{{ route('restaurant.categories.create') }}" class="btn btn-sm btn-primary mt-2">
                                        إضافة أول تصنيف
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($categories instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="card-footer">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
