{{-- resources/views/app/restaurant-menu/orders/index.blade.php --}}
@extends('app.layouts.app')

@section('title', 'طلبات المنيو')
@section('page_title', 'طلبات المنيو')
@section('page_description', 'متابعة الطلبات الواردة من المنيو العام وتحديث حالتها.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <strong>
            عدد الطلبات:
            {{ $orders->total() }}
        </strong>
    </div>

    <a href="{{ route('public.restaurant-menu.workspace', $workspace) }}" target="_blank" class="btn btn-outline-primary">
        <i class="bi bi-box-arrow-up-left"></i>
        عرض المنيو
    </a>
</div>

<div class="card content-card mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('app.restaurant-menu.orders.index', $workspace) }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">بحث</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="رقم الطلب، الاسم، الهاتف"
                >
            </div>

            <div class="col-md-2">
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

            <div class="col-md-2">
                <label class="form-label">الحالة</label>
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    <option value="new" @selected(request('status') === 'new')>جديد</option>
                    <option value="accepted" @selected(request('status') === 'accepted')>مقبول</option>
                    <option value="preparing" @selected(request('status') === 'preparing')>قيد التحضير</option>
                    <option value="ready" @selected(request('status') === 'ready')>جاهز</option>
                    <option value="completed" @selected(request('status') === 'completed')>مكتمل</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>ملغي</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">نوع الطلب</label>
                <select name="order_type" class="form-select">
                    <option value="">كل الأنواع</option>
                    <option value="dine_in" @selected(request('order_type') === 'dine_in')>داخل المكان</option>
                    <option value="takeaway" @selected(request('order_type') === 'takeaway')>تيك أواي</option>
                    <option value="delivery" @selected(request('order_type') === 'delivery')>دليفري</option>
                </select>
            </div>

            <div class="col-md-1">
                <label class="form-label">من</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
            </div>

            <div class="col-md-1">
                <label class="form-label">إلى</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
            </div>

            <div class="col-md-1 d-flex gap-2">
                <button type="submit" class="btn btn-dark">
                    بحث
                </button>
            </div>

            <div class="col-12">
                <a href="{{ route('app.restaurant-menu.orders.index', $workspace) }}" class="btn btn-outline-secondary btn-sm">
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
                        <th>رقم الطلب</th>
                        <th>العميل</th>
                        <th>الفرع</th>
                        <th>النوع</th>
                        <th>الأصناف</th>
                        <th>الإجمالي</th>
                        <th>الحالة</th>
                        <th>الوقت</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('app.restaurant-menu.orders.show', [$workspace, $order]) }}" class="fw-bold text-decoration-none">
                                    #{{ $order->order_number }}
                                </a>
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $order->customer_name ?: 'عميل' }}
                                </div>

                                @if($order->customer_phone)
                                    <small class="text-muted">
                                        {{ $order->customer_phone }}
                                    </small>
                                @endif
                            </td>

                            <td>
                                {{ $order->branch?->name ?: '-' }}
                            </td>

                            <td>
                                <span class="badge {{ $order->orderTypeBadgeClass() }}">
                                    {{ $order->orderTypeLabel() }}
                                </span>

                                @if($order->table_number)
                                    <div class="small text-muted mt-1">
                                        طاولة: {{ $order->table_number }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-secondary">
                                    {{ $order->items_count }}
                                </span>
                            </td>

                            <td>
                                <strong>
                                    {{ number_format((float) $order->total, 2) }}
                                    {{ $order->currency }}
                                </strong>
                            </td>

                            <td>
                                @include('app.restaurant-menu.orders.partials.status-badge', [
                                    'order' => $order,
                                ])
                            </td>

                            <td>
                                <div>{{ $order->created_at?->format('Y-m-d') }}</div>
                                <small class="text-muted">
                                    {{ $order->created_at?->format('H:i') }}
                                </small>
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    @include('app.restaurant-menu.orders.partials.whatsapp-actions', [
                                        'order' => $order,
                                    ])

                                    <form method="POST" action="{{ route('app.restaurant-menu.orders.update-status', [$workspace, $order]) }}">
                                        @csrf
                                        @method('PATCH')

                                        <select
                                            name="status"
                                            class="form-select form-select-sm"
                                            onchange="this.form.submit()"
                                            style="min-width: 145px;"
                                        >
                                            <option value="new" @selected($order->status === 'new')>جديد</option>
                                            <option value="accepted" @selected($order->status === 'accepted')>مقبول</option>
                                            <option value="preparing" @selected($order->status === 'preparing')>قيد التحضير</option>
                                            <option value="ready" @selected($order->status === 'ready')>جاهز</option>
                                            <option value="completed" @selected($order->status === 'completed')>مكتمل</option>
                                            <option value="cancelled" @selected($order->status === 'cancelled')>ملغي</option>
                                        </select>
                                    </form>

                                    <a
                                        href="{{ route('app.restaurant-menu.orders.show', [$workspace, $order]) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        التفاصيل
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                لا توجد طلبات بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection