<div class="row g-4">
    <div class="col-md-5">
        <label class="form-label">الاسم <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $staff?->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            required
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">الدور <span class="text-danger">*</span></label>
        <select name="role" class="form-select" required>
            <option value="doctor" @selected(old('role', $staff?->role ?? 'doctor') === 'doctor')>طبيب</option>
            <option value="nurse" @selected(old('role', $staff?->role) === 'nurse')>تمريض</option>
            <option value="lab_technician" @selected(old('role', $staff?->role) === 'lab_technician')>فني معمل</option>
            <option value="radiology_technician" @selected(old('role', $staff?->role) === 'radiology_technician')>فني أشعة</option>
            <option value="receptionist" @selected(old('role', $staff?->role) === 'receptionist')>استقبال</option>
            <option value="accountant" @selected(old('role', $staff?->role) === 'accountant')>محاسب</option>
            <option value="admin" @selected(old('role', $staff?->role) === 'admin')>مدير</option>
            <option value="other" @selected(old('role', $staff?->role) === 'other')>أخرى</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">ربط بحساب مستخدم</label>
        <select name="user_id" class="form-select">
            <option value="">بدون ربط</option>
            @foreach($workspaceUsers as $user)
                <option value="{{ $user->id }}" @selected((string) old('user_id', $staff?->user_id) === (string) $user->id)>
                    {{ $user->name }} - {{ $user->email }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">الفرع</label>
        <select name="branch_id" class="form-select">
            <option value="">كل الفروع</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}" @selected((string) old('branch_id', $staff?->branch_id) === (string) $branch->id)>
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
                <option value="{{ $department->id }}" @selected((string) old('department_id', $staff?->department_id) === (string) $department->id)>
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
                <option value="{{ $specialty->id }}" @selected((string) old('specialty_id', $staff?->specialty_id) === (string) $specialty->id)>
                    {{ $specialty->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">اللقب / المسمى</label>
        <input
            type="text"
            name="title"
            value="{{ old('title', $staff?->title) }}"
            class="form-control"
            placeholder="مثال: استشاري أمراض جلدية"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">الهاتف</label>
        <input
            type="text"
            name="phone"
            value="{{ old('phone', $staff?->phone) }}"
            class="form-control"
            dir="ltr"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">واتساب</label>
        <input
            type="text"
            name="whatsapp_number"
            value="{{ old('whatsapp_number', $staff?->whatsapp_number) }}"
            class="form-control"
            dir="ltr"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">البريد الإلكتروني</label>
        <input
            type="email"
            name="email"
            value="{{ old('email', $staff?->email) }}"
            class="form-control"
            dir="ltr"
        >
    </div>

    <div class="col-md-2">
        <label class="form-label">رسوم الكشف</label>
        <input
            type="number"
            step="0.01"
            min="0"
            name="consultation_fee"
            value="{{ old('consultation_fee', $staff?->consultation_fee) }}"
            class="form-control"
        >
    </div>

    <div class="col-md-2">
        <label class="form-label">رسوم المتابعة</label>
        <input
            type="number"
            step="0.01"
            min="0"
            name="follow_up_fee"
            value="{{ old('follow_up_fee', $staff?->follow_up_fee) }}"
            class="form-control"
        >
    </div>

    <div class="col-md-2">
        <label class="form-label">العملة</label>
        <input
            type="text"
            name="currency"
            value="{{ old('currency', $staff?->currency ?? 'EGP') }}"
            class="form-control"
            dir="ltr"
            required
        >
    </div>

    <div class="col-md-2">
        <label class="form-label">مدة الموعد</label>
        <input
            type="number"
            name="default_slot_minutes"
            value="{{ old('default_slot_minutes', $staff?->default_slot_minutes) }}"
            class="form-control"
            min="5"
            max="240"
            placeholder="30"
        >
    </div>

    <div class="col-md-2">
        <label class="form-label">الترتيب</label>
        <input
            type="number"
            name="sort_order"
            value="{{ old('sort_order', $staff?->sort_order ?? 0) }}"
            class="form-control"
            min="0"
        >
    </div>

    <div class="col-12">
        <label class="form-label">نبذة</label>
        <textarea
            name="bio"
            rows="4"
            class="form-control"
            placeholder="نبذة قصيرة عن الطبيب أو عضو الفريق"
        >{{ old('bio', $staff?->bio) }}</textarea>
    </div>

    <div class="col-md-4">
        <label class="form-label d-block">يقبل الحجز أونلاين</label>
        <input type="hidden" name="accepts_online_booking" value="0">
        <div class="form-check form-switch mt-2">
            <input
                type="checkbox"
                name="accepts_online_booking"
                value="1"
                class="form-check-input"
                @checked(old('accepts_online_booking', $staff?->accepts_online_booking ?? true))
            >
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label d-block">مميز</label>
        <input type="hidden" name="is_featured" value="0">
        <div class="form-check form-switch mt-2">
            <input
                type="checkbox"
                name="is_featured"
                value="1"
                class="form-check-input"
                @checked(old('is_featured', $staff?->is_featured ?? false))
            >
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label d-block">نشط</label>
        <input type="hidden" name="is_active" value="0">
        <div class="form-check form-switch mt-2">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                class="form-check-input"
                @checked(old('is_active', $staff?->is_active ?? true))
            >
        </div>
    </div>
</div>