{{-- resources/views/restaurant/item_options/index.blade.php --}}
@extends('restaurant.layouts.app')

@section('title', 'خيارات الصنف - ' . $item->name)

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
            <h1 class="h4 mb-1">خيارات الصنف</h1>
            <p class="text-muted small mb-0">
                المطعم: <strong>{{ $restaurant->name }}</strong> —
                الفرع الحالي: <strong>{{ $branch->name }}</strong> —
                الصنف: <strong>{{ $item->name }}</strong> —
                المجموعة: <strong>{{ $group->name }}</strong>
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('restaurant.items.option-groups.index', $item) }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right-circle ms-1"></i> الرجوع لمجموعات الخيارات
            </a>

            <a href="{{ route('restaurant.items.option-groups.options.create', [$item, $group]) }}"
               class="btn btn-primary">
                <i class="bi bi-plus-circle ms-1"></i> إضافة خيار جديد
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>اسم الخيار</th>
                            <th style="width: 150px;">فرق السعر</th>
                            <th style="width: 120px;">الترتيب</th>
                            <th style="width: 120px;">الحالة</th>
                            <th style="width: 220px;" class="text-end">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($options as $option)
                            <tr>
                                <td>{{ $option->id }}</td>

                                <td class="fw-semibold">
                                    {{ $option->name }}
                                </td>

                                <td>
                                    @php
                                        $delta = (float)($option->price_delta ?? 0);
                                    @endphp
                                    @if($delta > 0)
                                        <span class="badge bg-light text-muted border">
                                            + {{ number_format($delta, 2) }} ج
                                        </span>
                                    @else
                                        <span class="text-muted small">0.00 ج (بدون زيادة)</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-light text-muted">
                                        {{ $option->sort_order ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    @if($option->is_active)
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
                                        {{-- تعديل --}}
                                        <a href="{{ route('restaurant.items.option-groups.options.edit', [$item, $group, $option]) }}"
                                           class="btn btn-outline-primary">
                                            <i class="bi bi-pencil-square"></i> تعديل
                                        </a>

                                        {{-- تفعيل / تعطيل --}}
                                        <form method="POST"
                                              action="{{ route('restaurant.items.option-groups.options.toggle', [$item, $group, $option]) }}"
                                              onsubmit="return confirm('تأكيد تغيير حالة هذا الخيار؟');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-secondary">
                                                {{ $option->is_active ? 'تعطيل' : 'تفعيل' }}
                                            </button>
                                        </form>

                                        {{-- حذف --}}
                                        <form method="POST"
                                              action="{{ route('restaurant.items.option-groups.options.destroy', [$item, $group, $option]) }}"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الخيار؟');">
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
                                <td colspan="6" class="text-center text-muted py-4">
                                    لا توجد خيارات لهذه المجموعة حتى الآن.
                                    <br>
                                    <a href="{{ route('restaurant.items.option-groups.options.create', [$item, $group]) }}"
                                       class="btn btn-sm btn-primary mt-2">
                                        إضافة أول خيار
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($options instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="card-footer">
                {{ $options->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
