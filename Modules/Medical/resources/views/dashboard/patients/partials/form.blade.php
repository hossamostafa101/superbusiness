<div class="row g-4">
    <div class="col-md-4">
        <label class="form-label">كود المريض</label>
        <input
            type="text"
            name="patient_code"
            value="{{ old('patient_code', $patient?->patient_code) }}"
            class="form-control"
            dir="ltr"
            placeholder="يتم توليده تلقائيًا عند تركه فارغًا"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">الاسم الأول</label>
        <input
            type="text"
            name="first_name"
            value="{{ old('first_name', $patient?->first_name) }}"
            class="form-control"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">اسم العائلة</label>
        <input
            type="text"
            name="last_name"
            value="{{ old('last_name', $patient?->last_name) }}"
            class="form-control"
        >
    </div>

    <div class="col-md-6">
        <label class="form-label">الاسم الكامل</label>
        <input
            type="text"
            name="full_name"
            value="{{ old('full_name', $patient?->full_name) }}"
            class="form-control @error('full_name') is-invalid @enderror"
            placeholder="اختياري، ولو تركته فارغًا يتم تكوينه من الاسم الأول والعائلة"
        >
        @error('full_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">النوع</label>
        <select name="gender" class="form-select">
            <option value="unknown" @selected(old('gender', $patient?->gender ?? 'unknown') === 'unknown')>غير محدد</option>
            <option value="male" @selected(old('gender', $patient?->gender) === 'male')>ذكر</option>
            <option value="female" @selected(old('gender', $patient?->gender) === 'female')>أنثى</option>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">تاريخ الميلاد</label>
        <input
            type="date"
            name="birth_date"
            value="{{ old('birth_date', $patient?->birth_date?->format('Y-m-d')) }}"
            class="form-control"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">الهاتف</label>
        <input
            type="text"
            name="phone"
            value="{{ old('phone', $patient?->phone) }}"
            class="form-control"
            dir="ltr"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">واتساب</label>
        <input
            type="text"
            name="whatsapp_number"
            value="{{ old('whatsapp_number', $patient?->whatsapp_number) }}"
            class="form-control"
            dir="ltr"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">البريد الإلكتروني</label>
        <input
            type="email"
            name="email"
            value="{{ old('email', $patient?->email) }}"
            class="form-control"
            dir="ltr"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">الرقم القومي / الهوية</label>
        <input
            type="text"
            name="national_id"
            value="{{ old('national_id', $patient?->national_id) }}"
            class="form-control"
            dir="ltr"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">شركة التأمين</label>
        <input
            type="text"
            name="insurance_provider"
            value="{{ old('insurance_provider', $patient?->insurance_provider) }}"
            class="form-control"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">رقم التأمين</label>
        <input
            type="text"
            name="insurance_number"
            value="{{ old('insurance_number', $patient?->insurance_number) }}"
            class="form-control"
            dir="ltr"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">المدينة</label>
        <input
            type="text"
            name="city"
            value="{{ old('city', $patient?->city) }}"
            class="form-control"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">المنطقة</label>
        <input
            type="text"
            name="area"
            value="{{ old('area', $patient?->area) }}"
            class="form-control"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">الحالة</label>
        <select name="status" class="form-select">
            <option value="active" @selected(old('status', $patient?->status ?? 'active') === 'active')>نشط</option>
            <option value="inactive" @selected(old('status', $patient?->status) === 'inactive')>غير نشط</option>
            <option value="blocked" @selected(old('status', $patient?->status) === 'blocked')>محظور</option>
        </select>
    </div>

    <div class="col-12">
        <label class="form-label">العنوان</label>
        <textarea name="address" rows="3" class="form-control">{{ old('address', $patient?->address) }}</textarea>
    </div>

    <div class="col-md-4">
        <label class="form-label">اسم جهة الطوارئ</label>
        <input
            type="text"
            name="emergency_contact_name"
            value="{{ old('emergency_contact_name', $patient?->emergency_contact_name) }}"
            class="form-control"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">هاتف جهة الطوارئ</label>
        <input
            type="text"
            name="emergency_contact_phone"
            value="{{ old('emergency_contact_phone', $patient?->emergency_contact_phone) }}"
            class="form-control"
            dir="ltr"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">فصيلة الدم</label>
        <input
            type="text"
            name="blood_type"
            value="{{ old('blood_type', $patient?->blood_type) }}"
            class="form-control"
            placeholder="A+ / O-"
            dir="ltr"
        >
    </div>

    <div class="col-md-6">
        <label class="form-label">الحساسية</label>
        <textarea name="allergies" rows="3" class="form-control">{{ old('allergies', $patient?->allergies) }}</textarea>
    </div>

    <div class="col-md-6">
        <label class="form-label">الأمراض المزمنة</label>
        <textarea name="chronic_diseases" rows="3" class="form-control">{{ old('chronic_diseases', $patient?->chronic_diseases) }}</textarea>
    </div>

    <div class="col-12">
        <label class="form-label">ملاحظات</label>
        <textarea name="notes" rows="4" class="form-control">{{ old('notes', $patient?->notes) }}</textarea>
    </div>
</div>