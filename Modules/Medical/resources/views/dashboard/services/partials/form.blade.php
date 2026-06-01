<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label">اسم الخدمة <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $service?->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            required
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">نوع الخدمة <span class="text-danger">*</span></label>
        <select name="type" class="form-select" required>
            <option value="consultation" @selected(old('type', $service?->type ?? 'consultation') === 'consultation')>كشف</option>
            <option value="follow_up" @selected(old('type', $service?->type) === 'follow_up')>متابعة</option>
            <option value="procedure" @selected(old('type', $service?->type) === 'procedure')>إجراء طبي</option>
            <option value="lab_test" @selected(old('type', $service?->type) === 'lab_test')>تحليل</option>
            <option value="scan" @selected(old('type', $service?->type) === 'scan')>أشعة</option>
            <option value="operation" @selected(old('type', $service?->type) === 'operation')>عملية</option>
            <option value="session" @selected(old('type', $service?->type) === 'session')>جلسة</option>
            <option value="package" @selected(old('type', $service?->type) === 'package')>باقة</option>
            <option value="other" @selected(old('type', $service?->type) === 'other')>أخرى</option>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">الفرع</label>
        <select name="branch_id" class="form-select">
            <option value="">كل الفروع</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}" @selected((string) old('branch_id', $service?->branch_id) === (string) $branch->id)>
                    {{ $branch->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">القسم</label>
        <select name="department_id" class="form-select">
            <option value="">بدون قسم</option>
            @foreach($departments as $department)
                <option value="{{ $department->id }}" @selected((string) old('department_id', $service?->department_id) === (string) $department->id)>
                    {{ $department->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">التخصص</label>
        <select name="specialty_id" class="form-select">
            <option value="">بدون تخصص</option>
            @foreach($specialties as $specialty)
                <option value="{{ $specialty->id }}" @selected((string) old('specialty_id', $service?->specialty_id) === (string) $specialty->id)>
                    {{ $specialty->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label">السعر</label>
        <input
            type="number"
            step="0.01"
            min="0"
            name="price"
            value="{{ old('price', $service?->price) }}"
            class="form-control"
        >
    </div>

    <div class="col-md-2">
        <label class="form-label">العملة</label>
        <input
            type="text"
            name="currency"
            value="{{ old('currency', $service?->currency ?? 'EGP') }}"
            class="form-control"
            dir="ltr"
            required
        >
    </div>

    <div class="col-md-3">
        <label class="form-label">المدة بالدقائق</label>
        <input
            type="number"
            name="duration_minutes"
            value="{{ old('duration_minutes', $service?->duration_minutes) }}"
            class="form-control"
            min="1"
            max="1440"
            placeholder="مثال: 30"
        >
    </div>

    <div class="col-md-3">
        <label class="form-label">الترتيب</label>
        <input
            type="number"
            name="sort_order"
            value="{{ old('sort_order', $service?->sort_order ?? 0) }}"
            class="form-control"
            min="0"
        >
    </div>

    <div class="col-12">
        <label class="form-label">الوصف</label>
        <textarea
            name="description"
            rows="4"
            class="form-control"
        >{{ old('description', $service?->description) }}</textarea>
    </div>

    <div class="col-md-2">
        <label class="form-label d-block">تحتاج دكتور</label>
        <input type="hidden" name="requires_doctor" value="0">
        <div class="form-check form-switch mt-2">
            <input
                type="checkbox"
                name="requires_doctor"
                value="1"
                class="form-check-input"
                @checked(old('requires_doctor', $service?->requires_doctor ?? true))
            >
        </div>
    </div>

    <div class="col-md-2">
        <label class="form-label d-block">تحتاج موعد</label>
        <input type="hidden" name="requires_appointment" value="0">
        <div class="form-check form-switch mt-2">
            <input
                type="checkbox"
                name="requires_appointment"
                value="1"
                class="form-check-input"
                @checked(old('requires_appointment', $service?->requires_appointment ?? true))
            >
        </div>
    </div>

    <div class="col-md-2">
        <label class="form-label d-block">تحتاج عينة</label>
        <input type="hidden" name="requires_sample" value="0">
        <div class="form-check form-switch mt-2">
            <input
                type="checkbox"
                name="requires_sample"
                value="1"
                class="form-check-input"
                @checked(old('requires_sample', $service?->requires_sample ?? false))
            >
        </div>
    </div>

    <div class="col-md-2">
        <label class="form-label d-block">تحتاج تقرير</label>
        <input type="hidden" name="requires_report" value="0">
        <div class="form-check form-switch mt-2">
            <input
                type="checkbox"
                name="requires_report"
                value="1"
                class="form-check-input"
                @checked(old('requires_report', $service?->requires_report ?? false))
            >
        </div>
    </div>

    <div class="col-md-2">
        <label class="form-label d-block">مميزة</label>
        <input type="hidden" name="is_featured" value="0">
        <div class="form-check form-switch mt-2">
            <input
                type="checkbox"
                name="is_featured"
                value="1"
                class="form-check-input"
                @checked(old('is_featured', $service?->is_featured ?? false))
            >
        </div>
    </div>

    <div class="col-md-2">
        <label class="form-label d-block">نشطة</label>
        <input type="hidden" name="is_active" value="0">
        <div class="form-check form-switch mt-2">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                class="form-check-input"
                @checked(old('is_active', $service?->is_active ?? true))
            >
        </div>
    </div>
</div>