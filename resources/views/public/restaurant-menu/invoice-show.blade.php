{{-- resources/views/public/restaurant-menu/invoice-show.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الفاتورة {{ $restaurantInvoice->invoice_number }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f6f7fb;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            padding: 18px;
        }

        .wrap {
            max-width: 760px;
            margin: 0 auto;
        }

        .hero {
            background: linear-gradient(135deg, #111827, #2563eb);
            color: #fff;
            border-radius: 26px;
            padding: 24px;
            margin-bottom: 16px;
        }

        .cardx {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 22px;
            padding: 18px;
            margin-bottom: 14px;
        }

        .line {
            border-bottom: 1px solid #edf0f3;
            padding: 13px 0;
        }

        .line:last-child {
            border-bottom: 0;
        }
    </style>
</head>
<body>

<div class="wrap">
    <div class="hero">
        <div class="d-flex justify-content-between align-items-start gap-3">
            <div>
                <h1 class="h4 fw-bold mb-2">
                    فاتورة {{ $restaurantInvoice->invoice_number }}
                </h1>

                <div class="opacity-75">
                    {{ $workspace->name }}
                    —
                    {{ $branch->name }}
                </div>

                @if($restaurantInvoice->table_number)
                    <div class="mt-2">
                        الطاولة:
                        <strong>{{ $restaurantInvoice->table_number }}</strong>
                    </div>
                @endif
            </div>

            <span class="badge {{ $restaurantInvoice->statusBadgeClass() }}">
                {{ $restaurantInvoice->statusLabel() }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="cardx">
        <div class="row g-3">
            <div class="col-6">
                <small class="text-muted d-block">تم الفتح</small>
                <strong>{{ $restaurantInvoice->opened_at?->format('Y-m-d H:i') }}</strong>
            </div>

            <div class="col-6">
                <small class="text-muted d-block">تنتهي في</small>
                <strong>{{ $restaurantInvoice->expires_at?->format('Y-m-d H:i') }}</strong>
            </div>

            <div class="col-6">
                <small class="text-muted d-block">صاحب الفاتورة</small>
                <strong>{{ $restaurantInvoice->opened_by_name ?: '-' }}</strong>
            </div>

            <div class="col-6">
                <small class="text-muted d-block">عدد الضيوف</small>
                <strong>{{ $restaurantInvoice->guests->count() }}</strong>
            </div>
        </div>
    </div>

    <div class="cardx">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2 class="h6 fw-bold mb-0">الأصناف</h2>

            <a
                href="{{ route('public.restaurant-menu.branch', [
                    'workspace' => $workspace,
                    'branch' => $branch,
                    'invoice_id' => $restaurantInvoice->id,
                ]) }}"
                class="btn btn-sm btn-primary"
            >
                إضافة أصناف
            </a>
        </div>

        @forelse($restaurantInvoice->items as $invoiceItem)
            <div class="line">
                <div class="d-flex justify-content-between gap-3">
                    <div>
                        <div class="fw-bold">
                            {{ $invoiceItem->item_name }}

                            @if($invoiceItem->variant_name)
                                <span class="text-muted">
                                    - {{ $invoiceItem->variant_name }}
                                </span>
                            @endif
                        </div>

                        <div class="small text-muted">
                            الكمية:
                            {{ $invoiceItem->quantity }}
                            —
                            الحالة:
                            {{ $invoiceItem->status }}
                        </div>

                        @if($invoiceItem->options->count())
                            <div class="small text-muted mt-1">
                                @foreach($invoiceItem->options as $option)
                                    <div>
                                        {{ $option->group_name }}:
                                        {{ $option->option_name }}

                                        @if((float) $option->price > 0)
                                            (+{{ number_format((float) $option->price, 2) }} {{ $option->currency }})
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if($invoiceItem->notes)
                            <div class="small mt-1">
                                ملاحظة:
                                {{ $invoiceItem->notes }}
                            </div>
                        @endif
                    </div>

                    <strong class="text-nowrap">
                        {{ number_format((float) $invoiceItem->line_total, 2) }}
                        {{ $invoiceItem->currency }}
                    </strong>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-4">
                لا توجد أصناف في الفاتورة بعد.
            </div>
        @endforelse
    </div>

    <div class="cardx">
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

        <hr>

        <div class="d-flex justify-content-between h5 mb-0">
            <span>الإجمالي</span>
            <strong>
                {{ number_format((float) $restaurantInvoice->total, 2) }}
                {{ $restaurantInvoice->currency }}
            </strong>
        </div>
    </div>

    <div class="d-grid gap-2">
        <a
            href="{{ route('public.restaurant-menu.branch', [
                'workspace' => $workspace,
                'branch' => $branch,
                'invoice_id' => $restaurantInvoice->id,
            ]) }}"
            class="btn btn-primary btn-lg"
        >
            الرجوع للمنيو
        </a>
    </div>
</div>

</body>
</html>