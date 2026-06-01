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
            @if(! in_array($restaurantOrder->status, ['completed', 'cancelled'], true))
    <a href="{{ route('app.restaurant-menu.orders.edit', [$workspace, $restaurantOrder]) }}" class="btn btn-outline-primary">
        <i class="bi bi-pencil"></i>
        تعديل الطلب
    </a>
@endif

            @include('app.restaurant-menu.orders.partials.whatsapp-actions', [
                'order' => $restaurantOrder,
            ])

            <form method="POST"
                action="{{ route('app.restaurant-menu.orders.update-status', [$workspace, $restaurantOrder]) }}">
                @csrf
                @method('PATCH')

                <select name="status" class="form-select" onchange="this.form.submit()" style="min-width: 170px;">
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
                                @foreach ($restaurantOrder->items as $orderItem)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">
                                                {{ $orderItem->item_name }}

                                                @if ($orderItem->variant_name)
                                                    <span class="text-muted">
                                                        - {{ $orderItem->variant_name }}
                                                    </span>
                                                @endif
                                            </div>

                                            @if ($orderItem->notes)
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
                                            @if ($orderItem->options->count())
                                                <div class="small">
                                                    @foreach ($orderItem->options as $option)
                                                        <div>
                                                            <span class="text-muted">
                                                                {{ $option->group_name }}:
                                                            </span>

                                                            {{ $option->option_name }}

                                                            @if ((float) $option->price > 0)
                                                                <span class="text-muted">
                                                                    (+{{ number_format((float) $option->price, 2) }}
                                                                    {{ $option->currency }})
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

                    @if ($restaurantOrder->notes)
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

            @if(! in_array($restaurantOrder->status, ['completed', 'cancelled'], true))
    <div class="card content-card mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="h5 fw-bold mb-1">تعديل أصناف الطلب</h2>
                    <p class="text-muted mb-0">
                        يمكنك تعديل الكميات أو حذف صنف بجعل الكمية 0.
                    </p>
                </div>
            </div>

            <form
                method="POST"
                action="{{ route('app.restaurant-menu.orders.update-items', [$workspace, $restaurantOrder]) }}"
            >
                @csrf
                @method('PUT')

                
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>الصنف</th>
                                <th style="width: 130px;">الكمية</th>
                                <th>السعر</th>
                                <th>الإجمالي</th>
                                <th style="width: 260px;">ملاحظات</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($restaurantOrder->items as $orderItem)
                                @php
                                    $lineTotal = (float) ($orderItem->line_total ?? $orderItem->total ?? 0);
                                    $unitPrice = (float) ($orderItem->unit_price ?? 0);
                                @endphp

                                <tr>
                                    <td>
                                        <input
                                            type="hidden"
                                            name="items[{{ $loop->index }}][id]"
                                            value="{{ $orderItem->id }}"
                                        >

                                        <div class="fw-bold">
                                            {{ $orderItem->name
                                                ?? $orderItem->item_name
                                                ?? $orderItem->title
                                                ?? 'صنف'
                                            }}
                                        </div>

                                        @if($orderItem->variant_name ?? false)
                                            <div class="small text-muted">
                                                النوع: {{ $orderItem->variant_name }}
                                            </div>
                                        @endif

                                        @if($orderItem->options && $orderItem->options->count())
                                            <div class="small text-muted">
                                                الإضافات:
                                                {{ $orderItem->options->pluck('name')->join('، ') }}
                                            </div>
                                        @endif

                                        <div class="small text-muted">
                                            اجعل الكمية 0 لحذف هذا السطر.
                                        </div>
                                    </td>

                                    <td>
                                        <input
                                            type="number"
                                            min="0"
                                            max="999"
                                            name="items[{{ $loop->index }}][quantity]"
                                            value="{{ old('items.' . $loop->index . '.quantity', $orderItem->quantity) }}"
                                            class="form-control"
                                        >
                                    </td>

                                    <td>
                                        {{ number_format($unitPrice, 2) }}
                                        {{ $restaurantOrder->currency }}
                                    </td>

                                    <td>
                                        {{ number_format($lineTotal, 2) }}
                                        {{ $restaurantOrder->currency }}
                                    </td>

                                    <td>
                                        <input
                                            type="text"
                                            name="items[{{ $loop->index }}][notes]"
                                            value="{{ old('items.' . $loop->index . '.notes', $orderItem->notes) }}"
                                            class="form-control"
                                            placeholder="ملاحظات السطر"
                                        >
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mb-3">
                    <label class="form-label">سبب تعديل الأصناف</label>
                    <input
                        type="text"
                        name="edit_reason"
                        class="form-control"
                        required
                        placeholder="مثال: العميل طلب تعديل الكمية"
                    >
                </div>

                <div id="orderEditItemsInputs"></div>
                

                <div id="orderEditLines">
    @foreach($restaurantOrder->items as $orderItem)
        @php
            $lineTotal = (float) ($orderItem->line_total ?? $orderItem->total ?? 0);
        @endphp

        <div class="border rounded-4 p-3 mb-2 js-order-edit-line"
             data-line-id="{{ $orderItem->id }}"
             data-item-id="{{ $orderItem->item_id }}"
             data-offer-id="{{ $orderItem->offer_id }}"
             data-quantity="{{ $orderItem->quantity }}"
             data-notes="{{ $orderItem->notes }}"
             data-variant-id="{{ $orderItem->variant_id }}"
             data-options='@json($orderItem->options?->pluck("option_id")->filter()->values() ?? [])'
        >
            <div class="d-flex justify-content-between gap-3">
                <div>
                    <div class="fw-bold">
                        {{ $orderItem->name ?? $orderItem->item_name ?? $orderItem->title ?? 'صنف' }}
                    </div>

                    @if($orderItem->variant_name)
                        <div class="small text-muted">
                            النوع: {{ $orderItem->variant_name }}
                        </div>
                    @endif

                    @if($orderItem->options && $orderItem->options->count())
                        <div class="small text-muted">
                            الإضافات:
                            {{ $orderItem->options->pluck('name')->join('، ') }}
                        </div>
                    @endif

                    @if($orderItem->notes)
                        <div class="small text-muted">
                            ملاحظة: {{ $orderItem->notes }}
                        </div>
                    @endif

                    <div class="small text-muted">
                        الكمية: {{ $orderItem->quantity }}
                        · الإجمالي:
                        {{ number_format($lineTotal, 2) }}
                        {{ $restaurantOrder->currency }}
                    </div>
                </div>

                <div class="d-flex gap-2 align-items-start">
                    @if(empty($orderItem->offer_id))
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-primary js-edit-order-line"
                            data-index="{{ $loop->index }}"
                        >
                            تعديل
                        </button>
                    @endif

                    <button
                        type="button"
                        class="btn btn-sm btn-outline-danger js-remove-order-line"
                        data-index="{{ $loop->index }}"
                    >
                        حذف
                    </button>
                </div>
            </div>
        </div>
    @endforeach
</div>



<div class="mb-3 mt-3">
    <label class="form-label">سبب تعديل الأصناف</label>
    <input
        type="text"
        name="edit_reason"
        class="form-control"
        required
        placeholder="مثال: تعديل الإضافات أو حذف صنف"
    >
</div>

<div class="d-flex justify-content-end">
    <button class="btn btn-primary">
        حفظ تعديل الأصناف
    </button>
</div>
                @if(! in_array($restaurantOrder->status, ['completed', 'cancelled'], true))
    <div class="d-flex justify-content-end mb-3">
        <button
            type="button"
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#addOrderItemModal"
        >
            <i class="bi bi-plus-circle"></i>
            إضافة صنف للطلب
        </button>
    </div>
@endif

                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary">
                        حفظ تعديل الأصناف
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
        </div>

        <div class="col-lg-4">

             @if(! in_array($restaurantOrder->status, ['completed', 'cancelled'], true))
    <div class="card content-card mb-4">
        <div class="card-body p-4">
            <h2 class="h5 fw-bold mb-3">إلغاء الطلب</h2>

            <form
                method="POST"
                action="{{ route('app.restaurant-menu.orders.cancel', [$workspace, $restaurantOrder]) }}"
                onsubmit="return confirm('تأكيد إلغاء الطلب؟')"
            >
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label class="form-label">سبب الإلغاء</label>
                    <input
                        type="text"
                        name="reason"
                        class="form-control"
                        required
                        placeholder="مثال: العميل ألغى الطلب"
                    >
                </div>

                <button class="btn btn-outline-danger">
                    إلغاء الطلب
                </button>
            </form>
        </div>
    </div>
@endif




@if($restaurantOrder->events->count())
    <div class="card content-card mb-4">
        <div class="card-body p-4">
            <h2 class="h5 fw-bold mb-3">سجل الطلب</h2>

            @foreach($restaurantOrder->events->sortByDesc('created_at') as $event)
                <div class="border rounded-4 p-3 mb-2">
                    <div class="d-flex justify-content-between gap-2">
                        <strong>{{ $event->event_type }}</strong>
                        <span class="small text-muted">
                            {{ $event->created_at?->format('Y-m-d H:i') }}
                        </span>
                    </div>

                    @if($event->user)
                        <div class="small text-muted">
                            بواسطة: {{ $event->user->name }}
                        </div>
                    @endif

                    @if($event->reason)
                        <div class="small mt-1">
                            السبب: {{ $event->reason }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif
            <div class="card content-card mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">بيانات العميل</h6>

                    <div class="mb-3">
                        <small class="text-muted d-block">الاسم</small>
                        <strong>{{ $restaurantOrder->customer_name ?: '-' }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">الهاتف</small>

                        @if ($restaurantOrder->customer_phone)
                            <a href="tel:{{ $restaurantOrder->customer_phone }}" class="text-decoration-none">
                                {{ $restaurantOrder->customer_phone }}
                            </a>
                        @else
                            <strong>-</strong>
                        @endif
                    </div>

                    <div class="mb-0">
                        <small class="text-muted d-block">البريد</small>

                        @if ($restaurantOrder->customer_email)
                            <a href="mailto:{{ $restaurantOrder->customer_email }}" class="text-decoration-none">
                                {{ $restaurantOrder->customer_email }}
                            </a>
                        @else
                            <strong>-</strong>
                        @endif
                    </div>
                </div>
            </div>

            @if ($restaurantOrder->isDeliveryOrder())
                <div class="card content-card mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <h2 class="h5 fw-bold mb-1">
                                    بيانات الدليفري
                                </h2>

                                <p class="text-muted mb-0">
                                    تفاصيل منطقة التوصيل والعنوان وحالة الدليفري.
                                </p>
                            </div>

                            <span class="badge bg-dark">
                                {{ $restaurantOrder->deliveryStatusLabel() }}
                            </span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="text-muted small">منطقة التوصيل</div>
                                <div class="fw-bold">
                                    {{ $restaurantOrder->deliveryZone?->name ?? '—' }}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="text-muted small">رسوم التوصيل</div>
                                <div class="fw-bold">
                                    {{ number_format((float) $restaurantOrder->delivery_fee, 2) }}
                                    {{ $restaurantOrder->currency }}
                                </div>

                                <div class="small text-muted">
                                    {{ $restaurantOrder->delivery_fee_included_in_total ? 'داخلة في الإجمالي' : 'غير داخلة في الإجمالي' }}
                                    ·
                                    {{ $restaurantOrder->show_delivery_fee_on_receipt ? 'تظهر في الإيصال' : 'لا تظهر في الإيصال' }}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="text-muted small">مستفيد الرسوم</div>
                                <div class="fw-bold">
                                    {{ $restaurantOrder->deliveryFeeTargetLabel() }}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="text-muted small">الدليفري</div>
                                <div class="fw-bold">
                                    {{ $restaurantOrder->deliveryCourier?->name ?? ($restaurantOrder->delivery_courier_name ?? 'لم يتم التعيين') }}
                                </div>

                                @if ($restaurantOrder->delivery_courier_phone)
                                    <div class="small text-muted" dir="ltr">
                                        {{ $restaurantOrder->delivery_courier_phone }}
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <div class="text-muted small">الشركة</div>
                                <div class="fw-bold">
                                    {{ $restaurantOrder->delivery_company_name ?: '—' }}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="text-muted small">المنطقة / الحي</div>
                                <div class="fw-bold">
                                    {{ $restaurantOrder->delivery_area ?: '—' }}
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="text-muted small">العنوان التفصيلي</div>
                                <div class="fw-bold">
                                    {{ $restaurantOrder->delivery_address_details ?: $restaurantOrder->delivery_address ?: '—' }}
                                </div>
                            </div>

                            @if (
                                $restaurantOrder->delivery_building ||
                                    $restaurantOrder->delivery_floor ||
                                    $restaurantOrder->delivery_apartment ||
                                    $restaurantOrder->delivery_landmark)
                                <div class="col-12">
                                    <div class="d-flex flex-wrap gap-2">
                                        @if ($restaurantOrder->delivery_building)
                                            <span class="badge bg-light text-dark border">
                                                العمارة: {{ $restaurantOrder->delivery_building }}
                                            </span>
                                        @endif

                                        @if ($restaurantOrder->delivery_floor)
                                            <span class="badge bg-light text-dark border">
                                                الدور: {{ $restaurantOrder->delivery_floor }}
                                            </span>
                                        @endif

                                        @if ($restaurantOrder->delivery_apartment)
                                            <span class="badge bg-light text-dark border">
                                                الشقة: {{ $restaurantOrder->delivery_apartment }}
                                            </span>
                                        @endif

                                        @if ($restaurantOrder->delivery_landmark)
                                            <span class="badge bg-light text-dark border">
                                                علامة مميزة: {{ $restaurantOrder->delivery_landmark }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if($restaurantOrder->isDeliveryOrder())
    <div class="card content-card mb-4">
        <div class="card-body p-4">
            <h2 class="h5 fw-bold mb-3">
                تعيين وتحديث الدليفري
            </h2>

            <form
                method="POST"
                action="{{ route('app.restaurant-menu.orders.update-delivery', [$workspace, $restaurantOrder]) }}"
            >
                @csrf
                @method('PATCH')

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">اختيار دليفري مسجل</label>

                        <select name="delivery_courier_id" id="deliveryCourierSelect" class="form-select">
                            <option value="">بدون / إدخال يدوي</option>

                            @foreach($deliveryCouriers as $courier)
                                <option
                                    value="{{ $courier->id }}"
                                    data-name="{{ $courier->name }}"
                                    data-phone="{{ $courier->phone }}"
                                    data-company="{{ $courier->company_name }}"
                                    @selected((int) $restaurantOrder->delivery_courier_id === (int) $courier->id)
                                >
                                    {{ $courier->name }}
                                    @if($courier->type === 'external')
                                        - خارجي
                                    @else
                                        - داخلي
                                    @endif
                                    @if($courier->company_name)
                                        - {{ $courier->company_name }}
                                    @endif
                                </option>
                            @endforeach
                        </select>

                        <div class="form-text">
                            عند اختيار دليفري مسجل سيتم حفظ اسمه ورقمه داخل الطلب.
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">اسم الدليفري / المندوب</label>

                        <input
                            type="text"
                            name="delivery_courier_name"
                            id="deliveryCourierName"
                            value="{{ old('delivery_courier_name', $restaurantOrder->delivery_courier_name) }}"
                            class="form-control @error('delivery_courier_name') is-invalid @enderror"
                        >

                        @error('delivery_courier_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">هاتف الدليفري</label>

                        <input
                            type="text"
                            name="delivery_courier_phone"
                            id="deliveryCourierPhone"
                            value="{{ old('delivery_courier_phone', $restaurantOrder->delivery_courier_phone) }}"
                            class="form-control @error('delivery_courier_phone') is-invalid @enderror"
                            dir="ltr"
                        >

                        @error('delivery_courier_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">شركة التوصيل</label>

                        <input
                            type="text"
                            name="delivery_company_name"
                            id="deliveryCompanyName"
                            value="{{ old('delivery_company_name', $restaurantOrder->delivery_company_name) }}"
                            class="form-control @error('delivery_company_name') is-invalid @enderror"
                        >

                        @error('delivery_company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">حالة الدليفري</label>

                        @php
                            $deliveryStatuses = [
                                'not_assigned' => 'لم يتم التعيين',
                                'assigned' => 'تم التعيين',
                                'picked_up' => 'تم الاستلام من المطعم',
                                'on_the_way' => 'في الطريق',
                                'delivered' => 'تم التسليم',
                                'failed' => 'فشل التوصيل',
                            ];
                        @endphp

                        <select name="delivery_status" class="form-select @error('delivery_status') is-invalid @enderror" required>
                            @foreach($deliveryStatuses as $value => $label)
                                <option value="{{ $value }}" @selected(old('delivery_status', $restaurantOrder->delivery_status) === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        @error('delivery_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 justify-content-end mt-4">
                    <button
                        type="submit"
                        name="delivery_status"
                        value="assigned"
                        class="btn btn-outline-primary"
                    >
                        تم التعيين
                    </button>

                    <button
                        type="submit"
                        name="delivery_status"
                        value="picked_up"
                        class="btn btn-outline-dark"
                    >
                        استلم من المطعم
                    </button>

                    <button
                        type="submit"
                        name="delivery_status"
                        value="on_the_way"
                        class="btn btn-outline-warning"
                    >
                        في الطريق
                    </button>

                    <button
                        type="submit"
                        name="delivery_status"
                        value="delivered"
                        class="btn btn-success"
                    >
                        تم التسليم
                    </button>

                    <button
                        type="submit"
                        name="delivery_status"
                        value="failed"
                        class="btn btn-outline-danger"
                    >
                        فشل التوصيل
                    </button>
                </div>
            </form>
        </div>
    </div>

   
@endif
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

                    @if ($restaurantOrder->table_number)
                        <div class="mb-3">
                            <small class="text-muted d-block">رقم الطاولة</small>
                            <strong>{{ $restaurantOrder->table_number }}</strong>
                        </div>
                    @endif


                    @if ($restaurantOrder->invoice_id)
                        <div class="mb-3">
                            <small class="text-muted d-block">الفاتورة المفتوحة</small>
                            <a href="{{ route('app.restaurant-menu.invoices.show', [$workspace, $restaurantOrder->invoice_id]) }}"
                                class="text-decoration-none fw-bold">
                                عرض الفاتورة
                            </a>
                        </div>
                    @endif




                    @if ($restaurantOrder->delivery_address)
                        <div class="mb-3">
                            <small class="text-muted d-block">عنوان التوصيل</small>
                            <div>{{ $restaurantOrder->delivery_address }}</div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <small class="text-muted d-block">الدفع</small>
                        <strong>{{ $restaurantOrder->payment_status }}</strong>

                        @if ($restaurantOrder->payment_method)
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









    @if(! in_array($restaurantOrder->status, ['completed', 'cancelled'], true))
    <div class="modal fade" id="addOrderItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title fw-bold">إضافة صنف إلى الطلب</h5>
                        <div class="small text-muted">
                            اختر الصنف والحجم والإضافات والكمية.
                        </div>
                    </div>

                    <button type="button" class="btn-close ms-0" data-bs-dismiss="modal"></button>
                </div>

                <form
                    method="POST"
                    action="{{ route('app.restaurant-menu.orders.items.store', [$workspace, $restaurantOrder]) }}"
                    id="addOrderItemForm"
                >
                    @csrf

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">الصنف</label>

                            <select name="item_id" id="orderAddItemSelect" class="form-select" required>
                                <option value="">اختر الصنف</option>

                                @foreach($menuItems as $menuItem)
                                    <option value="{{ $menuItem->id }}">
                                        {{ $menuItem->name }}
                                        @if($menuItem->category)
                                            - {{ $menuItem->category->name }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="orderAddItemInfo" class="alert alert-light border rounded-4" style="display:none;"></div>

                        <div id="orderAddVariantWrap" class="mb-4" style="display:none;">
                            <label class="form-label fw-bold">الحجم / النوع</label>
                            <div id="orderAddVariantList" class="d-grid gap-2"></div>
                        </div>

                        <div id="orderAddOptionsWrap" class="mb-4" style="display:none;">
                            <label class="form-label fw-bold">الإضافات</label>
                            <div id="orderAddOptionsList" class="d-grid gap-3"></div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">الكمية</label>
                                <input
                                    type="number"
                                    name="quantity"
                                    id="orderAddQty"
                                    value="1"
                                    min="1"
                                    max="999"
                                    class="form-control"
                                    required
                                >
                            </div>

                            <div class="col-md-8">
                                <label class="form-label fw-bold">ملاحظات السطر</label>
                                <input
                                    type="text"
                                    name="notes"
                                    id="orderAddNotes"
                                    class="form-control"
                                    placeholder="مثال: بدون صوص / زيادة جبنة"
                                >
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">سبب الإضافة</label>
                                <input
                                    type="text"
                                    name="edit_reason"
                                    class="form-control"
                                    required
                                    placeholder="مثال: العميل طلب إضافة صنف"
                                >
                            </div>
                        </div>

                        <div class="mt-3 border rounded-4 p-3 bg-light">
                            <div class="d-flex justify-content-between">
                                <span>إجمالي السطر المتوقع</span>
                                <strong id="orderAddLineTotalPreview">
                                    0.00 {{ $restaurantOrder->currency }}
                                </strong>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            إلغاء
                        </button>

                        <button class="btn btn-primary">
                            إضافة الصنف
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif


<div class="modal fade" id="editOrderLineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-bold" id="editOrderLineTitle">تعديل الصنف</h5>
                    <div class="small text-muted" id="editOrderLineDesc"></div>
                </div>

                <button type="button" class="btn-close ms-0" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="editOrderLineIndex">

                <div id="editOrderVariantWrap" class="mb-4" style="display:none;">
                    <label class="form-label fw-bold">الحجم / النوع</label>
                    <div id="editOrderVariantList" class="d-grid gap-2"></div>
                </div>

                <div id="editOrderOptionsWrap" class="mb-4" style="display:none;">
                    <label class="form-label fw-bold">الإضافات</label>
                    <div id="editOrderOptionsList" class="d-grid gap-3"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">الكمية</label>
                    <input type="number" id="editOrderLineQty" class="form-control" min="1" max="999">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">ملاحظات السطر</label>
                    <input type="text" id="editOrderLineNotes" class="form-control">
                </div>

                <div class="border rounded-4 p-3 bg-light">
                    <div class="d-flex justify-content-between">
                        <span>إجمالي السطر المتوقع</span>
                        <strong id="editOrderLineTotalPreview">0.00</strong>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    إلغاء
                </button>

                <button type="button" class="btn btn-primary" id="saveEditOrderLineBtn">
                    حفظ السطر
                </button>
            </div>
        </div>
    </div>
</div>


@if(! in_array($restaurantOrder->status, ['completed', 'cancelled'], true))
    <script>
        window.orderMenuItems = @json($menuItemsPayload ?? []);

        document.addEventListener('DOMContentLoaded', function () {
            const itemsById = {};

            (window.orderMenuItems || []).forEach(function (item) {
                itemsById[String(item.id)] = item;
            });

            const itemSelect = document.getElementById('orderAddItemSelect');
            const infoBox = document.getElementById('orderAddItemInfo');

            const variantWrap = document.getElementById('orderAddVariantWrap');
            const variantList = document.getElementById('orderAddVariantList');

            const optionsWrap = document.getElementById('orderAddOptionsWrap');
            const optionsList = document.getElementById('orderAddOptionsList');

            const qtyInput = document.getElementById('orderAddQty');
            const preview = document.getElementById('orderAddLineTotalPreview');

            const currency = @json($restaurantOrder->currency ?? 'EGP');

            function escapeHtml(value) {
                return String(value || '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            function money(value) {
                return Number(value || 0).toFixed(2) + ' ' + currency;
            }

            function selectedItem() {
                if (!itemSelect.value) {
                    return null;
                }

                return itemsById[String(itemSelect.value)] || null;
            }

            function selectedVariant(item) {
                const input = document.querySelector('input[name="variant_id"]:checked');

                if (!input || !item) {
                    return null;
                }

                return (item.variants || []).find(function (variant) {
                    return String(variant.id) === String(input.value);
                }) || null;
            }

            function selectedOptions(item) {
                if (!item) {
                    return [];
                }

                const selected = [];

                (item.option_groups || []).forEach(function (group) {
                    const inputs = document.querySelectorAll('[data-order-option-group="' + group.id + '"]:checked');

                    inputs.forEach(function (input) {
                        const option = (group.options || []).find(function (option) {
                            return String(option.id) === String(input.value);
                        });

                        if (option) {
                            selected.push(option);
                        }
                    });
                });

                return selected;
            }

            function unitPrice(item) {
                if (!item) {
                    return 0;
                }

                const variant = selectedVariant(item);
                const options = selectedOptions(item);

                let price = variant ? Number(variant.price || 0) : Number(item.price || 0);

                options.forEach(function (option) {
                    price += Number(option.price || 0);
                });

                return price;
            }

            function updatePreview() {
                const item = selectedItem();
                const qty = Number(qtyInput.value || 1);

                const total = unitPrice(item) * qty;

                preview.textContent = money(total);
            }

            function renderItemChoices() {
                const item = selectedItem();

                if (!item) {
                    infoBox.style.display = 'none';
                    infoBox.innerHTML = '';

                    variantWrap.style.display = 'none';
                    variantList.innerHTML = '';

                    optionsWrap.style.display = 'none';
                    optionsList.innerHTML = '';

                    updatePreview();
                    return;
                }

                infoBox.style.display = '';
                infoBox.innerHTML = `
                    <div class="fw-bold">${escapeHtml(item.name)}</div>
                    ${item.category_name ? `<div class="small text-muted">${escapeHtml(item.category_name)}</div>` : ''}
                    ${item.description ? `<div class="small text-muted mt-1">${escapeHtml(item.description)}</div>` : ''}
                    <div class="small text-muted mt-1">السعر الأساسي: ${money(item.price)}</div>
                `;

                renderVariants(item);
                renderOptions(item);
                updatePreview();
            }

            function renderVariants(item) {
                const variants = item.variants || [];

                if (!variants.length) {
                    variantWrap.style.display = 'none';
                    variantList.innerHTML = '';
                    return;
                }

                variantWrap.style.display = '';

                const defaultVariant = variants.find(function (variant) {
                    return variant.is_default;
                }) || variants[0];

                variantList.innerHTML = variants.map(function (variant) {
                    return `
                        <label class="border rounded-4 p-3 d-flex justify-content-between align-items-center">
                            <span>
                                <input
                                    type="radio"
                                    name="variant_id"
                                    value="${variant.id}"
                                    ${String(defaultVariant.id) === String(variant.id) ? 'checked' : ''}
                                >
                                <strong class="ms-2">${escapeHtml(variant.name)}</strong>
                            </span>

                            <span class="fw-bold">${money(variant.price)}</span>
                        </label>
                    `;
                }).join('');

                variantList.querySelectorAll('input[name="variant_id"]').forEach(function (input) {
                    input.addEventListener('change', updatePreview);
                });
            }

            function renderOptions(item) {
                const groups = item.option_groups || [];

                if (!groups.length) {
                    optionsWrap.style.display = 'none';
                    optionsList.innerHTML = '';
                    return;
                }

                optionsWrap.style.display = '';

                optionsList.innerHTML = groups.map(function (group) {
                    const type = group.type === 'single' ? 'radio' : 'checkbox';
                    const name = 'order_option_group_' + group.id;

                    const optionsHtml = (group.options || []).map(function (option) {
                        return `
                            <label class="border rounded-4 p-3 d-flex justify-content-between align-items-center mb-2">
                                <span>
                                    <input
                                        type="${type}"
                                        name="${name}${type === 'checkbox' ? '[]' : ''}"
                                        value="${option.id}"
                                        data-order-option-group="${group.id}"
                                    >
                                    <strong class="ms-2">${escapeHtml(option.name)}</strong>
                                </span>

                                <span class="fw-bold">
                                    ${Number(option.price || 0) > 0 ? '+' + money(option.price) : ''}
                                </span>
                            </label>
                        `;
                    }).join('');

                    return `
                        <div class="border rounded-4 p-3">
                            <div class="fw-bold mb-2">
                                ${escapeHtml(group.name)}
                                ${group.is_required ? '<span class="text-danger">*</span>' : ''}
                            </div>

                            ${optionsHtml}
                        </div>
                    `;
                }).join('');

                optionsList.querySelectorAll('input').forEach(function (input) {
                    input.addEventListener('change', updatePreview);
                });
            }

            function validateRequiredGroups() {
                const item = selectedItem();

                if (!item) {
                    alert('اختر الصنف أولًا.');
                    return false;
                }

                for (const group of (item.option_groups || [])) {
                    if (!group.is_required) {
                        continue;
                    }

                    const checked = document.querySelectorAll('[data-order-option-group="' + group.id + '"]:checked');

                    if (!checked.length) {
                        alert('اختر من مجموعة: ' + group.name);
                        return false;
                    }
                }

                return true;
            }

            document.getElementById('addOrderItemForm')?.addEventListener('submit', function (event) {
                if (!validateRequiredGroups()) {
                    event.preventDefault();
                    return;
                }

                /*
                 * نحول الاختيارات إلى inputs باسم options[]
                 * لأن أسماء radio/checkbox الحالية خاصة بالواجهة فقط.
                 */
                document.querySelectorAll('.js-generated-option-input').forEach(function (input) {
                    input.remove();
                });

                const item = selectedItem();
                const options = selectedOptions(item);

                options.forEach(function (option) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'options[]';
                    input.value = option.id;
                    input.className = 'js-generated-option-input';

                    event.target.appendChild(input);
                });
            });

            itemSelect?.addEventListener('change', renderItemChoices);
            qtyInput?.addEventListener('input', updatePreview);
        });
    </script>
@endif
@endsection


@push('scripts')
 <script>
        document.addEventListener('DOMContentLoaded', function () {
            const select = document.getElementById('deliveryCourierSelect');
            const nameInput = document.getElementById('deliveryCourierName');
            const phoneInput = document.getElementById('deliveryCourierPhone');
            const companyInput = document.getElementById('deliveryCompanyName');

            select?.addEventListener('change', function () {
                const option = select.options[select.selectedIndex];

                if (!select.value) {
                    return;
                }

                if (nameInput) {
                    nameInput.value = option.dataset.name || '';
                }

                if (phoneInput) {
                    phoneInput.value = option.dataset.phone || '';
                }

                if (companyInput) {
                    companyInput.value = option.dataset.company || '';
                }
            });
        });
    </script>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    const currency = @json($restaurantOrder->currency ?? 'EGP');

    const menuItems = @json($menuItemsPayload ?? []);
    const menuItemsById = {};

    menuItems.forEach(function (item) {
        menuItemsById[String(item.id)] = item;
    });

    const orderLines = [];

    document.querySelectorAll('.js-order-edit-line').forEach(function (el) {
        orderLines.push({
            id: Number(el.dataset.lineId),
            item_id: el.dataset.itemId ? Number(el.dataset.itemId) : null,
            offer_id: el.dataset.offerId ? Number(el.dataset.offerId) : null,
            quantity: Number(el.dataset.quantity || 1),
            notes: el.dataset.notes || '',
            variant_id: el.dataset.variantId ? Number(el.dataset.variantId) : null,
            options: JSON.parse(el.dataset.options || '[]'),
            removed: false,
        });
    });

    const inputsWrap = document.getElementById('orderEditItemsInputs');

    const modalEl = document.getElementById('editOrderLineModal');
    const modal = modalEl && window.bootstrap ? new bootstrap.Modal(modalEl) : null;

    const modalIndex = document.getElementById('editOrderLineIndex');
    const modalTitle = document.getElementById('editOrderLineTitle');
    const modalDesc = document.getElementById('editOrderLineDesc');
    const variantWrap = document.getElementById('editOrderVariantWrap');
    const variantList = document.getElementById('editOrderVariantList');
    const optionsWrap = document.getElementById('editOrderOptionsWrap');
    const optionsList = document.getElementById('editOrderOptionsList');
    const qtyInput = document.getElementById('editOrderLineQty');
    const notesInput = document.getElementById('editOrderLineNotes');
    const preview = document.getElementById('editOrderLineTotalPreview');
    const saveBtn = document.getElementById('saveEditOrderLineBtn');

    function escapeHtml(value) {
        return String(value || '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function money(value) {
        return Number(value || 0).toFixed(2) + ' ' + currency;
    }

    function selectedVariant(item) {
        const input = document.querySelector('input[name="edit_order_variant_id"]:checked');

        if (!input || !item) {
            return null;
        }

        return (item.variants || []).find(v => String(v.id) === String(input.value)) || null;
    }

    function selectedOptions(item) {
        if (!item) {
            return [];
        }

        const selected = [];

        (item.option_groups || []).forEach(function (group) {
            const inputs = document.querySelectorAll('[data-edit-order-option-group="' + group.id + '"]:checked');

            inputs.forEach(function (input) {
                const option = (group.options || []).find(o => String(o.id) === String(input.value));

                if (option) {
                    selected.push(option);
                }
            });
        });

        return selected;
    }

    function unitPrice(item) {
        if (!item) {
            return 0;
        }

        const variant = selectedVariant(item);
        const options = selectedOptions(item);

        let price = variant ? Number(variant.price || 0) : Number(item.price || 0);

        options.forEach(function (option) {
            price += Number(option.price || 0);
        });

        return price;
    }

    function updatePreview() {
        const index = Number(modalIndex.value);
        const line = orderLines[index];

        if (!line) {
            return;
        }

        const item = menuItemsById[String(line.item_id)];
        const qty = Number(qtyInput.value || 1);

        preview.textContent = money(unitPrice(item) * qty);
    }

    function renderVariants(item, line) {
        const variants = item.variants || [];

        if (!variants.length) {
            variantWrap.style.display = 'none';
            variantList.innerHTML = '';
            return;
        }

        variantWrap.style.display = '';

        const selectedId = line.variant_id
            || variants.find(v => v.is_default)?.id
            || variants[0]?.id;

        variantList.innerHTML = variants.map(function (variant) {
            return `
                <label class="border rounded-4 p-3 d-flex justify-content-between align-items-center">
                    <span>
                        <input
                            type="radio"
                            name="edit_order_variant_id"
                            value="${variant.id}"
                            ${String(selectedId) === String(variant.id) ? 'checked' : ''}
                        >
                        <strong class="ms-2">${escapeHtml(variant.name)}</strong>
                    </span>

                    <span class="fw-bold">${money(variant.price)}</span>
                </label>
            `;
        }).join('');

        variantList.querySelectorAll('input').forEach(input => {
            input.addEventListener('change', updatePreview);
        });
    }

    function renderOptions(item, line) {
        const groups = item.option_groups || [];

        if (!groups.length) {
            optionsWrap.style.display = 'none';
            optionsList.innerHTML = '';
            return;
        }

        optionsWrap.style.display = '';

        const selectedIds = (line.options || []).map(String);

        optionsList.innerHTML = groups.map(function (group) {
            const type = group.type === 'single' ? 'radio' : 'checkbox';
            const name = 'edit_order_option_group_' + group.id;

            const optionsHtml = (group.options || []).map(function (option) {
                const checked = selectedIds.includes(String(option.id));

                return `
                    <label class="border rounded-4 p-3 d-flex justify-content-between align-items-center mb-2">
                        <span>
                            <input
                                type="${type}"
                                name="${name}${type === 'checkbox' ? '[]' : ''}"
                                value="${option.id}"
                                data-edit-order-option-group="${group.id}"
                                ${checked ? 'checked' : ''}
                            >
                            <strong class="ms-2">${escapeHtml(option.name)}</strong>
                        </span>

                        <span class="fw-bold">
                            ${Number(option.price || 0) > 0 ? '+' + money(option.price) : ''}
                        </span>
                    </label>
                `;
            }).join('');

            return `
                <div class="border rounded-4 p-3">
                    <div class="fw-bold mb-2">
                        ${escapeHtml(group.name)}
                        ${group.is_required ? '<span class="text-danger">*</span>' : ''}
                    </div>

                    ${optionsHtml}
                </div>
            `;
        }).join('');

        optionsList.querySelectorAll('input').forEach(input => {
            input.addEventListener('change', updatePreview);
        });
    }

    function validateLine(item) {
        for (const group of (item.option_groups || [])) {
            if (!group.is_required) {
                continue;
            }

            const checked = document.querySelectorAll('[data-edit-order-option-group="' + group.id + '"]:checked');

            if (!checked.length) {
                alert('اختر من مجموعة: ' + group.name);
                return false;
            }
        }

        return true;
    }

    function openEditLine(index) {
        const line = orderLines[index];

        if (!line || line.removed || line.offer_id) {
            return;
        }

        const item = menuItemsById[String(line.item_id)];

        if (!item) {
            alert('بيانات الصنف غير متاحة للتعديل.');
            return;
        }

        modalIndex.value = index;
        modalTitle.textContent = item.name;
        modalDesc.textContent = item.category_name || '';

        qtyInput.value = line.quantity;
        notesInput.value = line.notes || '';

        renderVariants(item, line);
        renderOptions(item, line);
        updatePreview();

        modal?.show();
    }

    function saveLine() {
        const index = Number(modalIndex.value);
        const line = orderLines[index];

        if (!line) {
            return;
        }

        const item = menuItemsById[String(line.item_id)];

        if (!validateLine(item)) {
            return;
        }

        const variant = selectedVariant(item);
        const options = selectedOptions(item);

        line.quantity = Number(qtyInput.value || 1);
        line.notes = notesInput.value || '';
        line.variant_id = variant ? Number(variant.id) : null;
        line.options = options.map(option => Number(option.id));

        buildInputs();

        modal?.hide();

        const card = document.querySelectorAll('.js-order-edit-line')[index];

        if (card) {
            card.classList.add('border-primary');
        }
    }

    function removeLine(index) {
        const line = orderLines[index];

        if (!line) {
            return;
        }

        if (!confirm('حذف هذا السطر من الطلب؟')) {
            return;
        }

        line.removed = true;
        line.quantity = 0;

        const card = document.querySelectorAll('.js-order-edit-line')[index];

        if (card) {
            card.style.opacity = '.45';
            card.querySelector('.js-remove-order-line')?.setAttribute('disabled', 'disabled');
            card.querySelector('.js-edit-order-line')?.setAttribute('disabled', 'disabled');
        }

        buildInputs();
    }

    function buildInputs() {
        let html = '';

        orderLines.forEach(function (line, index) {
            html += `
                <input type="hidden" name="items[${index}][id]" value="${line.id}">
                <input type="hidden" name="items[${index}][quantity]" value="${line.removed ? 0 : line.quantity}">
                <input type="hidden" name="items[${index}][notes]" value="${escapeHtml(line.notes || '')}">
            `;

            if (!line.offer_id) {
                if (line.variant_id) {
                    html += `<input type="hidden" name="items[${index}][variant_id]" value="${line.variant_id}">`;
                }

                (line.options || []).forEach(function (optionId, optionIndex) {
                    html += `
                        <input type="hidden" name="items[${index}][options][${optionIndex}]" value="${optionId}">
                    `;
                });
            }
        });

        inputsWrap.innerHTML = html;
    }

    document.querySelectorAll('.js-edit-order-line').forEach(function (button) {
        button.addEventListener('click', function () {
            openEditLine(Number(button.dataset.index));
        });
    });

    document.querySelectorAll('.js-remove-order-line').forEach(function (button) {
        button.addEventListener('click', function () {
            removeLine(Number(button.dataset.index));
        });
    });

    saveBtn?.addEventListener('click', saveLine);
    qtyInput?.addEventListener('input', updatePreview);

    buildInputs();
});
</script>
    @endpush