<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label">اسم الفرع <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $branch?->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            required
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">الهاتف</label>
        <input
            type="text"
            name="phone"
            value="{{ old('phone', $branch?->phone) }}"
            class="form-control"
            dir="ltr"
        >
    </div>

    <div class="col-md-3">
        <label class="form-label">واتساب</label>
        <input
            type="text"
            name="whatsapp_number"
            value="{{ old('whatsapp_number', $branch?->whatsapp_number) }}"
            class="form-control"
            dir="ltr"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">البريد الإلكتروني</label>
        <input
            type="email"
            name="email"
            value="{{ old('email', $branch?->email) }}"
            class="form-control"
            dir="ltr"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">المدينة</label>
        <input
            type="text"
            name="city"
            value="{{ old('city', $branch?->city) }}"
            class="form-control"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">المنطقة</label>
        <input
            type="text"
            name="area"
            value="{{ old('area', $branch?->area) }}"
            class="form-control"
        >
    </div>

    <div class="col-12">
        <label class="form-label">العنوان</label>
        <textarea
            name="address"
            rows="3"
            class="form-control"
        >{{ old('address', $branch?->address) }}</textarea>
    </div>

    <div class="col-12">
        <label class="form-label">رابط Google Maps</label>
        <input
            type="url"
            name="google_maps_url"
            value="{{ old('google_maps_url', $branch?->google_maps_url) }}"
            class="form-control"
            dir="ltr"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">الترتيب</label>
        <input
            type="number"
            name="sort_order"
            value="{{ old('sort_order', $branch?->sort_order ?? 0) }}"
            class="form-control"
            min="0"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label d-block">فرع رئيسي</label>
        <input type="hidden" name="is_main" value="0">

        <div class="form-check form-switch mt-2">
            <input
                type="checkbox"
                name="is_main"
                value="1"
                class="form-check-input"
                @checked(old('is_main', $branch?->is_main ?? false))
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
                @checked(old('is_active', $branch?->is_active ?? true))
            >
        </div>
    </div>
</div>