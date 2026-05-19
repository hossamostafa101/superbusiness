{{-- resources/views/app/restaurant-menu/tables/index.blade.php --}}
@extends('app.layouts.app')

@section('title', 'الطاولات و QR')
@section('page_title', 'الطاولات و QR')
@section('page_description', 'إدارة طاولات الفروع وروابط QR الخاصة بكل طاولة.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <strong>
            عدد الطاولات:
            {{ $tables->total() }}
            /
            {{ $isUnlimited ? 'غير محدود' : $tablesLimit }}
        </strong>
    </div>

    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('app.restaurant-menu.tables.print-all', array_filter([
            'workspace' => $workspace->slug,
            'branch_id' => request('branch_id'),
        ])) }}" target="_blank" class="btn btn-outline-dark">
            <i class="bi bi-printer"></i>
            طباعة QR
        </a>

        <a href="{{ route('app.restaurant-menu.tables.create', $workspace) }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            إضافة طاولة
        </a>
    </div>
</div>

<div class="card content-card mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('app.restaurant-menu.tables.index', $workspace) }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">بحث</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="اسم الطاولة، الرقم، الكود"
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

                <a href="{{ route('app.restaurant-menu.tables.index', $workspace) }}" class="btn btn-outline-secondary">
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
                        <th>الطاولة</th>
                        <th>الفرع</th>
                        <th>QR</th>
                        <th>الطلبات</th>
                        <th>الترتيب</th>
                        <th>الحالة</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($tables as $table)
                       @php
    $publicUrl = null;
    $qrImage = null;

    if ($table->branch) {
        $publicUrl = route('public.restaurant-menu.branch', [
            'workspace' => $workspace,
            'branch' => $table->branch,
        ]) . '?table_code=' . urlencode($table->code);

        $qrImage = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' . urlencode($publicUrl);
    }
@endphp

                        <tr>
                            <td>{{ $table->id }}</td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $table->name }}
                                </div>

                                <small class="text-muted">
                                    رقم الطاولة:
                                    {{ $table->number }}
                                </small>

                                @if($table->seats)
                                    <div class="small text-muted mt-1">
                                        عدد المقاعد:
                                        {{ $table->seats }}
                                    </div>
                                @endif

                                <div class="small text-muted mt-1" dir="ltr">
                                    {{ $table->code }}
                                </div>
                            </td>

                            <td>
                                {{ $table->branch?->name ?: '-' }}
                            </td>

                            <td>
                                @if($qrEnabled && $publicUrl)
                                    <div class="d-flex align-items-center gap-3">
                                        <img
                                            src="{{ $qrImage }}"
                                            alt="QR {{ $table->name }}"
                                            class="rounded border bg-white"
                                            style="width: 82px; height: 82px;"
                                        >

                                        <div>
                                            <a href="{{ $publicUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary mb-1">
                                                فتح الرابط
                                            </a>

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-dark d-block"
                                                onclick="navigator.clipboard.writeText('{{ $publicUrl }}'); alert('تم نسخ الرابط');"
                                            >
                                                نسخ الرابط
                                            </button>
                                        </div>
                                    </div>

                                 @elseif(! $publicUrl)
    <span class="badge bg-danger">الفرع غير موجود</span>
@else
    <span class="badge bg-secondary">QR غير متاح في الباقة</span>
@endif
                            </td>

                            <td>
                                <span class="badge bg-info">
                                    {{ $table->orders_count }}
                                </span>
                            </td>

                            <td>{{ $table->sort_order }}</td>

                            <td>
                                @if($table->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">غير نشط</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    <a
                                        href="{{ route('app.restaurant-menu.tables.edit', [$workspace, $table]) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        تعديل
                                    </a>


                                    <a
    href="{{ route('app.restaurant-menu.tables.print-one', [$workspace, $table]) }}"
    target="_blank"
    class="btn btn-sm btn-outline-dark"
>
    طباعة
</a>


                                    <form
                                        method="POST"
                                        action="{{ route('app.restaurant-menu.tables.regenerate-code', [$workspace, $table]) }}"
                                        onsubmit="return confirm('سيتم تغيير رابط QR الحالي. هل تريد المتابعة؟')"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit" class="btn btn-sm btn-outline-warning">
                                            QR جديد
                                        </button>
                                    </form>

                                    <form
                                        method="POST"
                                        action="{{ route('app.restaurant-menu.tables.destroy', [$workspace, $table]) }}"
                                        onsubmit="return confirm('هل أنت متأكد من حذف هذه الطاولة؟')"
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
                                لا توجد طاولات بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $tables->links() }}
        </div>
    </div>
</div>
@endsection