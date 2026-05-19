{{-- resources/views/app/restaurant-menu/settings/edit.blade.php --}}
@extends('app.layouts.app')

@section('title', 'إعدادات المنيو')
@section('page_title', 'إعدادات المنيو')
@section('page_description', 'تحكم في طريقة استقبال الطلبات والفواتير المفتوحة للطاولات.')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card content-card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('app.restaurant-menu.settings.update', $workspace) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            طريقة استقبال الطلبات
                        </label>

                        @php
                            $orderingMode = old('restaurant_ordering_mode', $settings['restaurant_ordering_mode']);
                        @endphp

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="border rounded-4 p-3 d-block h-100">
                                    <input
                                        type="radio"
                                        name="restaurant_ordering_mode"
                                        value="single_order"
                                        @checked($orderingMode === 'single_order')
                                    >

                                    <span class="fw-bold ms-1">
                                        طلب منفصل
                                    </span>

                                    <div class="small text-muted mt-2">
                                        كل إرسال من العميل يتم تسجيله كطلب مستقل بدون فاتورة مفتوحة.
                                    </div>
                                </label>
                            </div>

                            <div class="col-md-6">
                                <label class="border rounded-4 p-3 d-block h-100 {{ ! $openInvoiceEnabled ? 'opacity-50' : '' }}">
                                    <input
                                        type="radio"
                                        name="restaurant_ordering_mode"
                                        value="open_invoice"
                                        @checked($orderingMode === 'open_invoice')
                                        @disabled(! $openInvoiceEnabled)
                                    >

                                    <span class="fw-bold ms-1">
                                        فاتورة مفتوحة للطاولة
                                    </span>

                                    <div class="small text-muted mt-2">
                                        يفتح العميل فاتورة للطاولة ويضيف عليها أكثر من طلب خلال مدة الجلسة.
                                    </div>

                                    @if(! $openInvoiceEnabled)
                                        <div class="badge bg-secondary mt-2">
                                            غير متاح في الباقة
                                        </div>
                                    @endif
                                </label>
                            </div>
                        </div>

                        @error('restaurant_ordering_mode')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            مدة الفاتورة المفتوحة بالدقائق
                        </label>

                        <input
                            type="number"
                            name="restaurant_invoice_duration_minutes"
                            value="{{ old('restaurant_invoice_duration_minutes', $settings['restaurant_invoice_duration_minutes']) }}"
                            class="form-control @error('restaurant_invoice_duration_minutes') is-invalid @enderror"
                            min="15"
                            max="{{ $invoiceDurationLimit === -1 ? 1440 : max(15, $invoiceDurationLimit) }}"
                        >

                        @error('restaurant_invoice_duration_minutes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="form-text">
                            الافتراضي 120 دقيقة.
                            @if($invoiceDurationLimit === -1)
                                الحد الأقصى في باقتك: غير محدود داخل النظام.
                            @else
                                الحد الأقصى في باقتك: {{ $invoiceDurationLimit }} دقيقة.
                            @endif
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            سياسة الانضمام لفاتورة مفتوحة من جهاز آخر
                        </label>

                        @php
                            $joinPolicy = old('restaurant_invoice_join_policy', $settings['restaurant_invoice_join_policy']);
                        @endphp

                        <select
                            name="restaurant_invoice_join_policy"
                            class="form-select @error('restaurant_invoice_join_policy') is-invalid @enderror"
                        >
                            <option value="allow_with_pin" @selected($joinPolicy === 'allow_with_pin') @disabled(! $joinWithPinEnabled)>
                                السماح بالانضمام باستخدام PIN
                            </option>

                            <option value="block_until_closed" @selected($joinPolicy === 'block_until_closed')>
                                منع الانضمام حتى إغلاق الفاتورة
                            </option>
                        </select>

                        @error('restaurant_invoice_join_policy')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        @if(! $joinWithPinEnabled)
                            <div class="form-text text-danger">
                                الانضمام باستخدام PIN غير متاح في الباقة الحالية.
                            </div>
                        @else
                            <div class="form-text">
                                عند السماح بالانضمام، سيحتاج العميل الثاني إلى إدخال PIN الخاص بالفاتورة.
                            </div>
                        @endif
                    </div>

                    <div class="mb-4">
                        <input type="hidden" name="restaurant_allow_new_invoice_when_table_busy" value="0">

                        <div class="form-check form-switch">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                role="switch"
                                name="restaurant_allow_new_invoice_when_table_busy"
                                value="1"
                                id="restaurant_allow_new_invoice_when_table_busy"
                                @checked(old('restaurant_allow_new_invoice_when_table_busy', $settings['restaurant_allow_new_invoice_when_table_busy']) == '1')
                            >

                            <label class="form-check-label fw-bold" for="restaurant_allow_new_invoice_when_table_busy">
                                السماح بفتح فاتورة جديدة إذا كانت الطاولة لديها فاتورة مفتوحة
                            </label>
                        </div>

                        <div class="form-text">
                            عند إيقافها، أي شخص يمسح QR لطاولة لديها فاتورة مفتوحة لن يستطيع فتح فاتورة جديدة حتى تنتهي أو يتم إغلاقها.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            خطوة تمديد الفاتورة بالدقائق
                        </label>

                        <input
                            type="number"
                            name="restaurant_invoice_extend_minutes_step"
                            value="{{ old('restaurant_invoice_extend_minutes_step', $settings['restaurant_invoice_extend_minutes_step']) }}"
                            class="form-control @error('restaurant_invoice_extend_minutes_step') is-invalid @enderror"
                            min="5"
                            max="240"
                        >

                        @error('restaurant_invoice_extend_minutes_step')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="form-text">
                            هذه القيمة تظهر في زر التمديد داخل لوحة الفواتير.
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('app.restaurant-menu.orders.index', $workspace) }}" class="btn btn-light">
                            إلغاء
                        </a>

                        <button type="submit" class="btn btn-primary">
                            حفظ الإعدادات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card content-card mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">
                    كيف تعمل الفاتورة المفتوحة؟
                </h6>

                <div class="small text-muted">
                    العميل يمسح QR الطاولة، يفتح فاتورة، يحصل على PIN مكوّن من رقمين ورقمين مثل
                    <strong dir="ltr">24-81</strong>.
                    بعدها يمكنه إضافة أكثر من طلب لنفس الفاتورة حتى تنتهي المدة.
                </div>
            </div>
        </div>

        <div class="card content-card">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">
                    الإعداد الحالي
                </h6>

                <div class="mb-3">
                    <small class="text-muted d-block">الوضع</small>
                    <strong>
                        @if($settings['restaurant_ordering_mode'] === 'open_invoice')
                            فاتورة مفتوحة
                        @else
                            طلب منفصل
                        @endif
                    </strong>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">مدة الجلسة</small>
                    <strong>{{ $settings['restaurant_invoice_duration_minutes'] }} دقيقة</strong>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">سياسة الانضمام</small>
                    <strong>
                        @if($settings['restaurant_invoice_join_policy'] === 'allow_with_pin')
                            باستخدام PIN
                        @else
                            ممنوع حتى الإغلاق
                        @endif
                    </strong>
                </div>

                <div class="mb-0">
                    <small class="text-muted d-block">فاتورة جديدة عند انشغال الطاولة</small>
                    <strong>
                        {{ $settings['restaurant_allow_new_invoice_when_table_busy'] == '1' ? 'مسموح' : 'ممنوع' }}
                    </strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection