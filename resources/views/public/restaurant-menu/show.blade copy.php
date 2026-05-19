{{-- resources/views/public/restaurant-menu/show.blade.php --}}
@php
    $profile = $workspace->businessProfile ?? null;

    // $themeColor = $profile?->theme_color ?: '#111827';
    // $buttonColor = $profile?->button_color ?: '#2563eb';

    $themeColors = $menuTheme['colors'] ?? [];

$themeColor = $themeColors['theme_color'] ?? ($profile?->theme_color ?: '#111827');
$buttonColor = $themeColors['button_color'] ?? ($profile?->button_color ?: '#2563eb');
$backgroundColor = $themeColors['background_color'] ?? '#f6f7fb';
$textColor = $themeColors['text_color'] ?? '#111827';

$fontFamily = $menuTheme['typography']['font_family'] ?? 'system';


    $itemsPayload = [];

    foreach ($branch->categories as $category) {
        foreach ($category->items as $item) {
            $itemsPayload[$item->id] = [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'image' => $item->image ? asset('storage/' . $item->image) : null,
                'price' => (float) $item->price,
                'sale_price' => $item->sale_price !== null ? (float) $item->sale_price : null,
                'currency' => $item->currency,
                'calories' => $item->calories,
                'preparation_time_minutes' => $item->preparation_time_minutes,
                'variants' => $item->activeVariants
                    ->map(
                        fn($variant) => [
                            'id' => $variant->id,
                            'name' => $variant->name,
                            'price' => (float) $variant->price,
                            'sale_price' => $variant->sale_price !== null ? (float) $variant->sale_price : null,
                            'currency' => $variant->currency,
                            'is_default' => (bool) $variant->is_default,
                        ],
                    )
                    ->values(),
                'option_groups' => $item->activeOptionGroups
                    ->map(
                        fn($group) => [
                            'id' => $group->id,
                            'name' => $group->name,
                            'type' => $group->type,
                            'is_required' => (bool) $group->is_required,
                            'min_choices' => (int) $group->min_choices,
                            'max_choices' => $group->max_choices ? (int) $group->max_choices : null,
                            'options' => $group->options
                                ->map(
                                    fn($option) => [
                                        'id' => $option->id,
                                        'name' => $option->name,
                                        'price' => (float) $option->price,
                                        'currency' => $option->currency,
                                    ],
                                )
                                ->values(),
                        ],
                    )
                    ->values(),
            ];
        }
    }
@endphp

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منيو {{ $workspace->name }} - {{ $branch->name }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* :root {
            --theme-color: {{ $themeColor }};
            --button-color: {{ $buttonColor }};
            --soft-bg: #f6f7fb;
            --border: #e5e7eb;
            --text: #111827;
            --muted: #6b7280;
        } */

        :root {
    --theme-color: {{ $themeColor }};
    --button-color: {{ $buttonColor }};
    --soft-bg: {{ $backgroundColor }};
    --border: #e5e7eb;
    --text: {{ $textColor }};
    --muted: #6b7280;
}

        body {
            background: var(--soft-bg);
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text);
        }

        .menu-wrapper {
            max-width: 820px;
            margin: 0 auto;
            padding: 14px 12px 90px;
        }

        .hero {
            background:
                radial-gradient(circle at top left, rgba(255, 255, 255, .20), transparent 34%),
                linear-gradient(135deg, var(--theme-color), var(--button-color));
            color: #fff;
            border-radius: 28px;
            padding: 24px;
            margin-bottom: 14px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, .16);
        }

        .branch-switch {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 12px;
            margin-bottom: 14px;
            overflow-x: auto;
            display: flex;
            gap: 8px;
        }

        .branch-pill {
            white-space: nowrap;
            border-radius: 999px;
            border: 1px solid var(--border);
            padding: 8px 13px;
            color: var(--text);
            text-decoration: none;
            background: #fff;
            font-size: 14px;
        }

        .branch-pill.active {
            color: #fff;
            background: var(--button-color);
            border-color: var(--button-color);
        }

        .category-tabs {
            position: sticky;
            top: 0;
            z-index: 10;
            background: rgba(246, 247, 251, .92);
            backdrop-filter: blur(12px);
            padding: 10px 0;
            overflow-x: auto;
            display: flex;
            gap: 8px;
            margin-bottom: 12px;
        }

        .category-tab {
            white-space: nowrap;
            border-radius: 999px;
            background: #fff;
            color: var(--text);
            border: 1px solid var(--border);
            padding: 8px 13px;
            text-decoration: none;
            font-size: 14px;
        }

        .category-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 18px;
            margin-bottom: 16px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .04);
        }

        .item-card {
            border-bottom: 1px solid #edf0f3;
            padding: 14px 0;
            cursor: pointer;
        }

        .item-card:last-child {
            border-bottom: 0;
        }

        .item-image {
            width: 92px;
            height: 92px;
            border-radius: 18px;
            object-fit: cover;
            background: #f3f4f6;
            flex-shrink: 0;
        }

        .item-title {
            font-weight: 800;
            line-height: 1.35;
        }

        .item-desc {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
        }

        .price {
            font-weight: 900;
            white-space: nowrap;
        }

        .old-price {
            color: var(--muted);
            text-decoration: line-through;
            font-size: 12px;
        }

        .tag {
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 999px;
            background: #eff6ff;
            color: #2563eb;
        }

        .modal-content {
            border: 0;
            border-radius: 26px;
            overflow: hidden;
        }

        .modal-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: #f3f4f6;
        }

        .option-box {
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 12px;
            margin-bottom: 12px;
        }

        .choice-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            padding: 9px 0;
            border-bottom: 1px solid #f1f3f5;
        }

        .choice-row:last-child {
            border-bottom: 0;
        }

        .btn-main {
            background: var(--button-color);
            color: #fff;
            border: 0;
            border-radius: 14px;
            min-height: 46px;
            font-weight: 800;
        }

        .btn-main:hover {
            color: #fff;
            filter: brightness(.97);
        }

        @media (max-width: 575px) {
            .hero {
                padding: 20px;
            }

            .item-image {
                width: 82px;
                height: 82px;
            }

            .category-card {
                padding: 15px;
            }
        }








        .cart-float {
            position: fixed;
            right: 12px;
            left: 12px;
            bottom: 14px;
            z-index: 1040;
            max-width: 820px;
            margin: 0 auto;
        }

        .cart-button {
            width: 100%;
            min-height: 54px;
            border: 0;
            border-radius: 18px;
            background: var(--button-color);
            color: #fff;
            box-shadow: 0 18px 40px rgba(15, 23, 42, .22);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 18px;
            font-weight: 900;
        }

        @if(!empty($menuTheme['custom_css']))
    {!! $menuTheme['custom_css'] !!}
@endif
    </style>
</head>

<body>

    <div class="menu-wrapper">
        <div class="hero">
            <div class="d-flex justify-content-between align-items-start gap-3">
                <div>
                    <h1 class="h3 fw-bold mb-2">{{ $workspace->name }}</h1>
                    <div class="opacity-75">
                        <i class="bi bi-shop"></i>
                        {{ $branch->name }}
                    </div>

                    @if ($branch->address)
                        <div class="mt-2 small opacity-75">
                            <i class="bi bi-geo-alt"></i>
                            {{ $branch->address }}
                        </div>
                    @endif
                </div>

                @if ($branch->whatsapp_number)
                    @php
                        $wa = preg_replace('/\D+/', '', $branch->whatsapp_number);
                    @endphp

                    <a href="https://wa.me/{{ $wa }}" target="_blank" class="btn btn-light rounded-pill">
                        <i class="bi bi-whatsapp text-success"></i>
                    </a>
                @endif
            </div>
        </div>

        @if (!empty($selectedTable))
            <div class="alert alert-info border-0 rounded-4">
                <i class="bi bi-qr-code"></i>
                أنت تطلب من طاولة:
                <strong>{{ $selectedTable->number }}</strong>
                —
                {{ $selectedTable->name }}
            </div>
        @endif

        @if (session('invoice_pin'))
            <div class="alert alert-success rounded-4">
                <div class="fw-bold mb-1">
                    تم فتح الفاتورة بنجاح
                </div>

                <div>
                    رقم PIN الخاص بالفاتورة:
                    <strong dir="ltr" style="font-size: 22px;">
                        {{ session('invoice_pin') }}
                    </strong>
                </div>

                <div class="small mt-2">
                    احتفظ بهذا الرقم. أي شخص يريد الإضافة على نفس الفاتورة سيحتاج إدخاله.
                </div>
            </div>
        @endif

        @if (!empty($openInvoiceEnabled) && !empty($selectedTable))
            @if (!empty($currentInvoice) && !empty($currentInvoiceGuest))
                <div class="alert alert-success rounded-4">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <div class="fw-bold">
                                أنت متصل بالفاتورة الحالية
                            </div>

                            <div class="small">
                                رقم الفاتورة:
                                <strong>{{ $currentInvoice->invoice_number }}</strong>
                                —
                                تنتهي:
                                <strong>{{ $currentInvoice->expires_at?->format('H:i') }}</strong>
                            </div>
                        </div>

                        <a href="{{ route('public.restaurant-menu.invoices.show', [$workspace, $branch, $currentInvoice]) }}"
                            class="btn btn-sm btn-outline-success">
                            عرض الفاتورة
                        </a>
                    </div>
                </div>
            @elseif(!empty($openInvoice))
                <div class="alert alert-warning rounded-4">
                    <div class="fw-bold mb-1">
                        توجد فاتورة مفتوحة لهذه الطاولة
                    </div>

                    <div class="small mb-3">
                        يمكنك فتح فاتورة جديدة كضيف مستقل، أو الانضمام للفاتورة الحالية باستخدام PIN إذا كان معك.
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        @if ($invoiceJoinPolicy === 'allow_with_pin')
                            <button type="button" class="btn btn-dark btn-sm" data-bs-toggle="modal"
                                data-bs-target="#joinInvoiceModal">
                                الانضمام للفاتورة الحالية
                            </button>
                        @endif

                        @if (!empty($allowNewInvoiceWhenTableBusy))
                            <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal"
                                data-bs-target="#openInvoiceModal">
                                فتح فاتورة جديدة
                            </button>
                        @else
                            <span class="badge bg-danger align-self-center">
                                لا يمكن فتح فاتورة جديدة حتى إغلاق الحالية
                            </span>
                        @endif
                    </div>
                </div>
            @else
                <div class="alert alert-light border rounded-4">
                    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                        <div>
                            <div class="fw-bold">
                                نظام الفاتورة المفتوحة مفعل
                            </div>

                            <div class="small text-muted">
                                افتح فاتورة للطاولة ثم أضف طلباتك عليها خلال مدة الجلسة.
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#openInvoiceModal">
                            فتح فاتورة
                        </button>
                    </div>
                </div>
            @endif
        @endif

        @if ($branches->count() > 1)
            <div class="branch-switch">
                @foreach ($branches as $branchItem)
                    <a href="{{ route('public.restaurant-menu.branch', [$workspace, $branchItem]) }}"
                        class="branch-pill {{ $branchItem->id === $branch->id ? 'active' : '' }}">
                        {{ $branchItem->name }}
                    </a>
                @endforeach
            </div>
        @endif

        @if ($branch->categories->count())
            <div class="category-tabs">
                @foreach ($branch->categories as $category)
                    @if ($category->items->count())
                        <a href="#category-{{ $category->id }}" class="category-tab">
                            {{ $category->name }}
                        </a>
                    @endif
                @endforeach
            </div>
        @endif

        @forelse($branch->categories as $category)
            @if ($category->items->count())
                <section class="category-card" id="category-{{ $category->id }}">
                    <div class="mb-3">
                        <h2 class="h5 fw-bold mb-1">{{ $category->name }}</h2>

                        @if ($category->description)
                            <p class="text-muted small mb-0">{{ $category->description }}</p>
                        @endif
                    </div>

                    @foreach ($category->items as $item)
                        <div class="item-card d-flex gap-3" data-item-id="{{ $item->id }}">
                            @if ($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" class="item-image"
                                    alt="{{ $item->name }}">
                            @else
                                <div class="item-image d-flex align-items-center justify-content-center text-muted">
                                    <i class="bi bi-image"></i>
                                </div>
                            @endif

                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between gap-2 align-items-start">
                                    <div>
                                        <div class="item-title">
                                            {{ $item->name }}
                                        </div>

                                        <div class="d-flex gap-1 flex-wrap mt-1">
                                            @if ($item->is_featured)
                                                <span class="tag">مميز</span>
                                            @endif

                                            @if ($item->activeVariants->count())
                                                <span class="tag">أحجام</span>
                                            @endif

                                            @if ($item->activeOptionGroups->count())
                                                <span class="tag">إضافات</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        @if ($item->sale_price)
                                            <div class="price text-success">
                                                {{ number_format((float) $item->sale_price, 2) }}
                                                {{ $item->currency }}
                                            </div>

                                            <div class="old-price">
                                                {{ number_format((float) $item->price, 2) }}
                                                {{ $item->currency }}
                                            </div>
                                        @else
                                            <div class="price">
                                                {{ number_format((float) $item->price, 2) }}
                                                {{ $item->currency }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if ($item->description)
                                    <div class="item-desc mt-2">
                                        {{ \Illuminate\Support\Str::limit($item->description, 115) }}
                                    </div>
                                @endif

                                <div class="small text-muted mt-2">
                                    @if ($item->calories)
                                        {{ $item->calories }} كالوري
                                    @endif

                                    @if ($item->preparation_time_minutes)
                                        @if ($item->calories)
                                            —
                                        @endif
                                        {{ $item->preparation_time_minutes }} دقيقة
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </section>
            @endif
        @empty
            <div class="category-card text-center text-muted">
                لا توجد تصنيفات في هذا الفرع بعد.
            </div>
        @endforelse
    </div>

    <div class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div id="modalImageWrap"></div>

                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between gap-3 align-items-start mb-2">
                        <div>
                            <h3 class="h5 fw-bold mb-1" id="modalTitle"></h3>
                            <p class="text-muted small mb-0" id="modalDescription"></p>
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="d-flex gap-2 flex-wrap small text-muted mb-3" id="modalMeta"></div>

                    <div class="border rounded-4 p-3 mb-3">
                        <div class="small text-muted">السعر</div>
                        <div class="h5 fw-bold mb-0" id="modalPrice"></div>
                        <div class="old-price" id="modalOldPrice"></div>
                    </div>

                    <div id="modalVariants"></div>
                    <div id="modalOptionGroups"></div>

                    <div class="mb-3">
                        <label class="form-label">الكمية</label>
                        <div class="d-flex gap-2 align-items-center">
                            <button type="button" class="btn btn-light border" id="qtyMinus">-</button>
                            <input type="number" id="modalQty" value="1" min="1" max="100"
                                class="form-control text-center" style="max-width: 90px;">
                            <button type="button" class="btn btn-light border" id="qtyPlus">+</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ملاحظات على الصنف</label>
                        <textarea id="modalItemNotes" class="form-control" rows="2" placeholder="مثال: بدون بصل، زيادة صوص"></textarea>
                    </div>

                    <button type="button" class="btn btn-main w-100 mt-2" id="addToCartBtn">
                        إضافة إلى الطلب
                    </button>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h3 class="h5 fw-bold mb-1">مراجعة الطلب</h3>
                            <p class="text-muted small mb-0">راجع الأصناف ثم املأ بياناتك لإرسال الطلب.</p>
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>راجع البيانات:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div id="cartItemsWrap" class="mb-3"></div>

                    <div class="border rounded-4 p-3 mb-3">
                        <div class="d-flex justify-content-between">
                            <span>الإجمالي</span>
                            <strong id="cartModalTotal">0.00</strong>
                        </div>
                    </div>

                    <form method="POST"
                        action="{{ route('public.restaurant-menu.orders.store', [$workspace, $branch]) }}"
                        id="checkoutForm">
                        @csrf

                        @if (!empty($currentInvoice) && !empty($currentInvoiceGuest))
                            <input type="hidden" name="invoice_id" value="{{ $currentInvoice->id }}">
                        @endif

                        @if (!empty($selectedTable))
                            <input type="hidden" name="table_code" value="{{ $selectedTable->code }}">
                        @endif

                        <div id="checkoutItemsInputs"></div>

                        <div class="mb-3">
                            <label class="form-label">الاسم <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                                class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                            <input type="text" name="customer_phone" value="{{ old('customer_phone') }}"
                                class="form-control" required placeholder="2010xxxxxxxx">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                                class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">نوع الطلب <span class="text-danger">*</span></label>

                            <select name="order_type" id="orderType" class="form-select" required>
                                <option value="takeaway" @selected(old('order_type') === 'takeaway' && empty($selectedTable))>
                                    تيك أواي
                                </option>

                                <option value="dine_in" @selected(old('order_type', !empty($selectedTable) || !empty($currentInvoice) ? 'dine_in' : null) === 'dine_in')>
                                    داخل المكان
                                </option>

                                <option value="delivery" @selected(old('order_type') === 'delivery')>
                                    دليفري
                                </option>
                            </select>


                            {{-- <select name="order_type" id="orderType" class="form-select" required>
                                <option value="takeaway" @selected(old('order_type') === 'takeaway' && empty($selectedTable))>تيك أواي</option>
                                <option value="dine_in" @selected(old('order_type', !empty($selectedTable) ? 'dine_in' : null) === 'dine_in')>داخل المكان</option>
                                <option value="delivery" @selected(old('order_type') === 'delivery')>دليفري</option>
                            </select> --}}
                        </div>

                        <div class="mb-3" id="tableNumberWrap" style="display:none;">
                            <label class="form-label">رقم الطاولة</label>
                            @if (!empty($selectedTable))
                                <input type="text" value="{{ $selectedTable->number }}" class="form-control"
                                    readonly>
                                <input type="hidden" name="table_number" value="{{ $selectedTable->number }}">
                            @else
                                <input type="text" name="table_number" value="{{ old('table_number') }}"
                                    class="form-control" placeholder="مثال: 5">
                            @endif
                        </div>

                        <div class="mb-3" id="deliveryAddressWrap" style="display:none;">
                            <label class="form-label">عنوان التوصيل</label>
                            <textarea name="delivery_address" rows="3" class="form-control" placeholder="اكتب العنوان بالتفصيل">{{ old('delivery_address') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ملاحظات عامة</label>
                            <textarea name="notes" rows="3" class="form-control" placeholder="أي ملاحظات على الطلب">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-main w-100" id="submitOrderBtn">
                            إرسال الطلب
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    @if (!empty($openInvoiceEnabled) && !empty($selectedTable))
        <div class="modal fade" id="openInvoiceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h3 class="h5 fw-bold mb-1">فتح فاتورة للطاولة</h3>
                                <p class="text-muted small mb-0">
                                    سيتم إنشاء فاتورة مفتوحة لمدة محددة، وسيظهر لك PIN للمشاركة.
                                </p>
                            </div>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <form method="POST"
                            action="{{ route('public.restaurant-menu.invoices.open', [$workspace, $branch]) }}">
                            @csrf

                            <input type="hidden" name="table_code" value="{{ $selectedTable->code }}">
                            <input type="hidden" name="table_number" value="{{ $selectedTable->number }}">

                            <div class="mb-3">
                                <label class="form-label">اسمك <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                                    class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                <input type="text" name="customer_phone" value="{{ old('customer_phone') }}"
                                    class="form-control" required placeholder="2010xxxxxxxx">
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                فتح الفاتورة
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif




    @if (!empty($openInvoiceEnabled) && !empty($openInvoice) && $invoiceJoinPolicy === 'allow_with_pin')
        <div class="modal fade" id="joinInvoiceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h3 class="h5 fw-bold mb-1">الانضمام للفاتورة</h3>
                                <p class="text-muted small mb-0">
                                    أدخل PIN الخاص بالفاتورة المفتوحة لهذه الطاولة.
                                </p>
                            </div>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <form method="POST"
                            action="{{ route('public.restaurant-menu.invoices.join', [$workspace, $branch, $openInvoice]) }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">اسمك <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                                    class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                <input type="text" name="customer_phone" value="{{ old('customer_phone') }}"
                                    class="form-control" required placeholder="2010xxxxxxxx">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">PIN <span class="text-danger">*</span></label>
                                <input type="text" name="pin" class="form-control text-center" required
                                    dir="ltr" placeholder="24-81" maxlength="20"
                                    style="font-size: 22px; letter-spacing: 3px;">
                            </div>

                            <button type="submit" class="btn btn-dark w-100">
                                الانضمام
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif




    <div class="cart-float" id="cartFloat" style="display:none;">
        <button type="button" class="cart-button" data-bs-toggle="modal" data-bs-target="#cartModal">
            <span>
                <i class="bi bi-bag"></i>
                الطلب
            </span>

            <strong id="cartFloatTotal">0.00</strong>
        </button>
    </div>

    <script>
        window.restaurantMenuItems = @json($itemsPayload);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    window.hasCurrentInvoice = @json(!empty($currentInvoice) && !empty($currentInvoiceGuest));
    window.openInvoiceEnabled = @json(!empty($openInvoiceEnabled));
    window.hasSelectedTable = @json(!empty($selectedTable));
</script>

    <script>
        (function() {
            const modalElement = document.getElementById('itemModal');
            const modal = new bootstrap.Modal(modalElement);

            const imageWrap = document.getElementById('modalImageWrap');
            const title = document.getElementById('modalTitle');
            const description = document.getElementById('modalDescription');
            const meta = document.getElementById('modalMeta');
            const price = document.getElementById('modalPrice');
            const oldPrice = document.getElementById('modalOldPrice');
            const variantsWrap = document.getElementById('modalVariants');
            const groupsWrap = document.getElementById('modalOptionGroups');

            const qtyInput = document.getElementById('modalQty');
            const qtyMinus = document.getElementById('qtyMinus');
            const qtyPlus = document.getElementById('qtyPlus');
            const notesInput = document.getElementById('modalItemNotes');
            const addToCartBtn = document.getElementById('addToCartBtn');

            const cartFloat = document.getElementById('cartFloat');
            const cartFloatTotal = document.getElementById('cartFloatTotal');
            const cartItemsWrap = document.getElementById('cartItemsWrap');
            const cartModalTotal = document.getElementById('cartModalTotal');
            const checkoutItemsInputs = document.getElementById('checkoutItemsInputs');
            const checkoutForm = document.getElementById('checkoutForm');

            const orderType = document.getElementById('orderType');
            const tableNumberWrap = document.getElementById('tableNumberWrap');
            const deliveryAddressWrap = document.getElementById('deliveryAddressWrap');

            let currentItem = null;
            let cart = [];

            function money(value, currency) {
                const number = Number(value || 0).toFixed(2);
                return `${number} ${currency || ''}`;
            }

            function itemBasePrice(item) {
                return item.sale_price !== null ? Number(item.sale_price) : Number(item.price);
            }

            function variantPrice(variant) {
                return variant.sale_price !== null ? Number(variant.sale_price) : Number(variant.price);
            }

            function getSelectedVariant(item) {
                const checked = document.querySelector('input[name="variant_id"]:checked');

                if (!checked || !checked.value) {
                    return null;
                }

                return (item.variants || []).find(v => String(v.id) === String(checked.value)) || null;
            }

            function getSelectedOptions(item) {
                const selected = [];

                (item.option_groups || []).forEach(function(group) {
                    const inputs = document.querySelectorAll(`[data-option-group="${group.id}"]:checked`);

                    inputs.forEach(function(input) {
                        const option = (group.options || []).find(o => String(o.id) === String(input
                            .value));

                        if (option) {
                            selected.push({
                                ...option,
                                group_id: group.id,
                                group_name: group.name
                            });
                        }
                    });
                });

                return selected;
            }

            function calculateLine(item, variant, options, quantity) {
                const unit = variant ? variantPrice(variant) : itemBasePrice(item);
                const optionsTotal = options.reduce((sum, option) => sum + Number(option.price || 0), 0);

                return {
                    unit,
                    optionsTotal,
                    lineTotal: (unit + optionsTotal) * quantity
                };
            }

            function validateSelections(item) {
                for (const group of (item.option_groups || [])) {
                    const checked = document.querySelectorAll(`[data-option-group="${group.id}"]:checked`);
                    const count = checked.length;

                    if (group.is_required && count < Math.max(1, Number(group.min_choices || 0))) {
                        alert(`يجب اختيار ${group.name}`);
                        return false;
                    }

                    if (group.max_choices && count > Number(group.max_choices)) {
                        alert(`تجاوزت الحد الأقصى في ${group.name}`);
                        return false;
                    }

                    if (group.type === 'single' && count > 1) {
                        alert(`يمكن اختيار خيار واحد فقط من ${group.name}`);
                        return false;
                    }
                }

                return true;
            }

            function renderItem(item) {
                currentItem = item;

                qtyInput.value = 1;
                notesInput.value = '';

                imageWrap.innerHTML = item.image ?
                    `<img src="${item.image}" alt="${item.name}" class="modal-image">` :
                    `<div class="modal-image d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image fs-1"></i></div>`;

                title.textContent = item.name;
                description.textContent = item.description || '';

                meta.innerHTML = '';

                if (item.calories) {
                    meta.innerHTML += `<span class="badge bg-light text-dark border">${item.calories} كالوري</span>`;
                }

                if (item.preparation_time_minutes) {
                    meta.innerHTML +=
                        `<span class="badge bg-light text-dark border">${item.preparation_time_minutes} دقيقة</span>`;
                }

                const finalPrice = item.sale_price !== null ? item.sale_price : item.price;
                price.textContent = money(finalPrice, item.currency);

                oldPrice.textContent = item.sale_price !== null ?
                    money(item.price, item.currency) :
                    '';

                renderVariants(item);
                renderOptionGroups(item);
            }

            function renderVariants(item) {
                variantsWrap.innerHTML = '';

                if (!item.variants || item.variants.length === 0) {
                    return;
                }

                let html = `
                <div class="option-box">
                    <div class="fw-bold mb-2">الأحجام / الاختيارات السعرية</div>
            `;

                item.variants.forEach(function(variant) {
                    const finalPrice = variant.sale_price !== null ? variant.sale_price : variant.price;

                    html += `
                    <label class="choice-row">
                        <span>
                            <input type="radio" name="variant_id" value="${variant.id}" ${variant.is_default ? 'checked' : ''}>
                            <span class="ms-1">${variant.name}</span>
                            ${variant.is_default ? '<span class="badge bg-primary ms-1">افتراضي</span>' : ''}
                        </span>

                        <strong>${money(finalPrice, variant.currency)}</strong>
                    </label>
                `;
                });

                html += `</div>`;

                variantsWrap.innerHTML = html;
            }

            function renderOptionGroups(item) {
                groupsWrap.innerHTML = '';

                if (!item.option_groups || item.option_groups.length === 0) {
                    return;
                }

                let html = '';

                item.option_groups.forEach(function(group) {
                    html += `
                    <div class="option-box">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <div>
                                <div class="fw-bold">${group.name}</div>
                                <div class="small text-muted">
                                    ${group.type === 'single' ? 'اختيار واحد' : 'اختيارات متعددة'}
                                    ${group.is_required ? ' — إجباري' : ' — اختياري'}
                                </div>
                            </div>
                        </div>
                `;

                    if (!group.options || group.options.length === 0) {
                        html += `<div class="text-muted small">لا توجد خيارات متاحة.</div>`;
                    } else {
                        group.options.forEach(function(option) {
                            const inputType = group.type === 'single' ? 'radio' : 'checkbox';
                            const inputName = group.type === 'single' ?
                                `option_group_${group.id}` :
                                `option_group_${group.id}[]`;

                            html += `
                            <label class="choice-row">
                                <span>
                                    <input
                                        type="${inputType}"
                                        name="${inputName}"
                                        value="${option.id}"
                                        data-option-group="${group.id}"
                                    >
                                    <span class="ms-1">${option.name}</span>
                                </span>

                                <span class="small">
                                    ${Number(option.price) > 0 ? '+' + money(option.price, option.currency) : 'بدون تكلفة'}
                                </span>
                            </label>
                        `;
                        });
                    }

                    html += `</div>`;
                });

                groupsWrap.innerHTML = html;
            }

            function addCurrentItemToCart() {
                if (!currentItem) {
                    return;
                }

                if (!validateSelections(currentItem)) {
                    return;
                }

                const quantity = Math.max(1, parseInt(qtyInput.value || '1', 10));
                const variant = getSelectedVariant(currentItem);
                const options = getSelectedOptions(currentItem);
                const pricing = calculateLine(currentItem, variant, options, quantity);

                cart.push({
                    key: Date.now() + '_' + Math.random().toString(16).slice(2),
                    item: currentItem,
                    variant,
                    options,
                    quantity,
                    notes: notesInput.value || '',
                    unit_price: pricing.unit,
                    options_total: pricing.optionsTotal,
                    line_total: pricing.lineTotal,
                    currency: currentItem.currency
                });

                renderCart();
                modal.hide();
            }

            function cartTotal() {
                return cart.reduce((sum, line) => sum + Number(line.line_total || 0), 0);
            }

            function renderCart() {
                if (cart.length === 0) {
                    cartFloat.style.display = 'none';
                    cartItemsWrap.innerHTML = `<div class="text-center text-muted py-3">الطلب فارغ.</div>`;
                    cartModalTotal.textContent = money(0, 'EGP');
                    checkoutItemsInputs.innerHTML = '';
                    return;
                }

                const currency = cart[0]?.currency || 'EGP';
                const total = cartTotal();

                cartFloat.style.display = 'block';
                cartFloatTotal.textContent = money(total, currency);
                cartModalTotal.textContent = money(total, currency);

                cartItemsWrap.innerHTML = cart.map(function(line) {
                    const optionsText = line.options.length ?
                        line.options.map(o => `${o.group_name}: ${o.name}`).join('، ') :
                        '';

                    return `
                    <div class="border rounded-4 p-3 mb-2">
                        <div class="d-flex justify-content-between gap-2">
                            <div>
                                <div class="fw-bold">
                                    ${line.item.name}
                                    ${line.variant ? ' - ' + line.variant.name : ''}
                                </div>

                                <div class="small text-muted">
                                    الكمية: ${line.quantity}
                                </div>

                                ${optionsText ? `<div class="small text-muted mt-1">${optionsText}</div>` : ''}
                                ${line.notes ? `<div class="small mt-1">ملاحظة: ${line.notes}</div>` : ''}
                            </div>

                            <div class="text-end">
                                <strong>${money(line.line_total, line.currency)}</strong>

                                <button type="button" class="btn btn-sm btn-outline-danger d-block mt-2 remove-cart-line" data-key="${line.key}">
                                    حذف
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                }).join('');

                renderCheckoutInputs();
            }

            function renderCheckoutInputs() {
                let html = '';

                cart.forEach(function(line, index) {
                    html += `
                    <input type="hidden" name="items[${index}][item_id]" value="${line.item.id}">
                    <input type="hidden" name="items[${index}][quantity]" value="${line.quantity}">
                    <input type="hidden" name="items[${index}][notes]" value="${escapeHtml(line.notes)}">
                `;

                    if (line.variant) {
                        html +=
                            `<input type="hidden" name="items[${index}][variant_id]" value="${line.variant.id}">`;
                    }

                    line.options.forEach(function(option, optionIndex) {
                        html +=
                            `<input type="hidden" name="items[${index}][options][${optionIndex}]" value="${option.id}">`;
                    });
                });

                checkoutItemsInputs.innerHTML = html;
            }

            function escapeHtml(value) {
                return String(value || '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;');
            }

            function syncOrderTypeFields() {
                const type = orderType.value;

                tableNumberWrap.style.display = type === 'dine_in' ? 'block' : 'none';
                deliveryAddressWrap.style.display = type === 'delivery' ? 'block' : 'none';
            }

            qtyMinus?.addEventListener('click', function() {
                qtyInput.value = Math.max(1, parseInt(qtyInput.value || '1', 10) - 1);
            });

            qtyPlus?.addEventListener('click', function() {
                qtyInput.value = Math.min(100, parseInt(qtyInput.value || '1', 10) + 1);
            });

            addToCartBtn?.addEventListener('click', addCurrentItemToCart);

            cartItemsWrap?.addEventListener('click', function(event) {
                const button = event.target.closest('.remove-cart-line');

                if (!button) {
                    return;
                }

                const key = button.getAttribute('data-key');
                cart = cart.filter(line => line.key !== key);
                renderCart();
            });

            // checkoutForm?.addEventListener('submit', function(event) {
            //     if (cart.length === 0) {
            //         event.preventDefault();
            //         alert('يجب إضافة صنف واحد على الأقل.');
            //     }
            // });

            checkoutForm?.addEventListener('submit', function (event) {
    if (cart.length === 0) {
        event.preventDefault();
        alert('يجب إضافة صنف واحد على الأقل.');
        return;
    }

    if (window.openInvoiceEnabled && window.hasSelectedTable && !window.hasCurrentInvoice) {
        event.preventDefault();
        alert('يجب فتح فاتورة أو الانضمام لفاتورة موجودة قبل إرسال الطلب.');
    }
});

            orderType?.addEventListener('change', syncOrderTypeFields);

            document.querySelectorAll('.item-card').forEach(function(card) {
                card.addEventListener('click', function() {
                    const itemId = card.getAttribute('data-item-id');
                    const item = window.restaurantMenuItems[itemId];

                    if (!item) {
                        return;
                    }

                    renderItem(item);
                    modal.show();
                });
            });

            renderCart();
            syncOrderTypeFields();

            @if ($errors->any() || session('error'))
                const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
                cartModal.show();
            @endif
        })();
    </script>

</body>

</html>
