{{-- resources/views/app/restaurant-menu/orders/show.blade.php --}}
@extends('app.layouts.app')

@section('title', 'تفاصيل الطلب')
@section('page_title', 'تفاصيل الطلب #' . $restaurantOrder->order_number)
@section('page_description', 'عرض تفاصيل الطلب وتحديث حالته والتواصل مع العميل.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('app.restaurant-menu.orders.index', $workspace) }}" class="btn btn-light">
        <i class="bi bi-arrow-right"></i>
        رجوع للطلبات
    </a>

    <div class="d-flex gap-2 flex-wrap">
        @include('app.restaurant-menu.orders.partials.whatsapp-actions', [
            'order' => $restaurantOrder,
        ])

        <form method="POST" action="{{ route('app.restaurant-menu.orders.update-status', [$workspace, $restaurantOrder]) }}">
            @csrf
            @method('PATCH')

            <select
                name="status"
                class="form-select"
                onchange="this.form.submit()"
                style="min-width: 170px;"
            >
                <option value="new" @selected($restaurantOrder->status === 'new')>جديد</option>
                <option value="accepted" @selected($restaurantOrder->status === 'accepted')>مقبول</option>
                <option value="preparing" @selected($restaurantOrder->status === 'preparing')>قيد التحضير</option>
                <option value="ready" @selected($restaurantOrder->status === 'ready')>جاهز</option>
                <option value="completed" @selected($restaurantOrder->status === 'completed')>مكتمل</option>
                <option value="cancelled" @selected($restaurantOrder->status === 'cancelled')>ملغي</option>
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
                            الطلب #{{ $restaurantOrder->order_number }}
                        </h5>

                        <div class="text-muted small">
                            {{ $restaurantOrder->created_at?->format('Y-m-d H:i') }}
                        </div>
                    </div>

                    @include('app.restaurant-menu.orders.partials.status-badge', [
                        'order' => $restaurantOrder,
                    ])
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>الصنف</th>
                                <th>الكمية</th>
                                <th>السعر</th>
                                <th>الإضافات</th>
                                <th class="text-end">الإجمالي</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($restaurantOrder->items as $orderItem)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $orderItem->item_name }}

                                            @if($orderItem->variant_name)
                                                <span class="text-muted">
                                                    - {{ $orderItem->variant_name }}
                                                </span>
                                            @endif
                                        </div>

                                        @if($orderItem->notes)
                                            <div class="small mt-1">
                                                <strong>ملاحظة:</strong>
                                                {{ $orderItem->notes }}
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $orderItem->quantity }}
                                    </td>

                                    <td>
                                        {{ number_format((float) $orderItem->unit_price, 2) }}
                                        {{ $orderItem->currency }}
                                    </td>

                                    <td>
                                        @if($orderItem->options->count())
                                            <div class="small">
                                                @foreach($orderItem->options as $option)
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

                                    <td class="text-end">
                                        <strong>
                                            {{ number_format((float) $orderItem->line_total, 2) }}
                                            {{ $orderItem->currency }}
                                        </strong>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($restaurantOrder->notes)
                    <div class="alert alert-light border mt-3 mb-0">
                        <strong>ملاحظات الطلب:</strong>
                        <div class="mt-1">
                            {{ $restaurantOrder->notes }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="card content-card">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">ملخص الحساب</h6>

                <div class="d-flex justify-content-between mb-2">
                    <span>المجموع الفرعي</span>
                    <strong>
                        {{ number_format((float) $restaurantOrder->subtotal, 2) }}
                        {{ $restaurantOrder->currency }}
                    </strong>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>الخصم</span>
                    <strong>
                        {{ number_format((float) $restaurantOrder->discount_total, 2) }}
                        {{ $restaurantOrder->currency }}
                    </strong>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>التوصيل</span>
                    <strong>
                        {{ number_format((float) $restaurantOrder->delivery_fee, 2) }}
                        {{ $restaurantOrder->currency }}
                    </strong>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>الضريبة</span>
                    <strong>
                        {{ number_format((float) $restaurantOrder->tax_total, 2) }}
                        {{ $restaurantOrder->currency }}
                    </strong>
                </div>

                <hr>

                <div class="d-flex justify-content-between h5 mb-0">
                    <span>الإجمالي</span>
                    <strong>
                        {{ number_format((float) $restaurantOrder->total, 2) }}
                        {{ $restaurantOrder->currency }}
                    </strong>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card content-card mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">بيانات العميل</h6>

                <div class="mb-3">
                    <small class="text-muted d-block">الاسم</small>
                    <strong>{{ $restaurantOrder->customer_name ?: '-' }}</strong>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">الهاتف</small>

                    @if($restaurantOrder->customer_phone)
                        <a href="tel:{{ $restaurantOrder->customer_phone }}" class="text-decoration-none">
                            {{ $restaurantOrder->customer_phone }}
                        </a>
                    @else
                        <strong>-</strong>
                    @endif
                </div>

                <div class="mb-0">
                    <small class="text-muted d-block">البريد</small>

                    @if($restaurantOrder->customer_email)
                        <a href="mailto:{{ $restaurantOrder->customer_email }}" class="text-decoration-none">
                            {{ $restaurantOrder->customer_email }}
                        </a>
                    @else
                        <strong>-</strong>
                    @endif
                </div>
            </div>
        </div>

        <div class="card content-card mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">بيانات الطلب</h6>

                <div class="mb-3">
                    <small class="text-muted d-block">الفرع</small>
                    <strong>{{ $restaurantOrder->branch?->name ?: '-' }}</strong>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">نوع الطلب</small>
                    <span class="badge {{ $restaurantOrder->orderTypeBadgeClass() }}">
                        {{ $restaurantOrder->orderTypeLabel() }}
                    </span>
                </div>

                @if($restaurantOrder->table_number)
                    <div class="mb-3">
                        <small class="text-muted d-block">رقم الطاولة</small>
                        <strong>{{ $restaurantOrder->table_number }}</strong>
                    </div>
                @endif


                @if($restaurantOrder->invoice_id)
    <div class="mb-3">
        <small class="text-muted d-block">الفاتورة المفتوحة</small>
        <a
            href="{{ route('app.restaurant-menu.invoices.show', [$workspace, $restaurantOrder->invoice_id]) }}"
            class="text-decoration-none fw-bold"
        >
            عرض الفاتورة
        </a>
    </div>
@endif




                @if($restaurantOrder->delivery_address)
                    <div class="mb-3">
                        <small class="text-muted d-block">عنوان التوصيل</small>
                        <div>{{ $restaurantOrder->delivery_address }}</div>
                    </div>
                @endif

                <div class="mb-3">
                    <small class="text-muted d-block">الدفع</small>
                    <strong>{{ $restaurantOrder->payment_status }}</strong>

                    @if($restaurantOrder->payment_method)
                        <div class="small text-muted">
                            {{ $restaurantOrder->payment_method }}
                        </div>
                    @endif
                </div>

                <div class="mb-0">
                    <small class="text-muted d-block">المصدر</small>
                    <strong>{{ $restaurantOrder->source }}</strong>
                </div>
            </div>
        </div>

        <div class="card content-card">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">توقيتات</h6>

                <div class="mb-2">
                    <small class="text-muted d-block">تم الإنشاء</small>
                    <strong>{{ $restaurantOrder->created_at?->format('Y-m-d H:i') }}</strong>
                </div>

                <div class="mb-2">
                    <small class="text-muted d-block">تم القبول</small>
                    <strong>{{ $restaurantOrder->accepted_at?->format('Y-m-d H:i') ?: '-' }}</strong>
                </div>

                <div class="mb-2">
                    <small class="text-muted d-block">تم الإكمال</small>
                    <strong>{{ $restaurantOrder->completed_at?->format('Y-m-d H:i') ?: '-' }}</strong>
                </div>

                <div class="mb-0">
                    <small class="text-muted d-block">تم الإلغاء</small>
                    <strong>{{ $restaurantOrder->cancelled_at?->format('Y-m-d H:i') ?: '-' }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection