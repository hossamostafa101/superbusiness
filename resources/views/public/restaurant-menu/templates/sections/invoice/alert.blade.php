@if(!empty($selectedTable))
    <div class="alert alert-info border-0 rounded-4">
        <i class="bi bi-qr-code"></i>
        أنت تطلب من طاولة:
        <strong>{{ $selectedTable->number }}</strong>
        —
        {{ $selectedTable->name }}
    </div>
@endif

@if(session('invoice_pin'))
    <div class="alert alert-success rounded-4">
        <div class="fw-bold mb-1">
            تم فتح الجلسة بنجاح
        </div>

        <div>
            رقم PIN الخاص بالجلسة:
            <strong dir="ltr" style="font-size: 22px;">
                {{ session('invoice_pin') }}
            </strong>
        </div>

        <div class="small mt-2">
            احتفظ بهذا الرقم. أي شخص يريد الإضافة على نفس الجلسة سيحتاج إدخاله.
        </div>
    </div>
@endif

@if(!empty($openInvoiceEnabled) && !empty($selectedTable))
    @if(!empty($currentInvoice) && !empty($currentInvoiceGuest))
        <div class="alert alert-success rounded-4">
            <div class="d-flex justify-content-between align-items-start gap-3">
                <div>
                    <div class="fw-bold">
                        أنت متصل بجلسة الطاولة الحالية
                    </div>

                    <div class="small">
                        رقم الجلسة:
                        <strong>{{ $currentInvoice->invoice_number }}</strong>
                        —
                        تنتهي:
                        <strong>{{ $currentInvoice->expires_at?->format('H:i') }}</strong>
                    </div>
                </div>

                <a
                    href="{{ route('public.restaurant-menu.invoices.show', [$workspace, $branch, $currentInvoice]) }}"
                    class="btn btn-sm btn-outline-success"
                >
                    عرض الجلسة
                </a>
            </div>
        </div>
    @elseif(!empty($openInvoice))
        <div class="alert alert-warning rounded-4">
            <div class="fw-bold mb-1">
                توجد جلسة مفتوحة لهذه الطاولة
            </div>

            <div class="small mb-3">
                يمكنك فتح جلسة جديدة كضيف مستقل، أو الانضمام للجلسة الحالية باستخدام PIN.
            </div>

            <div class="d-flex gap-2 flex-wrap">
                @if($invoiceJoinPolicy === 'allow_with_pin')
                    <button type="button" class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#joinInvoiceModal">
                        الانضمام للجلسة الحالية
                    </button>
                @endif

                @if(!empty($allowNewInvoiceWhenTableBusy))
                    <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#openInvoiceModal">
                        فتح جلسة جديدة
                    </button>
                @else
                    <span class="badge bg-danger align-self-center">
                        لا يمكن فتح جلسة جديدة حتى إغلاق الحالية
                    </span>
                @endif
            </div>
        </div>
    @else
        <div class="alert alert-light border rounded-4">
            <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                <div>
                    <div class="fw-bold">
                        نظام جلسة الطاولة مفعل
                    </div>

                    <div class="small text-muted">
                        افتح جلسة للطاولة ثم أضف طلباتك عليها خلال مدة الجلسة.
                    </div>
                </div>

                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#openInvoiceModal">
                    فتح جلسة
                </button>
            </div>
        </div>
    @endif
@endif