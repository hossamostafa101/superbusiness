<div class="row g-4">
    <div class="col-md-4">
        <label class="form-label">المريض <span class="text-danger">*</span></label>

        <select name="patient_id" class="form-select @error('patient_id') is-invalid @enderror" required>
            <option value="">اختر المريض</option>

            @foreach($patients as $patient)
                <option
                    value="{{ $patient->id }}"
                    @selected((string) old('patient_id', $visit?->patient_id ?? request('patient_id')) === (string) $patient->id)
                >
                    {{ $patient->full_name }}
                    @if($patient->phone)
                        - {{ $patient->phone }}
                    @endif
                    @if($patient->patient_code)
                        - {{ $patient->patient_code }}
                    @endif
                </option>
            @endforeach
        </select>

        @error('patient_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">الطبيب / المختص</label>

        <select name="staff_id" class="form-select">
            <option value="">بدون تحديد</option>

            @foreach($staffMembers as $member)
                <option
                    value="{{ $member->id }}"
                    @selected((string) old('staff_id', $visit?->staff_id) === (string) $member->id)
                >
                    {{ $member->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">الخدمة</label>

        <select name="service_id" class="form-select">
            <option value="">بدون خدمة</option>

            @foreach($services as $service)
                <option
                    value="{{ $service->id }}"
                    @selected((string) old('service_id', $visit?->service_id) === (string) $service->id)
                >
                    {{ $service->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">الفرع</label>

        <select name="branch_id" class="form-select">
            <option value="">حسب الطبيب / بدون تحديد</option>

            @foreach($branches as $branch)
                <option
                    value="{{ $branch->id }}"
                    @selected((string) old('branch_id', $visit?->branch_id) === (string) $branch->id)
                >
                    {{ $branch->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">تاريخ الزيارة <span class="text-danger">*</span></label>

        <input
            type="date"
            name="visit_date"
            value="{{ old('visit_date', $visit?->visit_date?->format('Y-m-d') ?? today()->format('Y-m-d')) }}"
            class="form-control @error('visit_date') is-invalid @enderror"
            required
        >

        @error('visit_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">نوع الزيارة</label>

        <select name="visit_type" class="form-select">
            <option value="consultation" @selected(old('visit_type', $visit?->visit_type ?? 'consultation') === 'consultation')>كشف</option>
            <option value="follow_up" @selected(old('visit_type', $visit?->visit_type) === 'follow_up')>متابعة</option>
            <option value="procedure" @selected(old('visit_type', $visit?->visit_type) === 'procedure')>إجراء</option>
            <option value="lab" @selected(old('visit_type', $visit?->visit_type) === 'lab')>معمل</option>
            <option value="scan" @selected(old('visit_type', $visit?->visit_type) === 'scan')>أشعة</option>
            <option value="emergency" @selected(old('visit_type', $visit?->visit_type) === 'emergency')>طوارئ</option>
            <option value="other" @selected(old('visit_type', $visit?->visit_type) === 'other')>أخرى</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">وقت البداية</label>

        <input
            type="datetime-local"
            name="started_at"
            value="{{ old('started_at', $visit?->started_at?->format('Y-m-d\TH:i')) }}"
            class="form-control"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">وقت النهاية</label>

        <input
            type="datetime-local"
            name="ended_at"
            value="{{ old('ended_at', $visit?->ended_at?->format('Y-m-d\TH:i')) }}"
            class="form-control"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">الحالة</label>

        <select name="status" class="form-select">
            <option value="open" @selected(old('status', $visit?->status ?? 'open') === 'open')>مفتوحة</option>
            <option value="in_progress" @selected(old('status', $visit?->status) === 'in_progress')>جاري الكشف</option>
            <option value="completed" @selected(old('status', $visit?->status) === 'completed')>مكتملة</option>
            <option value="cancelled" @selected(old('status', $visit?->status) === 'cancelled')>ملغية</option>
        </select>
    </div>

    <div class="col-12">
        <label class="form-label">الشكوى الرئيسية</label>

        <textarea
            name="chief_complaint"
            rows="3"
            class="form-control"
        >{{ old('chief_complaint', $visit?->chief_complaint) }}</textarea>
    </div>

    <div class="col-md-6">
        <label class="form-label">التشخيص</label>

        <textarea
            name="diagnosis"
            rows="5"
            class="form-control"
        >{{ old('diagnosis', $visit?->diagnosis) }}</textarea>
    </div>

    <div class="col-md-6">
        <label class="form-label">خطة العلاج</label>

        <textarea
            name="treatment_plan"
            rows="5"
            class="form-control"
        >{{ old('treatment_plan', $visit?->treatment_plan) }}</textarea>
    </div>

    <div class="col-md-6">
        <label class="form-label">ملاحظات عامة</label>

        <textarea
            name="notes"
            rows="4"
            class="form-control"
        >{{ old('notes', $visit?->notes) }}</textarea>
    </div>

    <div class="col-md-6">
        <label class="form-label">ملاحظات داخلية</label>

        <textarea
            name="internal_notes"
            rows="4"
            class="form-control"
        >{{ old('internal_notes', $visit?->internal_notes) }}</textarea>
    </div>
</div>