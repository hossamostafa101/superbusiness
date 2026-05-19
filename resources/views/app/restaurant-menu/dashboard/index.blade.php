{{-- resources/views/app/restaurant-menu/dashboard/index.blade.php --}}
@extends('app.layouts.app')

@section('title', 'لوحة المطعم')
@section('page_title', 'لوحة المطعم')
@section('page_description', 'متابعة الطلبات والفواتير المفتوحة والتنبيهات السريعة.')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card content-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small">طلبات جديدة</div>
                        <div class="h3 fw-bold mb-0">{{ $newOrdersCount }}</div>
                    </div>

                    <div class="rounded-4 bg-warning bg-opacity-25 p-3">
                        <i class="bi bi-bell fs-4 text-warning"></i>
                    </div>
                </div>

                <a href="{{ route('app.restaurant-menu.orders.index', ['workspace' => $workspace, 'status' => 'new']) }}"
                   class="btn btn-sm btn-outline-dark mt-3">
                    عرض الطلبات
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card content-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small">فواتير مفتوحة</div>
                        <div class="h3 fw-bold mb-0">{{ $openInvoicesCount }}</div>
                    </div>

                    <div class="rounded-4 bg-success bg-opacity-25 p-3">
                        <i class="bi bi-journal-text fs-4 text-success"></i>
                    </div>
                </div>

                <a href="{{ route('app.restaurant-menu.invoices.index', ['workspace' => $workspace, 'status' => 'open']) }}"
                   class="btn btn-sm btn-outline-success mt-3">
                    عرض الفواتير
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card content-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small">تنتهي خلال 10 دقائق</div>
                        <div class="h3 fw-bold mb-0">{{ $endingSoonInvoicesCount }}</div>
                    </div>

                    <div class="rounded-4 bg-info bg-opacity-25 p-3">
                        <i class="bi bi-hourglass-split fs-4 text-info"></i>
                    </div>
                </div>

                <a href="{{ route('app.restaurant-menu.invoices.index', ['workspace' => $workspace, 'status' => 'open']) }}"
                   class="btn btn-sm btn-outline-info mt-3">
                    متابعة
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card content-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small">طلبات اليوم</div>
                        <div class="h3 fw-bold mb-0">{{ $todayOrdersCount }}</div>
                    </div>

                    <div class="rounded-4 bg-primary bg-opacity-25 p-3">
                        <i class="bi bi-receipt fs-4 text-primary"></i>
                    </div>
                </div>

                <div class="small text-muted mt-3">
                    إجمالي اليوم:
                    <strong>
                        {{ number_format((float) $todaySalesTotal, 2) }}
                    </strong>
                </div>
            </div>
        </div>
    </div>
</div>

@if($expiredOpenInvoicesCount > 0)
    <div class="alert alert-warning rounded-4">
        يوجد
        <strong>{{ $expiredOpenInvoicesCount }}</strong>
        فواتير انتهت مدتها وما زالت حالتها مفتوحة.
        شغّل أمر انتهاء الفواتير أو راجع إعدادات الـ Cron Job.
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card content-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">آخر الطلبات</h5>

                    <a href="{{ route('app.restaurant-menu.orders.index', $workspace) }}" class="btn btn-sm btn-light">
                        الكل
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>الطلب</th>
                                <th>العميل</th>
                                <th>الحالة</th>
                                <th>الإجمالي</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($latestOrders as $order)
                                <tr>
                                    <td>
                                        <strong>#{{ $order->order_number }}</strong>
                                        <div class="small text-muted">
                                            {{ $order->created_at?->format('H:i') }}
                                        </div>
                                    </td>

                                    <td>
                                        {{ $order->customer_name ?: 'عميل' }}
                                    </td>

                                    <td>
                                        <span class="badge {{ $order->statusBadgeClass() }}">
                                            {{ $order->statusLabel() }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ number_format((float) $order->total, 2) }}
                                        {{ $order->currency }}
                                    </td>

                                    <td class="text-end">
                                        <a href="{{ route('app.restaurant-menu.orders.show', [$workspace, $order]) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            عرض
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        لا توجد طلبات بعد.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card content-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">آخر الفواتير المفتوحة</h5>

                    <a href="{{ route('app.restaurant-menu.invoices.index', $workspace) }}" class="btn btn-sm btn-light">
                        الكل
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>الفاتورة</th>
                                <th>الطاولة</th>
                                <th>تنتهي</th>
                                <th>الإجمالي</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($latestOpenInvoices as $invoice)
                                <tr>
                                    <td>
                                        <strong>#{{ $invoice->invoice_number }}</strong>
                                        <div class="small text-muted">
                                            {{ $invoice->opened_by_name ?: '-' }}
                                        </div>
                                    </td>

                                    <td>
                                        {{ $invoice->table?->name ?: ($invoice->table_number ?: '-') }}
                                    </td>

                                    <td>
                                        @if($invoice->expires_at)
                                            <div>{{ $invoice->expires_at->format('H:i') }}</div>
                                            <small class="text-muted">
                                                {{ $invoice->expires_at->diffForHumans() }}
                                            </small>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td>
                                        {{ number_format((float) $invoice->total, 2) }}
                                        {{ $invoice->currency }}
                                    </td>

                                    <td class="text-end">
                                        <a href="{{ route('app.restaurant-menu.invoices.show', [$workspace, $invoice]) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            عرض
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        لا توجد فواتير مفتوحة.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection




{{-- @push('scripts')
<script>
    setTimeout(function () {
        window.location.reload();
    }, 30000);
</script>
@endpush --}}