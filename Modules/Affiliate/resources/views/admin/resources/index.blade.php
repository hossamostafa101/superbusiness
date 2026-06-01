@extends('admin.layouts.app')

@section('title', 'الموارد التسويقية')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 fw-bold mb-0">
        الموارد التسويقية
    </h1>

    <a href="{{ route('admin.affiliate.resources.create') }}" class="btn btn-primary">
        إضافة مورد
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">التخصص</label>
                <select name="specification_id" class="form-select">
                    <option value="">كل التخصصات</option>

                    @foreach($specifications as $specification)
                        <option value="{{ $specification->id }}" @selected((string) request('specification_id') === (string) $specification->id)>
                            {{ $specification->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">النوع</label>
                <select name="type" class="form-select">
                    <option value="">كل الأنواع</option>
                    <option value="text" @selected(request('type') === 'text')>نص</option>
                    <option value="link" @selected(request('type') === 'link')>رابط</option>
                    <option value="video" @selected(request('type') === 'video')>فيديو</option>
                    <option value="image" @selected(request('type') === 'image')>صورة</option>
                    <option value="pdf" @selected(request('type') === 'pdf')>PDF</option>
                    <option value="demo" @selected(request('type') === 'demo')>ديمو</option>
                    <option value="whatsapp_script" @selected(request('type') === 'whatsapp_script')>نص واتساب</option>
                    <option value="other" @selected(request('type') === 'other')>أخرى</option>
                </select>
            </div>

            <div class="col-md-2 d-grid">
                <button class="btn btn-dark">
                    عرض
                </button>
            </div>

            <div class="col-md-2 d-grid">
                <a href="{{ route('admin.affiliate.resources.index') }}" class="btn btn-light">
                    إعادة
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>العنوان</th>
                        <th>التخصص</th>
                        <th>النوع</th>
                        <th>الترتيب</th>
                        <th>الحالة</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($resources as $resource)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $resource->title }}</div>

                                @if($resource->description)
                                    <div class="small text-muted">
                                        {{ \Illuminate\Support\Str::limit($resource->description, 80) }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                {{ $resource->specification?->name ?: 'عام' }}
                            </td>

                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $resource->typeLabel() }}
                                </span>
                            </td>

                            <td>{{ $resource->sort_order }}</td>

                            <td>
                                @if($resource->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-secondary">غير نشط</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.affiliate.resources.edit', $resource) }}" class="btn btn-sm btn-outline-primary">
                                        تعديل
                                    </a>

                                    <form method="POST" action="{{ route('admin.affiliate.resources.destroy', $resource) }}" onsubmit="return confirm('حذف المورد؟')">
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
                            <td colspan="6" class="text-center text-muted py-4">
                                لا توجد موارد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $resources->links() }}
    </div>
</div>
@endsection