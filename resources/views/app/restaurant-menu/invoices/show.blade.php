{{-- resources/views/app/restaurant-menu/invoices/show.blade.php --}}
@extends('app.layouts.app')

@section('title', 'تفاصيل الجلسة')
@section('page_title', 'تفاصيل الجلسة #' . $restaurantInvoice->invoice_number)
@section('page_description', 'عرض الفاتورة المفتوحة والأصناف والضيوف والطلبات المرتبطة بها.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('app.restaurant-menu.invoices.index', $workspace) }}" class="btn btn-light">
        <i class="bi bi-arrow-right"></i>
        رجوع للفواتير
    </a>

    <div class="d-flex gap-2 flex-wrap">
        @if($restaurantInvoice->status === 'open')
            <form method="POST" action="{{ route('app.restaurant-menu.invoices.extend', [$workspace, $restaurantInvoice]) }}">
                @csrf
                @method('PATCH')

                <input type="hidden" name="minutes" value="{{ $extendMinutesStep }}">

                <button type="submit" class="btn btn-outline-success">
                    تمديد {{ $extendMinutesStep }} دقيقة
                </button>
            </form>
        @endif

        <form method="POST" action="{{ route('app.restaurant-menu.invoices.recalculate', [$workspace, $restaurantInvoice]) }}">
            @csrf
            @method('PATCH')

            <button type="submit" class="btn btn-outline-secondary">
                تحديث الإجمالي
            </button>
        </form>

        <form method="POST" action="{{ route('app.restaurant-menu.invoices.update-status', [$workspace, $restaurantInvoice]) }}">
            @csrf
            @method('PATCH')

            <select
                name="status"
                class="form-select"
                onchange="this.form.submit()"
                style="min-width: 150px;"
            >
                <option value="open" @selected($restaurantInvoice->status === 'open')>مفتوحة</option>
                <option value="closed" @selected($restaurantInvoice->status === 'closed')>مغلقة</option>
                <option value="expired" @selected($restaurantInvoice->status === 'expired')>منتهية</option>
                <option value="cancelled" @selected($restaurantInvoice->status === 'cancelled')>ملغية</option>
            </select>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card content-card mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                    <div>
                        <h5 class="fw-bold mb-1">
                            #{{ $restaurantInvoice->invoice_number }}
                        </h5>

                        <div class="text-muted small">
                            الفرع:
                            {{ $restaurantInvoice->branch?->name ?: '-' }}
                            —
                            الطاولة:
                            {{ $restaurantInvoice->table?->name ?: ($restaurantInvoice->table_number ?: '-') }}
                        </div>
                    </div>

                    @include('app.restaurant-menu.invoices.partials.status-badge', [
                        'invoice' => $restaurantInvoice,
                    ])
                </div>

                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="border rounded-4 p-3">
                            <small class="text-muted d-block">الأصناف</small>
                            <strong>{{ $restaurantInvoice->items->count() }}</strong>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="border rounded-4 p-3">
                            <small class="text-muted d-block">الضيوف</small>
                            <strong>{{ $restaurantInvoice->guests->count() }}</strong>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="border rounded-4 p-3">
                            <small class="text-muted d-block">الطلبات</small>
                            <strong>{{ $restaurantInvoice->orders->count() }}</strong>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="border rounded-4 p-3">
                            <small class="text-muted d-block">الإجمالي</small>
                            <strong>
                                {{ number_format((float) $restaurantInvoice->total, 2) }}
                                {{ $restaurantInvoice->currency }}
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card content-card mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">أصناف الفاتورة</h6>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>الصنف</th>
                                <th>الضيف</th>
                                <th>الكمية</th>
                                <th>السعر</th>
                                <th>الإضافات</th>
                                <th>الحالة</th>
                                <th class="text-end">الإجمالي</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($restaurantInvoice->items as $invoiceItem)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $invoiceItem->item_name }}

                                            @if($invoiceItem->variant_name)
                                                <span class="text-muted">
                                                    - {{ $invoiceItem->variant_name }}
                                                </span>
                                            @endif
                                        </div>

                                        @if($invoiceItem->notes)
                                            <div class="small mt-1">
                                                <strong>ملاحظة:</strong>
                                                {{ $invoiceItem->notes }}
                                            </div>
                                        @endif

                                        @if($invoiceItem->order_id)
                                            <div class="small mt-1">
                                                <a href="{{ route('app.restaurant-menu.orders.show', [$workspace, $invoiceItem->order_id]) }}" class="text-decoration-none">
                                                    الطلب المرتبط
                                                </a>
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        @if($invoiceItem->guest)
                                            <div class="small">
                                                {{ $invoiceItem->guest->customer_name ?: 'ضيف' }}
                                            </div>

                                            @if($invoiceItem->guest->is_owner)
                                                <span class="badge bg-primary">صاحب الفاتورة</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $invoiceItem->quantity }}
                                    </td>

                                    <td>
                                        {{ number_format((float) $invoiceItem->unit_price, 2) }}
                                        {{ $invoiceItem->currency }}
                                    </td>

                                    <td>
                                        @if($invoiceItem->options->count())
                                            <div class="small">
                                                @foreach($invoiceItem->options as $option)
                                                    <div>
                                                        <span class="text-muted">
                                                            {{ $option->group_name }}:
                                                        </span>

                                                        {{ $option->option_name }}

                                                        @if((float) $option->price > 0)
                                                            <span class="text-muted">
                                                                (+{{ number_format((float) $option->price, 2) }} {{ $option->currency }})
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <td>
                                        @php
                                            $itemStatusClass = match ($invoiceItem->status) {
                                                'new' => 'bg-warning text-dark',
                                                'accepted' => 'bg-primary',
                                                'preparing' => 'bg-info text-dark',
                                                'ready' => 'bg-success',
                                                'served' => 'bg-dark',
                                                'cancelled' => 'bg-danger',
                                                default => 'bg-secondary',
                                            };

                                            $itemStatusLabel = match ($invoiceItem->status) {
                                                'new' => 'جديد',
                                                'accepted' => 'مقبول',
                                                'preparing' => 'قيد التحضير',
                                                'ready' => 'جاهز',
                                                'served' => 'تم التقديم',
                                                'cancelled' => 'ملغي',
                                                default => $invoiceItem->status,
                                            };
                                        @endphp

                                        <span class="badge {{ $itemStatusClass }}">
                                            {{ $itemStatusLabel }}
                                        </span>
                                    </td>

                                    <td class="text-end">
                                        <strong>
                                            {{ number_format((float) $invoiceItem->line_total, 2) }}
                                            {{ $invoiceItem->currency }}
                                        </strong>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        لا توجد أصناف في هذه الفاتورة بعد.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card content-card">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">الطلبات المرتبطة</h6>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>رقم الطلب</th>
                                <th>العميل</th>
                                <th>الحالة</th>
                                <th>الإجمالي</th>
                                <th>الوقت</th>
                                <th class="text-end">عرض</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($restaurantInvoice->orders as $order)
                                <tr>
                                    <td>
                                        #{{ $order->order_number }}
                                    </td>

                                    <td>
                                        {{ $order->customer_name ?: '-' }}
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

                                    <td>
                                        {{ $order->created_at?->format('Y-m-d H:i') }}
                                    </td>

                                    <td class="text-end">
                                        <a
                                            href="{{ route('app.restaurant-menu.orders.show', [$workspace, $order]) }}"
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            عرض
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        لا توجد طلبات مرتبطة بهذه الفاتورة.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card content-card mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">بيانات الفاتورة</h6>

                <div class="mb-3">
                    <small class="text-muted d-block">صاحب الفاتورة</small>
                    <strong>{{ $restaurantInvoice->opened_by_name ?: '-' }}</strong>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">الهاتف</small>

                    @if($restaurantInvoice->opened_by_phone)
                        <a href="tel:{{ $restaurantInvoice->opened_by_phone }}" class="text-decoration-none">
                            {{ $restaurantInvoice->opened_by_phone }}
                        </a>
                    @else
                        <strong>-</strong>
                    @endif
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">PIN Hint</small>
                    <strong dir="ltr">{{ $restaurantInvoice->pin_hint ?: '-' }}</strong>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">فتحت في</small>
                    <strong>{{ $restaurantInvoice->opened_at?->format('Y-m-d H:i') ?: '-' }}</strong>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">تنتهي في</small>
                    <strong>{{ $restaurantInvoice->expires_at?->format('Y-m-d H:i') ?: '-' }}</strong>
                </div>

                <div class="mb-0">
                    <small class="text-muted d-block">آخر نشاط</small>
                    <strong>{{ $restaurantInvoice->last_activity_at?->format('Y-m-d H:i') ?: '-' }}</strong>
                </div>
            </div>
        </div>

        <div class="card content-card mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">الضيوف</h6>

                @forelse($restaurantInvoice->guests as $guest)
                    <div class="border rounded-4 p-3 mb-2">
                        <div class="fw-semibold">
                            {{ $guest->customer_name ?: 'ضيف' }}

                            @if($guest->is_owner)
                                <span class="badge bg-primary">صاحب</span>
                            @endif
                        </div>

                        @if($guest->customer_phone)
                            <div class="small text-muted">
                                {{ $guest->customer_phone }}
                            </div>
                        @endif

                        <div class="small text-muted mt-1">
                            انضم:
                            {{ $guest->joined_at?->format('H:i') ?: '-' }}
                            —
                            آخر ظهور:
                            {{ $guest->last_seen_at?->format('H:i') ?: '-' }}
                        </div>
                    </div>
                @empty
                    <div class="text-muted">
                        لا يوجد ضيوف.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="card content-card">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">ملخص الحساب</h6>

                <div class="d-flex justify-content-between mb-2">
                    <span>المجموع الفرعي</span>
                    <strong>
                        {{ number_format((float) $restaurantInvoice->subtotal, 2) }}
                        {{ $restaurantInvoice->currency }}
                    </strong>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>الخصم</span>
                    <strong>
                        {{ number_format((float) $restaurantInvoice->discount_total, 2) }}
                        {{ $restaurantInvoice->currency }}
                    </strong>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>الخدمة / التوصيل</span>
                    <strong>
                        {{ number_format((float) $restaurantInvoice->delivery_fee, 2) }}
                        {{ $restaurantInvoice->currency }}
                    </strong>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>الضريبة</span>
                    <strong>
                        {{ number_format((float) $restaurantInvoice->tax_total, 2) }}
                        {{ $restaurantInvoice->currency }}
                    </strong>
                </div>

                <hr>

                <div class="d-flex justify-content-between h5 mb-0">
                    <span>الإجمالي</span>
                    <strong>
                        {{ number_format((float) $restaurantInvoice->total, 2) }}
                        {{ $restaurantInvoice->currency }}
                    </strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection