{{-- resources/views/app/business-categories/index.blade.php --}}
@extends('app.layouts.app')

@section('title', 'التصنيفات')
@section('page_title', 'التصنيفات')
@section('page_description', 'نظّم منتجاتك داخل تصنيفات لتظهر بشكل أوضح في الصفحة العامة.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <strong>عدد التصنيفات: {{ $categories->total() }}</strong>
    </div>

    <a href="{{ route('app.categories.create', $workspace) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        إضافة تصنيف
    </a>
</div>

<div class="card content-card">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم التصنيف</th>
                        <th>عدد المنتجات</th>
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
                                <div class="fw-semibold">{{ $category->name }}</div>

                                @if($category->description)
                                    <small class="text-muted">
                                        {{ \Illuminate\Support\Str::limit($category->description, 80) }}
                                    </small>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-secondary">
                                    {{ $category->products_count }}
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
                                    <a href="{{ route('app.categories.edit', [$workspace, $category]) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        تعديل
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('app.categories.destroy', [$workspace, $category]) }}"
                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟ المنتجات المرتبطة به ستبقى بدون تصنيف.')"
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
                                لا توجد تصنيفات بعد.
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