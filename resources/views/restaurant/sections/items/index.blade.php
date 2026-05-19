{{-- resources/views/restaurant/items/index.blade.php --}}
@extends('restaurant.layouts.app')

@section('title', 'أصناف المنيو')

@section('content')
<div class="container py-4">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
    </div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1">أصناف المنيو</h1>
            <p class="text-muted small mb-0">
                المطعم: <strong>{{ $restaurant->name }}</strong> —
                الفرع الحالي: <strong>{{ $branch->name }}</strong>
            </p>
        </div>

        <a href="{{ route('restaurant.items.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle ms-1"></i> إضافة صنف جديد
        </a>
    </div>

    {{-- فلترة بسيطة --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('restaurant.items.index') }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label" for="q">بحث بالاسم</label>
                    <input type="text" name="q" id="q" value="{{ request('q') }}" class="form-control"
                        placeholder="مثال: بيج برجر">
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label" for="category_id">التصنيف</label>
                    <select name="category_id" id="category_id" class="form-select">
                        <option value="">كل التصنيفات</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (string)$cat->id === (string)request('category_id') ?
                            'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1">
                        <i class="bi bi-search"></i> تطبيق الفلتر
                    </button>
                    @if(request()->hasAny(['q','category_id']) && (request('q') || request('category_id')))
                    <a href="{{ route('restaurant.items.index') }}" class="btn btn-outline-secondary">
                        مسح
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th style="width: 80px;">الصورة</th>
                            <th>الصنف</th>
                            <th>التصنيف</th>
                            <th style="width: 150px;">السعر</th>
                            <th style="width: 160px;">التاجز</th>
                            <th style="width: 120px;">الحالة</th>
                            <th style="width: 210px;" class="text-end">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                        <tr>
                            <td>{{ $item->id }}</td>

                            {{-- الصورة --}}
                            <td>
                                @if($item->image_path)
                                <img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->name }}"
                                    class="img-thumbnail" style="width:60px; height:60px; object-fit:cover;">
                                @else
                                <span class="text-muted small">لا توجد</span>
                                @endif
                            </td>

                            {{-- الاسم + وصف صغير --}}
                            <td>
                                <div class="fw-semibold">{{ $item->name }}</div>
                                @if($item->description)
                                <div class="small text-muted">{{ Str::limit($item->description, 50) }}</div>
                                @endif
                            </td>

                            <td>{{ $item->category?->name ?? '-' }}</td>

                            {{-- السعر + سعر العرض إن وجد --}}
                            <td>
                                @if($item->offer_price && $item->offer_price > 0 && $item->offer_price < $item->price)
                                    <div class="small text-muted text-decoration-line-through">
                                        {{ number_format($item->price, 2) }}
                                    </div>
                                    <div class="fw-bold text-danger">
                                        {{ number_format($item->offer_price, 2) }}
                                    </div>
                                    @else
                                    <div class="fw-semibold">
                                        {{ number_format($item->price, 2) }}
                                    </div>
                                    @endif
                            </td>

                            {{-- التاجز --}}
                            <td>
                                @if($item->tags_array)
                                @foreach($item->tags_array as $tag)
                                <span class="badge text-muted border me-1">
                                    {{ $tag }}
                                </span>
                                @endforeach
                                @else
                                <span class="text-muted small">-</span>
                                @endif
                            </td>

                            {{-- الحالة + الإجراءات ... (كما كانت) --}}
                            <td>
                                @if($item->is_active)
                                <span class="badge bg-success">مفعّل</span>
                                @else
                                <span class="badge bg-secondary">معطّل</span>
                                @endif
                            </td>

                            <td class="text-end">
                                <div class="btn-group btn-group-sm" role="group">
                                    {{-- تعديل الصنف --}}
                                    <a href="{{ route('restaurant.items.edit', $item) }}"
                                        class="btn btn-outline-primary">
                                        <i class="bi bi-pencil-square"></i> تعديل
                                    </a>

                                    {{-- مجموعات الخيارات لهذا الصنف --}}
                                    <a href="{{ route('restaurant.items.option-groups.index', $item) }}"
                                        class="btn btn-outline-info">
                                        <i class="bi bi-sliders"></i> مجموعات الخيارات
                                    </a>

                                    {{-- تفعيل / تعطيل --}}
                                    <form method="POST" action="{{ route('restaurant.items.toggle', $item) }}"
                                        onsubmit="return confirm('تأكيد تغيير حالة هذا الصنف؟');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-secondary">
                                            {{ $item->is_active ? 'تعطيل' : 'تفعيل' }}
                                        </button>
                                    </form>

                                    {{-- حذف --}}
                                    <form method="POST" action="{{ route('restaurant.items.destroy', $item) }}"
                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا الصنف؟ قد يؤثر على الطلبات المرتبطة.');">
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
                            <td colspan="8" class="text-center text-muted py-4">
                                لا توجد أصناف لعرضها.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>

        @if($items instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="card-footer">
            {{ $items->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
@endsection