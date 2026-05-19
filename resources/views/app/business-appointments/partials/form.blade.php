{{-- resources/views/app/business-appointments/partials/form.blade.php --}}
<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label">اختر عميل موجود</label>
        <select
            name="customer_id"
            id="customer_id"
            class="form-select @error('customer_id') is-invalid @enderror"
        >
            <option value="">بدون عميل / إدخال بيانات يدوي</option>

            @foreach($customers as $customer)
                <option
                    value="{{ $customer->id }}"
                    data-name="{{ $customer->name }}"
                    data-phone="{{ $customer->phone }}"
                    data-email="{{ $customer->email }}"
                    @selected((int) old('customer_id', $businessAppointment?->customer_id) === (int) $customer->id)
                >
                    {{ $customer->name }}
                    @if($customer->phone)
                        — {{ $customer->phone }}
                    @endif
                </option>
            @endforeach
        </select>

        @error('customer_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted">
            عند اختيار عميل، سيتم استخدام بياناته تلقائيًا.
        </small>
    </div>

    <div class="col-md-6">
        <label class="form-label">الخدمة</label>
        <select
            name="service_id"
            id="service_id"
            class="form-select @error('service_id') is-invalid @enderror"
        >
            <option value="">بدون خدمة</option>

            @foreach($services as $service)
                <option
                    value="{{ $service->id }}"
                    data-duration="{{ $service->duration_minutes }}"
                    @selected((int) old('service_id', $businessAppointment?->service_id) === (int) $service->id)
                >
                    {{ $service->name }}
                    —
                    {{ $service->duration_minutes }} دقيقة
                    @if($service->price !== null)
                        —
                        {{ number_format((float) $service->price, 2) }} {{ $service->currency }}
                    @endif
                </option>
            @endforeach
        </select>

        @error('service_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <hr>
        <h6 class="fw-bold mb-0">بيانات العميل اليدوية</h6>
        <small class="text-muted">
            استخدمها لو لم يتم اختيار عميل من القائمة.
        </small>
    </div>

    <div class="col-md-4">
        <label class="form-label">اسم العميل</label>
        <input
            type="text"
            name="customer_name"
            id="customer_name"
            value="{{ old('customer_name', $businessAppointment?->customer_name) }}"
            class="form-control @error('customer_name') is-invalid @enderror"
            placeholder="مثال: أحمد محمد"
        >

        @error('customer_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">هاتف العميل</label>
        <input
            type="text"
            name="customer_phone"
            id="customer_phone"
            value="{{ old('customer_phone', $businessAppointment?->customer_phone) }}"
            class="form-control @error('customer_phone') is-invalid @enderror"
            placeholder="2010xxxxxxxx"
        >

        @error('customer_phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">بريد العميل</label>
        <input
            type="email"
            name="customer_email"
            id="customer_email"
            value="{{ old('customer_email', $businessAppointment?->customer_email) }}"
            class="form-control @error('customer_email') is-invalid @enderror"
            placeholder="name@example.com"
        >

        @error('customer_email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <hr>
        <h6 class="fw-bold mb-0">وقت الموعد</h6>
    </div>

    <div class="col-md-4">
        <label class="form-label">تاريخ الموعد <span class="text-danger">*</span></label>
        <input
            type="date"
            name="appointment_date"
            value="{{ old('appointment_date', $businessAppointment?->appointment_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
            class="form-control @error('appointment_date') is-invalid @enderror"
            required
        >

        @error('appointment_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">وقت البداية <span class="text-danger">*</span></label>
        <input
            type="time"
            name="start_time"
            id="start_time"
            value="{{ old('start_time', $businessAppointment?->start_time ? \Illuminate\Support\Str::of($businessAppointment->start_time)->substr(0, 5) : '') }}"
            class="form-control @error('start_time') is-invalid @enderror"
            required
        >

        @error('start_time')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">وقت النهاية</label>
        <input
            type="time"
            name="end_time"
            id="end_time"
            value="{{ old('end_time', $businessAppointment?->end_time ? \Illuminate\Support\Str::of($businessAppointment->end_time)->substr(0, 5) : '') }}"
            class="form-control @error('end_time') is-invalid @enderror"
        >

        @error('end_time')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted">
            لو تركته فارغًا سيتم حسابه من مدة الخدمة.
        </small>
    </div>

    <div class="col-md-6">
        <label class="form-label">الحالة <span class="text-danger">*</span></label>

        @php
            $selectedStatus = old('status', $businessAppointment?->status ?? 'pending');
        @endphp

        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="pending" @selected($selectedStatus === 'pending')>قيد الانتظار</option>
            <option value="confirmed" @selected($selectedStatus === 'confirmed')>مؤكد</option>
            <option value="completed" @selected($selectedStatus === 'completed')>مكتمل</option>
            <option value="cancelled" @selected($selectedStatus === 'cancelled')>ملغي</option>
            <option value="no_show" @selected($selectedStatus === 'no_show')>لم يحضر</option>
        </select>

        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">المصدر</label>
        <input
            type="text"
            name="source"
            value="{{ old('source', $businessAppointment?->source ?? 'manual') }}"
            class="form-control @error('source') is-invalid @enderror"
            placeholder="manual, booking, whatsapp"
        >

        @error('source')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">ملاحظات</label>
        <textarea
            name="notes"
            rows="5"
            class="form-control @error('notes') is-invalid @enderror"
            placeholder="ملاحظات عن الموعد"
        >{{ old('notes', $businessAppointment?->notes) }}</textarea>

        @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const customerSelect = document.getElementById('customer_id');
        const serviceSelect = document.getElementById('service_id');
        const startInput = document.getElementById('start_time');
        const endInput = document.getElementById('end_time');

        const customerName = document.getElementById('customer_name');
        const customerPhone = document.getElementById('customer_phone');
        const customerEmail = document.getElementById('customer_email');

        function fillCustomerData() {
            const option = customerSelect?.selectedOptions?.[0];

            if (!option || !option.value) {
                return;
            }

            if (customerName && !customerName.value) {
                customerName.value = option.dataset.name || '';
            }

            if (customerPhone && !customerPhone.value) {
                customerPhone.value = option.dataset.phone || '';
            }

            if (customerEmail && !customerEmail.value) {
                customerEmail.value = option.dataset.email || '';
            }
        }

        function calculateEndTime() {
            if (!serviceSelect || !startInput || !endInput) {
                return;
            }

            if (!startInput.value) {
                return;
            }

            const option = serviceSelect.selectedOptions?.[0];

            if (!option || !option.dataset.duration) {
                return;
            }

            const duration = parseInt(option.dataset.duration, 10);

            if (!duration) {
                return;
            }

            const parts = startInput.value.split(':');

            if (parts.length < 2) {
                return;
            }

            const date = new Date();
            date.setHours(parseInt(parts[0], 10));
            date.setMinutes(parseInt(parts[1], 10));
            date.setSeconds(0);

            date.setMinutes(date.getMinutes() + duration);

            const hh = String(date.getHours()).padStart(2, '0');
            const mm = String(date.getMinutes()).padStart(2, '0');

            if (!endInput.value) {
                endInput.value = `${hh}:${mm}`;
            }
        }

        customerSelect?.addEventListener('change', fillCustomerData);
        serviceSelect?.addEventListener('change', calculateEndTime);
        startInput?.addEventListener('change', function () {
            endInput.value = '';
            calculateEndTime();
        });
    })();
</script>
@endpush