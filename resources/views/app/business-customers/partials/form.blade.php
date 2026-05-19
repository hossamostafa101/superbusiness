{{-- resources/views/app/business-customers/partials/form.blade.php --}}
<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label">اسم العميل <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $businessCustomer?->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            required
            placeholder="مثال: أحمد محمد"
        >

        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">الهاتف</label>
        <input
            type="text"
            name="phone"
            value="{{ old('phone', $businessCustomer?->phone) }}"
            class="form-control @error('phone') is-invalid @enderror"
            placeholder="2010xxxxxxxx"
        >

        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">البريد الإلكتروني</label>
        <input
            type="email"
            name="email"
            value="{{ old('email', $businessCustomer?->email) }}"
            class="form-control @error('email') is-invalid @enderror"
            placeholder="name@example.com"
        >

        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">النوع</label>
        @php
            $selectedGender = old('gender', $businessCustomer?->gender);
        @endphp

        <select name="gender" class="form-select @error('gender') is-invalid @enderror">
            <option value="">غير محدد</option>
            <option value="male" @selected($selectedGender === 'male')>ذكر</option>
            <option value="female" @selected($selectedGender === 'female')>أنثى</option>
        </select>

        @error('gender')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">تاريخ الميلاد</label>
        <input
            type="date"
            name="birthdate"
            value="{{ old('birthdate', $businessCustomer?->birthdate?->format('Y-m-d')) }}"
            class="form-control @error('birthdate') is-invalid @enderror"
        >

        @error('birthdate')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">المصدر</label>
        <input
            type="text"
            name="source"
            value="{{ old('source', $businessCustomer?->source ?? 'manual') }}"
            class="form-control @error('source') is-invalid @enderror"
            placeholder="manual, whatsapp, booking, public_page"
        >

        @error('source')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">الحالة <span class="text-danger">*</span></label>

        @php
            $selectedStatus = old('status', $businessCustomer?->status ?? 'active');
        @endphp

        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="active" @selected($selectedStatus === 'active')>نشط</option>
            <option value="inactive" @selected($selectedStatus === 'inactive')>غير نشط</option>
            <option value="blocked" @selected($selectedStatus === 'blocked')>محظور</option>
        </select>

        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">ملاحظات</label>
        <textarea
            name="notes"
            rows="5"
            class="form-control @error('notes') is-invalid @enderror"
            placeholder="أي ملاحظات عن العميل"
        >{{ old('notes', $businessCustomer?->notes) }}</textarea>

        @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>