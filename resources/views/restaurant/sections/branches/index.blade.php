{{-- resources/views/restaurant/branches/index.blade.php --}}
@extends('restaurant.layouts.app')

@section('title', 'فروع المطعم')

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
            <h1 class="h4 mb-1">فروع المطعم</h1>
            <p class="text-muted small mb-0">
                المطعم: <strong>{{ $restaurant->name }}</strong>
            </p>
        </div>

        <a href="{{ route('restaurant.branches.create') }}" class="btn btn-primary">
            + إضافة فرع جديد
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>اسم الفرع</th>
                            <th>المدينة</th>
                            <th>العنوان</th>
                            <th>الهاتف</th>
                            <th>الحالة</th>
                            <th class="text-end">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($branches as $branch)
                            <tr>
                                <td>{{ $branch->id }}</td>
                                <td class="fw-semibold">
                                    {{ $branch->name }}
                                    @if($branch->slug)
                                        <div class="small text-muted">
                                            slug: {{ $branch->slug }}
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $branch->city ?: '-' }}</td>
                                <td>
                                    <span class="small text-muted">
                                        {{ Str::limit($branch->address, 60) ?: '-' }}
                                    </span>
                                </td>
                                <td>{{ $branch->phone ?: '-' }}</td>
                                <td>
                                    @if($branch->is_active)
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
                                        <a href="{{ route('restaurant.branches.edit', $branch) }}"
                                           class="btn btn-outline-primary">
                                            تعديل
                                        </a>

                                        <form method="POST"
                                              action="{{ route('restaurant.branches.toggle', $branch) }}"
                                              onsubmit="return confirm('هل أنت متأكد من تغيير حالة هذا الفرع؟');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-secondary">
                                                {{ $branch->is_active ? 'تعطيل' : 'تفعيل' }}
                                            </button>
                                        </form>
                                        <a href="{{ route('restaurant.branches.qr.show', $branch) }}"
                                           class="btn btn-outline-info">
                                            رمز QR
                                        </a>

                                        <form method="POST"
                                              action="{{ route('restaurant.branches.destroy', $branch) }}"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الفرع؟ لا يمكن التراجع.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger">
                                                حذف
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    لا توجد فروع مسجّلة حتى الآن. قم بإضافة أول فرع لمطعمك.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($branches instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="card-footer">
                {{ $branches->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
