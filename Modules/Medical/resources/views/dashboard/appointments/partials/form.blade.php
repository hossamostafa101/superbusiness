<div class="row g-4">
    <div class="col-md-4">
        <label class="form-label">المريض <span class="text-danger">*</span></label>

        <select name="patient_id" class="form-select @error('patient_id') is-invalid @enderror" required>
            <option value="">اختر المريض</option>

            @foreach ($patients as $patient)
                <option value="{{ $patient->id }}" @selected((string) old('patient_id', $appointment?->patient_id) === (string) $patient->id)>
                    {{ $patient->full_name }}
                    @if ($patient->phone)
                        - {{ $patient->phone }}
                    @endif
                    @if ($patient->patient_code)
                        - {{ $patient->patient_code }}
                    @endif
                </option>
            @endforeach
        </select>

        @error('patient_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <div class="form-text">
            إن لم يكن المريض موجودًا، أضفه من صفحة المرضى أولًا.
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label">الخدمة <span class="text-danger">*</span></label>

        <select name="service_id" id="appointmentServiceId"
            class="form-select @error('service_id') is-invalid @enderror" required>
            <option value="">اختر الخدمة</option>

            @foreach ($services as $service)
                <option value="{{ $service->id }}" data-price="{{ $service->price }}"
                    data-duration="{{ $service->duration_minutes }}" data-currency="{{ $service->currency }}"
                    @selected((string) old('service_id', $appointment?->service_id) === (string) $service->id)>
                    {{ $service->name }}
                    @if ($service->price !== null)
                        - {{ number_format((float) $service->price, 2) }} {{ $service->currency }}
                    @endif
                </option>
            @endforeach
        </select>

        @error('service_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">الطبيب / عضو الفريق</label>

        <select name="staff_id" id="appointmentStaffId" class="form-select">
            <option value="">بدون تحديد</option>

            @foreach ($staffMembers as $member)
                <option value="{{ $member->id }}" @selected((string) old('staff_id', $appointment?->staff_id) === (string) $member->id)>
                    {{ $member->name }}
                </option>
            @endforeach
        </select>
        <div class="form-text">
            سيتم رفض الحجز إذا كان خارج مواعيد عمل عضو الفريق أو متعارضًا مع حجز آخر.
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label">الفرع</label>

        <select name="branch_id" id="appointmentBranchId" class="form-select">
            <option value="">حسب الطبيب / بدون تحديد</option>

            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" @selected((string) old('branch_id', $appointment?->branch_id) === (string) $branch->id)>
                    {{ $branch->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">تاريخ الحجز <span class="text-danger">*</span></label>

        <input type="date" name="appointment_date" id="appointmentDate"
            value="{{ old('appointment_date', $appointment?->appointment_date?->format('Y-m-d') ?? today()->format('Y-m-d')) }}"
            class="form-control @error('appointment_date') is-invalid @enderror" required>

        @error('appointment_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-2">
        <label class="form-label">من <span class="text-danger">*</span></label>

        <input type="time" name="starts_at" id="appointmentStartsAt"
            value="{{ old('starts_at', $appointment?->starts_at ? \Illuminate\Support\Carbon::parse($appointment->starts_at)->format('H:i') : '') }}"
            class="form-control @error('starts_at') is-invalid @enderror" required>

        @error('starts_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">إلى</label>

        <input type="time" name="ends_at" id="appointmentEndsAt"
            value="{{ old('ends_at', $appointment?->ends_at ? \Illuminate\Support\Carbon::parse($appointment->ends_at)->format('H:i') : '') }}"
            class="form-control @error('ends_at') is-invalid @enderror">

        @error('ends_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <div class="form-text">
            يمكن تركها فارغة وسيتم حسابها من مدة الخدمة.
        </div>
    </div>


    <div class="col-12">
        <div class="border rounded-4 p-3 bg-light">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
                <div>
                    <div class="fw-bold">
                        الأوقات المتاحة
                    </div>

                    <div class="small text-muted">
                        اختر المريض والخدمة وعضو الفريق والتاريخ، ثم اضغط جلب الأوقات.
                    </div>
                </div>

                <button type="button" class="btn btn-outline-dark btn-sm" id="loadAvailableSlotsBtn">
                    <i class="bi bi-clock"></i>
                    جلب الأوقات المتاحة
                </button>
            </div>

            <div id="availableSlotsWrap" class="d-flex flex-wrap gap-2"></div>

            <div id="availableSlotsMessage" class="small text-muted mt-2"></div>
        </div>
    </div>


    <div class="col-md-3">
        <label class="form-label">حالة الحجز</label>

        <select name="status" class="form-select">
            <option value="pending" @selected(old('status', $appointment?->status ?? 'confirmed') === 'pending')>في الانتظار</option>
            <option value="confirmed" @selected(old('status', $appointment?->status ?? 'confirmed') === 'confirmed')>مؤكد</option>
            <option value="checked_in" @selected(old('status', $appointment?->status) === 'checked_in')>وصل</option>
            <option value="in_progress" @selected(old('status', $appointment?->status) === 'in_progress')>جاري الكشف</option>
            <option value="completed" @selected(old('status', $appointment?->status) === 'completed')>مكتمل</option>
            <option value="cancelled" @selected(old('status', $appointment?->status) === 'cancelled')>ملغي</option>
            <option value="no_show" @selected(old('status', $appointment?->status) === 'no_show')>لم يحضر</option>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">حالة الدفع</label>

        <select name="payment_status" class="form-select">
            <option value="unpaid" @selected(old('payment_status', $appointment?->payment_status ?? 'unpaid') === 'unpaid')>غير مدفوع</option>
            <option value="partially_paid" @selected(old('payment_status', $appointment?->payment_status) === 'partially_paid')>مدفوع جزئيًا</option>
            <option value="paid" @selected(old('payment_status', $appointment?->payment_status) === 'paid')>مدفوع</option>
            <option value="refunded" @selected(old('payment_status', $appointment?->payment_status) === 'refunded')>مسترد</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">ملاحظات المريض</label>

        <textarea name="notes" rows="4" class="form-control">{{ old('notes', $appointment?->notes) }}</textarea>
    </div>

    <div class="col-md-6">
        <label class="form-label">ملاحظات داخلية</label>

        <textarea name="internal_notes" rows="4" class="form-control">{{ old('internal_notes', $appointment?->internal_notes) }}</textarea>
    </div>
</div>






@push('scripts')
    <script>
        (function() {
            const loadBtn = document.getElementById('loadAvailableSlotsBtn');
            const slotsWrap = document.getElementById('availableSlotsWrap');
            const messageEl = document.getElementById('availableSlotsMessage');

            const serviceInput = document.getElementById('appointmentServiceId');
            const staffInput = document.getElementById('appointmentStaffId');
            const branchInput = document.getElementById('appointmentBranchId');
            const dateInput = document.getElementById('appointmentDate');
            const startsAtInput = document.getElementById('appointmentStartsAt');
            const endsAtInput = document.getElementById('appointmentEndsAt');

            if (!loadBtn || !slotsWrap) {
                return;
            }

            function setMessage(message, isError = false) {
                messageEl.textContent = message || '';
                messageEl.className = isError ? 'small text-danger mt-2' : 'small text-muted mt-2';
            }

            function clearSlots() {
                slotsWrap.innerHTML = '';
                setMessage('');
            }

            function renderSlots(slots) {
                slotsWrap.innerHTML = '';

                if (!slots.length) {
                    setMessage('لا توجد أوقات متاحة لهذا اليوم.', true);
                    return;
                }

                slots.forEach(function(slot) {
                    const button = document.createElement('button');

                    button.type = 'button';
                    button.className = 'btn btn-sm btn-light border rounded-pill';
                    button.textContent = slot.label;
                    button.dataset.startsAt = slot.starts_at;
                    button.dataset.endsAt = slot.ends_at;

                    button.addEventListener('click', function() {
                        startsAtInput.value = slot.starts_at;
                        endsAtInput.value = slot.ends_at;

                        document.querySelectorAll('#availableSlotsWrap button').forEach(function(btn) {
                            btn.classList.remove('btn-dark');
                            btn.classList.add('btn-light');
                        });

                        button.classList.remove('btn-light');
                        button.classList.add('btn-dark');
                    });

                    slotsWrap.appendChild(button);
                });

                setMessage('اختر وقتًا من الأوقات المتاحة.');
            }

            loadBtn.addEventListener('click', function() {
                clearSlots();

                const staffId = staffInput.value;
                const serviceId = serviceInput.value;
                const branchId = branchInput.value;
                const appointmentDate = dateInput.value;

                if (!staffId) {
                    setMessage('اختر عضو الفريق أولًا.', true);
                    return;
                }

                if (!serviceId) {
                    setMessage('اختر الخدمة أولًا.', true);
                    return;
                }

                if (!appointmentDate) {
                    setMessage('اختر تاريخ الحجز أولًا.', true);
                    return;
                }

                loadBtn.disabled = true;
                loadBtn.textContent = 'جاري التحميل...';

                const url = new URL(@json(route('app.medical.appointments.available-slots', $workspace)));

                url.searchParams.set('staff_id', staffId);
                url.searchParams.set('service_id', serviceId);
                url.searchParams.set('appointment_date', appointmentDate);

                if (branchId) {
                    url.searchParams.set('branch_id', branchId);
                }

                @isset($appointment)
                    @if ($appointment)
                        url.searchParams.set('ignore_appointment_id', @json($appointment->id));
                    @endif
                @endisset

                fetch(url.toString(), {
                        headers: {
                            'Accept': 'application/json',
                        }
                    })
                    .then(function(response) {
                        if (!response.ok) {
                            throw new Error('Failed to load slots.');
                        }

                        return response.json();
                    })
                    .then(function(data) {
                        renderSlots(data.slots || []);
                    })
                    .catch(function() {
                        setMessage('حدث خطأ أثناء جلب الأوقات المتاحة.', true);
                    })
                    .finally(function() {
                        loadBtn.disabled = false;
                        loadBtn.innerHTML = '<i class="bi bi-clock"></i> جلب الأوقات المتاحة';
                    });
            });

            [serviceInput, staffInput, branchInput, dateInput].forEach(function(input) {
                if (!input) {
                    return;
                }

                input.addEventListener('change', clearSlots);
            });
        })();
    </script>
@endpush
