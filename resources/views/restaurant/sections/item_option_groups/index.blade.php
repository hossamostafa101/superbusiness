{{-- resources/views/restaurant/item_option_groups/index.blade.php --}}
@extends('restaurant.layouts.app')

@section('title', 'مجموعات الخيارات - ' . $item->name)

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
            <h1 class="h4 mb-1">مجموعات الخيارات للصنف</h1>
            <p class="text-muted small mb-0">
                المطعم: <strong>{{ $restaurant->name }}</strong> —
                الفرع الحالي: <strong>{{ $branch->name }}</strong> —
                الصنف: <strong>{{ $item->name }}</strong>
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('restaurant.items.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right-circle ms-1"></i> الرجوع لقائمة الأصناف
            </a>
            <a href="{{ route('restaurant.items.option-groups.create', $item) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle ms-1"></i> إضافة مجموعة جديدة
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
                            <th>اسم المجموعة</th>
                            <th style="width: 120px;">النوع</th>
                            <th style="width: 120px;">إجباري؟</th>
                            <th style="width: 120px;">متعدد؟</th>
                            <th style="width: 120px;">الترتيب</th>
                            <th style="width: 120px;">الحالة</th>
                            <th style="width: 200px;" class="text-end">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groups as $group)
                            <tr>
                                <td>{{ $group->id }}</td>

                                <td>
                                    <div class="fw-semibold">{{ $group->name }}</div>
                                    @if($group->type)
                                        <div class="small text-muted">
                                            النوع: {{ $group->type }}
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-light text-muted">
                                        {{ $group->type ?: '-' }}
                                    </span>
                                </td>

                                <td>
                                    @if($group->is_required)
                                        <span class="badge bg-warning text-dark">نعم</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                            لا
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if($group->is_multi)
                                        <span class="badge bg-info-subtle text-info border border-info-subtle">
                                            نعم
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                            لا
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-light text-muted">
                                        {{ $group->sort_order ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    @if($group->is_active)
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
        {{-- تعديل المجموعة --}}
        <a href="{{ route('restaurant.items.option-groups.edit', [$item, $group]) }}"
           class="btn btn-outline-primary">
            <i class="bi bi-pencil-square"></i> تعديل
        </a>

        {{-- إدارة الخيارات داخل هذه المجموعة --}}
        <a href="{{ route('restaurant.items.option-groups.options.index', [$item, $group]) }}"
           class="btn btn-outline-info">
            <i class="bi bi-list-ul"></i> إدارة الخيارات
        </a>

        {{-- حذف --}}
        <form method="POST"
              action="{{ route('restaurant.items.option-groups.destroy', [$item, $group]) }}"
              onsubmit="return confirm('هل أنت متأكد من حذف هذه المجموعة؟ سيتم حذف الخيارات التابعة لها.');">
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
                                    لا توجد مجموعات خيارات لهذا الصنف حتى الآن.
                                    <br>
                                    <a href="{{ route('restaurant.items.option-groups.create', $item) }}"
                                       class="btn btn-sm btn-primary mt-2">
                                        إضافة أول مجموعة خيارات
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($groups instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="card-footer">
                {{ $groups->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
