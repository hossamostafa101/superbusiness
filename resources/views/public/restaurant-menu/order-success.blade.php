{{-- resources/views/public/restaurant-menu/order-success.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم إرسال الطلب</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background: #f6f7fb;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            padding: 18px;
        }

        .wrap {
            max-width: 640px;
            margin: 0 auto;
        }

        .cardx {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 26px;
            box-shadow: 0 18px 45px rgba(15,23,42,.08);
            overflow: hidden;
        }

        .hero {
            background: linear-gradient(135deg, #111827, #2563eb);
            color: #fff;
            padding: 28px;
            text-align: center;
        }

        .line {
            border-bottom: 1px solid #eef0f3;
            padding: 14px 0;
        }

        .line:last-child {
            border-bottom: 0;
        }
    </style>
</head>
<body>

<div class="wrap">
    <div class="cardx">
        <div class="hero">
            <i class="bi bi-check-circle-fill" style="font-size: 58px;"></i>
            <h1 class="h3 fw-bold mt-3 mb-2">تم إرسال الطلب</h1>
            <div class="opacity-75">
                رقم الطلب:
                <strong>{{ $restaurantOrder->order_number }}</strong>
            </div>
        </div>

        <div class="p-4">
            <div class="alert alert-success">
                تم استلام طلبك بنجاح، وسيتم التواصل معك أو تجهيز الطلب حسب نوعه.
            </div>

            <div class="mb-3">
                <div class="fw-bold">بيانات الطلب</div>
                <div class="text-muted small">
                    الفرع: {{ $branch->name }}
                    —
                    النوع: {{ $restaurantOrder->orderTypeLabel() }}
                    —
                    الحالة: {{ $restaurantOrder->statusLabel() }}
                </div>
            </div>

            <div class="border rounded-4 p-3 mb-3">
                @foreach($restaurantOrder->items as $orderItem)
                    <div class="line">
                        <div class="d-flex justify-content-between gap-2">
                            <div>
                                <div class="fw-bold">
                                    {{ $orderItem->item_name }}

                                    @if($orderItem->variant_name)
                                        - {{ $orderItem->variant_name }}
                                    @endif
                                </div>

                                <div class="small text-muted">
                                    الكمية: {{ $orderItem->quantity }}
                                </div>

                                @if($orderItem->options->count())
                                    <div class="small text-muted mt-1">
                                        @foreach($orderItem->options as $option)
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

                                @if($orderItem->notes)
                                    <div class="small mt-1">
                                        ملاحظة: {{ $orderItem->notes }}
                                    </div>
                                @endif
                            </div>

                            <strong class="text-nowrap">
                                {{ number_format((float) $orderItem->line_total, 2) }}
                                {{ $orderItem->currency }}
                            </strong>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="border rounded-4 p-3 mb-4">
                <div class="d-flex justify-content-between">
                    <span>الإجمالي</span>
                    <strong>
                        {{ number_format((float) $restaurantOrder->total, 2) }}
                        {{ $restaurantOrder->currency }}
                    </strong>
                </div>
            </div>

            <div class="d-grid gap-2">
                <a href="{{ route('public.restaurant-menu.branch', [$workspace, $branch]) }}" class="btn btn-primary">
                    العودة للمنيو
                </a>

                @if($branch->whatsapp_number)
                    @php
                        $wa = preg_replace('/\D+/', '', $branch->whatsapp_number);
                        $message = urlencode('مرحبًا، أرسلت طلب رقم ' . $restaurantOrder->order_number . ' وأريد المتابعة.');
                    @endphp

                    <a href="https://wa.me/{{ $wa }}?text={{ $message }}" target="_blank" class="btn btn-outline-success">
                        متابعة عبر واتساب
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

</body>
</html>