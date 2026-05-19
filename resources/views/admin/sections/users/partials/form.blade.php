<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">الاسم <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $user?->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            required
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
        <input
            type="email"
            name="email"
            value="{{ old('email', $user?->email) }}"
            class="form-control @error('email') is-invalid @enderror"
            required
        >
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">الهاتف</label>
        <input
            type="text"
            name="phone"
            value="{{ old('phone', $user?->phone) }}"
            class="form-control @error('phone') is-invalid @enderror"
        >
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">الحالة <span class="text-danger">*</span></label>
        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="active" @selected(old('status', $user?->status ?? 'active') === 'active')>نشط</option>
            <option value="pending" @selected(old('status', $user?->status) === 'pending')>قيد الانتظار</option>
            <option value="suspended" @selected(old('status', $user?->status) === 'suspended')>موقوف</option>
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">
            كلمة المرور
            @unless($isEdit)
                <span class="text-danger">*</span>
            @endunless
        </label>
        <input
            type="password"
            name="password"
            class="form-control @error('password') is-invalid @enderror"
            autocomplete="new-password"
            @required(! $isEdit)
        >
        @if($isEdit)
            <small class="text-body-secondary">اتركها فارغة إذا لم ترغب في تغييرها.</small>
        @endif
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">
            تأكيد كلمة المرور
            @unless($isEdit)
                <span class="text-danger">*</span>
            @endunless
        </label>
        <input
            type="password"
            name="password_confirmation"
            class="form-control"
            autocomplete="new-password"
            @required(! $isEdit)
        >
    </div>

    <div class="col-md-6">
        <label class="form-label">المسمى الوظيفي</label>
        <input
            type="text"
            name="job_title"
            value="{{ old('job_title', $user?->adminProfile?->job_title) }}"
            class="form-control @error('job_title') is-invalid @enderror"
        >
        @error('job_title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">الأدوار</label>

        <div class="border rounded p-3 @error('roles') border-danger @enderror">
            @forelse($roles as $role)
                <div class="form-check mb-2">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="roles[]"
                        value="{{ $role->id }}"
                        id="role_{{ $role->id }}"
                        @checked(in_array($role->id, $selectedRoles ?? []))
                    >
                    <label class="form-check-label" for="role_{{ $role->id }}">
                        {{ $role->name }}
                    </label>
                </div>
            @empty
                <p class="text-body-secondary mb-0">لا توجد أدوار متاحة.</p>
            @endforelse
        </div>

        @error('roles')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror

        @error('roles.*')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">ملاحظات</label>
        <textarea
            name="notes"
            rows="4"
            class="form-control @error('notes') is-invalid @enderror"
        >{{ old('notes', $user?->adminProfile?->notes) }}</textarea>

        @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>