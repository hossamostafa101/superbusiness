{{-- resources/views/app/restaurant-menu/branches/index.blade.php --}}
@extends('app.layouts.app')

@section('title', 'فروع المطعم')
@section('page_title', 'فروع المطعم')
@section('page_description', 'إدارة فروع المطعم أو الكافيه وربط كل فرع بمنيو مستقل.')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <strong>
                عدد الفروع:
                {{ $branches->total() }}
                /
                {{ $isUnlimited ? 'غير محدود' : $branchesLimit }}
            </strong>
        </div>

        <a href="{{ route('app.restaurant-menu.branches.create', $workspace) }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            إضافة فرع
        </a>
    </div>

    <div class="card content-card mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('app.restaurant-menu.branches.index', $workspace) }}"
                class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label">بحث</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="اسم الفرع، الرابط، الهاتف">
                </div>

                <div class="col-md-3">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-select">
                        <option value="">كل الحالات</option>
                        <option value="active" @selected(request('status') === 'active')>نشط</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>غير نشط</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-dark">
                        بحث
                    </button>

                    <a href="{{ route('app.restaurant-menu.branches.index', $workspace) }}"
                        class="btn btn-outline-secondary">
                        إعادة ضبط
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
                            <th>الفرع</th>
                            <th>التواصل</th>
                            <th>المنيو</th>
                            <th>الترتيب</th>
                            <th>الحالة</th>
                            <th>الرابط العام</th>
                            <th>QR المنيو</th>
                            <th class="text-end">الإجراءات</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($branches as $branch)
                            <tr>
                                <td>{{ $branch->id }}</td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $branch->name }}

                                        @if ($branch->is_default)
                                            <span class="badge bg-primary ms-1">افتراضي</span>
                                        @endif
                                    </div>

                                    <small class="text-muted">
                                        {{ $branch->slug }}
                                    </small>

                                    @if ($branch->address)
                                        <div class="small text-muted mt-1">
                                            <i class="bi bi-geo-alt"></i>
                                            {{ \Illuminate\Support\Str::limit($branch->address, 70) }}
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    @if ($branch->phone)
                                        <div>
                                            <i class="bi bi-telephone text-muted"></i>
                                            <a href="tel:{{ $branch->phone }}" class="text-decoration-none">
                                                {{ $branch->phone }}
                                            </a>
                                        </div>
                                    @endif

                                    @if ($branch->whatsapp_number)
                                        @php
                                            $wa = preg_replace('/\D+/', '', $branch->whatsapp_number);
                                        @endphp

                                        <div>
                                            <i class="bi bi-whatsapp text-success"></i>
                                            <a href="https://wa.me/{{ $wa }}" target="_blank"
                                                class="text-decoration-none">
                                                {{ $branch->whatsapp_number }}
                                            </a>
                                        </div>
                                    @endif

                                    @if (!$branch->phone && !$branch->whatsapp_number)
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    <div>
                                        <span class="badge bg-secondary">
                                            {{ $branch->categories_count }} تصنيف
                                        </span>
                                    </div>

                                    <div class="mt-1">
                                        <span class="badge bg-info">
                                            {{ $branch->items_count }} صنف
                                        </span>
                                    </div>
                                </td>

                                <td>{{ $branch->sort_order }}</td>

                                <td>
                                    @if ($branch->is_active)
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-danger">غير نشط</span>
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route('public.restaurant-menu.branch', [$workspace, $branch]) }}"
                                        target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-box-arrow-up-left"></i>
                                        عرض
                                    </a>
                                </td>

                                @php
                                    $publicMenuUrl = route('public.restaurant-menu.branch', [$workspace, $branch]);

                                    $qrImage =
                                        'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' .
                                        urlencode($publicMenuUrl);
                                @endphp

                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-dark" data-bs-toggle="modal"
                                        data-bs-target="#branchQrModal{{ $branch->id }}">
                                        <i class="bi bi-qr-code"></i>
                                        QR
                                    </button>

                                    <div class="modal fade" id="branchQrModal{{ $branch->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 rounded-4">
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">
                                                        QR منيو {{ $branch->name }}
                                                    </h5>

                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body text-center">
                                                    <div class="p-3 bg-light rounded-4 d-inline-block">
                                                        <img src="{{ $qrImage }}" alt="QR Menu" width="220"
                                                            height="220">
                                                    </div>

                                                    <div class="mt-3">
                                                        <input type="text" class="form-control text-center"
                                                            dir="ltr" value="{{ $publicMenuUrl }}" readonly
                                                            onclick="this.select()">
                                                    </div>

                                                    <div class="d-flex justify-content-center gap-2 mt-3">
                                                        <a href="{{ $publicMenuUrl }}" target="_blank"
                                                            class="btn btn-outline-primary">
                                                            فتح المنيو
                                                        </a>

                                                        <a href="{{ $qrImage }}"
                                                            download="menu-qr-{{ $branch->slug }}.png"
                                                            class="btn btn-dark">
                                                            تحميل QR
                                                        </a>
                                                    </div>

                                                    <div class="small text-muted mt-3">
                                                        هذا QR عام للمنيو فقط ولا يرتبط بأي طاولة.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('app.restaurant-menu.branches.edit', [$workspace, $branch]) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            تعديل
                                        </a>

                                        <form method="POST"
                                            action="{{ route('app.restaurant-menu.branches.destroy', [$workspace, $branch]) }}"
                                            onsubmit="return confirm('هل أنت متأكد من حذف هذا الفرع؟ سيتم حذف التصنيفات والأصناف المرتبطة به.')">
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
                                    لا توجد فروع بعد.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $branches->links() }}
            </div>
        </div>
    </div>
@endsection
