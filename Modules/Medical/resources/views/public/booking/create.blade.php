<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حجز موعد - {{ $settings->display_name ?: $workspace->name }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: "Cairo", system-ui, sans-serif;
            background: #f5f7fb;
            color: #0f172a;
        }

        .booking-page {
            max-width: 560px;
            margin: 0 auto;
            padding: 18px 12px 40px;
        }

        .hero-card {
            border-radius: 28px;
            padding: 24px;
            background: linear-gradient(135deg, {{ $settings->primary_color ?: '#2563eb' }}, {{ $settings->secondary_color ?: '#0f172a' }});
            color: #fff;
            margin-bottom: 16px;
        }

        .hero-card h1 {
            font-size: 26px;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .hero-card p {
            margin: 0;
            opacity: .86;
            line-height: 1.7;
            font-size: 14px;
        }

        .booking-card {
            border: 0;
            border-radius: 28px;
            box-shadow: 0 16px 40px rgba(15, 23, 42, .08);
        }

        .form-control,
        .form-select {
            border-radius: 16px;
            min-height: 46px;
        }

        .slot-btn {
            border-radius: 999px;
            padding: 8px 13px;
            font-size: 13px;
            font-weight: 800;
        }

        .submit-btn {
            min-height: 52px;
            border-radius: 18px;
            font-weight: 900;
            background: {{ $settings->primary_color ?: '#2563eb' }};
            border-color: {{ $settings->primary_color ?: '#2563eb' }};
        }
    </style>
</head>
<body>

<div class="booking-page">
    <div class="hero-card">
        <h1>
            {{ $settings->display_name ?: $workspace->name }}
        </h1>

        <p>
            {{ $settings->description ?: 'احجز موعدك بسهولة واختر الوقت المناسب لك.' }}
        </p>
    </div>

    <div class="card booking-card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('public.medical.booking.store', $workspace) }}">
                @csrf

                <div class="row g-3">
                    @if($branches->count() > 1)
                        <div class="col-12">
                            <label class="form-label">الفرع</label>

                            <select name="branch_id" id="branchId" class="form-select">
                                <option value="">اختر الفرع</option>

                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" @selected(old('branch_id') == $branch->id)>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="branch_id" id="branchId" value="{{ $branches->first()?->id }}">
                    @endif

                    <div class="col-12">
                        <label class="form-label">الخدمة <span class="text-danger">*</span></label>

                        <select name="service_id" id="serviceId" class="form-select @error('service_id') is-invalid @enderror" required>
                            <option value="">اختر الخدمة</option>

                            @foreach($services as $service)
                                <option value="{{ $service->id }}" @selected(old('service_id') == $service->id)>
                                    {{ $service->name }}
                                    @if($service->price !== null)
                                        - {{ number_format((float) $service->price, 2) }} {{ $service->currency }}
                                    @endif
                                </option>
                            @endforeach
                        </select>

                        <div id="staffServiceInfo" class="small text-muted mt-2"></div>

                        @error('service_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if(old('service_id'))
    loadStaffByService();

    setTimeout(function () {
        const oldStaffId = @json(old('staff_id'));

        if (oldStaffId) {
            staffInput.value = oldStaffId;
            updateStaffServiceInfo();
        }
    }, 450);
@endif

                    <div class="col-12">
                        <label class="form-label">الطبيب / المختص <span class="text-danger">*</span></label>

                        {{-- <select name="staff_id" id="staffId" class="form-select @error('staff_id') is-invalid @enderror" required>
                            <option value="">اختر الطبيب أو المختص</option>

                            @foreach($staffMembers as $member)
                                <option value="{{ $member->id }}" @selected(old('staff_id') == $member->id)>
                                    {{ $member->name }}
                                    @if($member->title)
                                        - {{ $member->title }}
                                    @endif
                                </option>
                            @endforeach
                        </select> --}}
                        <select name="staff_id" id="staffId" class="form-select @error('staff_id') is-invalid @enderror" required>
    <option value="">اختر الخدمة أولًا</option>
</select>

                        @error('staff_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">تاريخ الموعد <span class="text-danger">*</span></label>

                        <input
                            type="date"
                            name="appointment_date"
                            id="appointmentDate"
                            value="{{ old('appointment_date', today()->format('Y-m-d')) }}"
                            min="{{ today()->format('Y-m-d') }}"
                            class="form-control @error('appointment_date') is-invalid @enderror"
                            required
                        >

                        @error('appointment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div class="border rounded-4 p-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                                <div>
                                    <div class="fw-bold">
                                        الأوقات المتاحة
                                    </div>

                                    <div class="small text-muted">
                                        اختر الخدمة والطبيب والتاريخ ثم اضغط جلب الأوقات.
                                    </div>
                                </div>

                                <button type="button" class="btn btn-outline-dark btn-sm" id="loadSlotsBtn">
                                    <i class="bi bi-clock"></i>
                                    جلب
                                </button>
                            </div>

                            <div id="slotsWrap" class="d-flex flex-wrap gap-2"></div>
                            <div id="slotsMessage" class="small text-muted mt-2"></div>
                        </div>

                        <input type="hidden" name="starts_at" id="startsAt" value="{{ old('starts_at') }}">
                        <input type="hidden" name="ends_at" id="endsAt" value="{{ old('ends_at') }}">

                        @error('starts_at')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">الاسم <span class="text-danger">*</span></label>

                        <input
                            type="text"
                            name="patient_name"
                            value="{{ old('patient_name') }}"
                            class="form-control @error('patient_name') is-invalid @enderror"
                            required
                        >

                        @error('patient_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">رقم الهاتف <span class="text-danger">*</span></label>

                        <input
                            type="text"
                            name="patient_phone"
                            value="{{ old('patient_phone') }}"
                            class="form-control @error('patient_phone') is-invalid @enderror"
                            dir="ltr"
                            required
                        >

                        @error('patient_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">البريد الإلكتروني</label>

                        <input
                            type="email"
                            name="patient_email"
                            value="{{ old('patient_email') }}"
                            class="form-control @error('patient_email') is-invalid @enderror"
                            dir="ltr"
                        >

                        @error('patient_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">ملاحظات</label>

                        <textarea name="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
                    </div>

                    <div class="col-12 d-grid mt-2">
                        <button class="btn btn-primary submit-btn">
                            تأكيد الحجز
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    const loadBtn = document.getElementById('loadSlotsBtn');
    const slotsWrap = document.getElementById('slotsWrap');
    const slotsMessage = document.getElementById('slotsMessage');

    const branchInput = document.getElementById('branchId');
    const serviceInput = document.getElementById('serviceId');
    const staffInput = document.getElementById('staffId');
    const dateInput = document.getElementById('appointmentDate');
    const startsAtInput = document.getElementById('startsAt');
    const endsAtInput = document.getElementById('endsAt');


    const staffServiceInfo = document.getElementById('staffServiceInfo');


    function setMessage(message, error = false) {
        slotsMessage.textContent = message || '';
        slotsMessage.className = error ? 'small text-danger mt-2' : 'small text-muted mt-2';
    }

    function clearSlots() {
        slotsWrap.innerHTML = '';
        startsAtInput.value = '';
        endsAtInput.value = '';
        setMessage('');
    }

    function renderSlots(slots) {
        slotsWrap.innerHTML = '';

        if (!slots.length) {
            setMessage('لا توجد أوقات متاحة لهذا اليوم.', true);
            return;
        }

        slots.forEach(function (slot) {
            const btn = document.createElement('button');

            btn.type = 'button';
            btn.className = 'btn btn-light border slot-btn';
            btn.textContent = slot.label;

            btn.addEventListener('click', function () {
                startsAtInput.value = slot.starts_at;
                endsAtInput.value = slot.ends_at;

                document.querySelectorAll('#slotsWrap button').forEach(function (button) {
                    button.classList.remove('btn-dark');
                    button.classList.add('btn-light');
                });

                btn.classList.remove('btn-light');
                btn.classList.add('btn-dark');
            });

            slotsWrap.appendChild(btn);
        });

        setMessage('اختر وقتًا مناسبًا.');
    }

    





    function resetStaffSelect(message = 'اختر الخدمة أولًا') {
    staffInput.innerHTML = '';

    const option = document.createElement('option');
    option.value = '';
    option.textContent = message;

    staffInput.appendChild(option);

    if (staffServiceInfo) {
        staffServiceInfo.textContent = '';
    }
}

function renderStaffOptions(staffList) {
    resetStaffSelect('اختر الطبيب أو المختص');

    staffList.forEach(function (member) {
        const option = document.createElement('option');

        option.value = member.id;

        let label = member.name;

        if (member.title) {
            label += ' - ' + member.title;
        } else if (member.specialty) {
            label += ' - ' + member.specialty;
        }

        option.textContent = label;

        option.dataset.price = member.price ?? '';
        option.dataset.currency = member.currency ?? '';
        option.dataset.duration = member.duration ?? '';

        staffInput.appendChild(option);
    });

    if (!staffList.length) {
        resetStaffSelect('لا يوجد مختصون متاحون لهذه الخدمة');
    }
}

function loadStaffByService() {
    clearSlots();
    resetStaffSelect('جاري تحميل المختصين...');

    const serviceId = serviceInput.value;

    if (!serviceId) {
        resetStaffSelect('اختر الخدمة أولًا');
        return;
    }

    const url = new URL(@json(route('public.medical.booking.staff-by-service', $workspace)));

    url.searchParams.set('service_id', serviceId);

    if (branchInput && branchInput.value) {
        url.searchParams.set('branch_id', branchInput.value);
    }

    fetch(url.toString(), {
        headers: {
            'Accept': 'application/json',
        }
    })
        .then(function (response) {
            if (!response.ok) {
                throw new Error('Failed');
            }

            return response.json();
        })
        .then(function (data) {
            renderStaffOptions(data.staff || []);
        })
        .catch(function () {
            resetStaffSelect('تعذر تحميل المختصين');
        });
}

function updateStaffServiceInfo() {
    if (!staffServiceInfo) {
        return;
    }

    const selected = staffInput.options[staffInput.selectedIndex];

    if (!selected || !selected.value) {
        staffServiceInfo.textContent = '';
        return;
    }

    const price = selected.dataset.price;
    const currency = selected.dataset.currency;
    const duration = selected.dataset.duration;

    let text = '';

    if (price) {
        text += 'السعر المتوقع: ' + Number(price).toFixed(2) + ' ' + currency;
    }

    if (duration) {
        if (text) {
            text += ' - ';
        }

        text += 'المدة: ' + duration + ' دقيقة';
    }

    staffServiceInfo.textContent = text;
}









    loadBtn.addEventListener('click', function () {
        clearSlots();

        if (!serviceInput.value) {
            setMessage('اختر الخدمة أولًا.', true);
            return;
        }

        if (!staffInput.value) {
            setMessage('اختر الطبيب أو المختص أولًا.', true);
            return;
        }

        if (!dateInput.value) {
            setMessage('اختر التاريخ أولًا.', true);
            return;
        }

        loadBtn.disabled = true;
        loadBtn.textContent = 'جاري...';

        const url = new URL(@json(route('public.medical.booking.available-slots', $workspace)));

        url.searchParams.set('service_id', serviceInput.value);
        url.searchParams.set('staff_id', staffInput.value);
        url.searchParams.set('appointment_date', dateInput.value);

        if (branchInput && branchInput.value) {
            url.searchParams.set('branch_id', branchInput.value);
        }

        fetch(url.toString(), {
            headers: {
                'Accept': 'application/json',
            }
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Failed');
                }

                return response.json();
            })
            .then(function (data) {
                renderSlots(data.slots || []);
            })
            .catch(function () {
                setMessage('حدث خطأ أثناء جلب الأوقات.', true);
            })
            .finally(function () {
                loadBtn.disabled = false;
                loadBtn.innerHTML = '<i class="bi bi-clock"></i> جلب';
            });
    });

    [branchInput, serviceInput, staffInput, dateInput].forEach(function (input) {
        // if (input) {
        //     input.addEventListener('change', clearSlots);
        // }
        if (serviceInput) {
    serviceInput.addEventListener('change', function () {
        loadStaffByService();
    });
}

if (branchInput) {
    branchInput.addEventListener('change', function () {
        loadStaffByService();
    });
}

if (staffInput) {
    staffInput.addEventListener('change', function () {
        clearSlots();
        updateStaffServiceInfo();
    });
}

if (dateInput) {
    dateInput.addEventListener('change', clearSlots);
}
    });
})();
</script>

</body>
</html>