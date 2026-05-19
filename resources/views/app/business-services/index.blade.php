{{-- resources/views/app/business-services/index.blade.php --}}
@extends('app.layouts.app')

@section('title', 'الخدمات')
@section('page_title', 'الخدمات')
@section('page_description', 'إدارة الخدمات التي يمكن ربطها بالمواعيد والحجوزات.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <strong>
            عدد الخدمات:
            {{ $services->total() }}
            /
            {{ $isUnlimited ? 'غير محدود' : $servicesLimit }}
        </strong>
    </div>

    <a href="{{ route('app.services.create', $workspace) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        إضافة خدمة
    </a>
</div>

<div class="card content-card mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('app.services.index', $workspace) }}" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label">بحث</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="اسم الخدمة أو الوصف"
                >
            </div>

            <div class="col-md-3">
                <label class="form-label">الحالة</label>
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    <option value="active" @selected(request('status') === 'active')>نشطة</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>غير نشطة</option>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-dark">
                    بحث
                </button>

                <a href="{{ route('app.services.index', $workspace) }}" class="btn btn-outline-secondary">
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
                        <th>الخدمة</th>
                        <th>المدة</th>
                        <th>السعر</th>
                        <th>المواعيد</th>
                        <th>الترتيب</th>
                        <th>الحالة</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($services as $service)
                        <tr>
                            <td>{{ $service->id }}</td>

                            <td>
                                <div class="fw-semibold">{{ $service->name }}</div>

                                @if($service->description)
                                    <small class="text-muted">
                                        {{ \Illuminate\Support\Str::limit($service->description, 80) }}
                                    </small>
                                @endif
                            </td>

                            <td>
                                {{ $service->duration_minutes }} دقيقة
                            </td>

                            <td>
                                @if($service->price !== null)
                                    <strong>
                                        {{ number_format((float) $service->price, 2) }}
                                    </strong>
                                    {{ $service->currency }}
                                @else
                                    <span class="text-muted">بدون سعر</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-info">
                                    {{ $service->appointments_count }}
                                </span>
                            </td>

                            <td>{{ $service->sort_order }}</td>

                            <td>
                                @if($service->is_active)
                                    <span class="badge bg-success">نشطة</span>
                                @else
                                    <span class="badge bg-danger">غير نشطة</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a
                                        href="{{ route('app.services.edit', [$workspace, $service]) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        تعديل
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('app.services.destroy', [$workspace, $service]) }}"
                                        onsubmit="return confirm('هل أنت متأكد من حذف هذه الخدمة؟ المواعيد المرتبطة بها ستبقى بدون خدمة.')"
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
                                لا توجد خدمات بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $services->links() }}
        </div>
    </div>
</div>
@endsection