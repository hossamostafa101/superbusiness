@extends('app.layouts.app')

@section('title', 'الخدمات الطبية')
@section('page_title', 'الخدمات الطبية')
@section('page_description', 'إدارة الكشف والتحاليل والأشعة والإجراءات الطبية.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <strong>
        عدد الخدمات:
        {{ $services->total() }}
    </strong>

    <a href="{{ route('app.medical.services.create', $workspace) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        إضافة خدمة
    </a>
</div>

<div class="card content-card mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
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
                <label class="form-label">النوع</label>
                <select name="type" class="form-select">
                    <option value="">كل الأنواع</option>
                    <option value="consultation" @selected(request('type') === 'consultation')>كشف</option>
                    <option value="follow_up" @selected(request('type') === 'follow_up')>متابعة</option>
                    <option value="procedure" @selected(request('type') === 'procedure')>إجراء طبي</option>
                    <option value="lab_test" @selected(request('type') === 'lab_test')>تحليل</option>
                    <option value="scan" @selected(request('type') === 'scan')>أشعة</option>
                    <option value="operation" @selected(request('type') === 'operation')>عملية</option>
                    <option value="session" @selected(request('type') === 'session')>جلسة</option>
                    <option value="package" @selected(request('type') === 'package')>باقة</option>
                    <option value="other" @selected(request('type') === 'other')>أخرى</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">القسم</label>
                <select name="department_id" class="form-select">
                    <option value="">كل الأقسام</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" @selected((string) request('department_id') === (string) $department->id)>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-1 d-grid">
                <button class="btn btn-dark">
                    فلترة
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card content-card">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>الخدمة</th>
                        <th>النوع</th>
                        <th>القسم</th>
                        <th>التخصص</th>
                        <th>السعر</th>
                        <th>المدة</th>
                        <th>الحالة</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($services as $service)
                        <tr>
                            <td>
                                <div class="fw-bold">
                                    {{ $service->name }}
                                </div>

                                @if($service->description)
                                    <div class="small text-muted">
                                        {{ \Illuminate\Support\Str::limit($service->description, 80) }}
                                    </div>
                                @endif

                                @if($service->is_featured)
                                    <span class="badge bg-warning text-dark mt-1">
                                        مميزة
                                    </span>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $service->typeLabel() }}
                                </span>
                            </td>

                            <td>
                                {{ $service->department?->name ?: '-' }}
                            </td>

                            <td>
                                {{ $service->specialty?->name ?: '-' }}
                            </td>

                            <td>
                                @if($service->price !== null)
                                    <strong>
                                        {{ number_format((float) $service->price, 2) }}
                                    </strong>
                                    <small>{{ $service->currency }}</small>
                                @else
                                    <span class="text-muted">غير محدد</span>
                                @endif
                            </td>

                            <td>
                                @if($service->duration_minutes)
                                    {{ $service->duration_minutes }} دقيقة
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                @if($service->is_active)
                                    <span class="badge bg-success">نشطة</span>
                                @else
                                    <span class="badge bg-danger">متوقفة</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('app.medical.services.edit', [$workspace, $service]) }}" class="btn btn-sm btn-outline-primary">
                                        تعديل
                                    </a>

                                    <form method="POST" action="{{ route('app.medical.services.destroy', [$workspace, $service]) }}" onsubmit="return confirm('حذف الخدمة؟')">
                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-sm btn-outline-danger">
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

        {{ $services->links() }}
    </div>
</div>
@endsection