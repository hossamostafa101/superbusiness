{{-- resources/views/app/restaurant-menu/variants/partials/form.blade.php --}}
<div class="row g-4">
    <div class="col-12">
        <div class="alert alert-light border">
            <div class="fw-bold mb-1">
                الصنف:
                {{ $restaurantMenuItem->name }}
            </div>

            <div class="small text-muted">
                استخدم الـ Variant للأحجام أو النسخ المختلفة مثل: صغير، وسط، كبير، سنجل، دبل.
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label">اسم الـ Variant <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $restaurantItemVariant?->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            required
            placeholder="مثال: Small, Medium, Large"
        >

        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-2">
        <label class="form-label">السعر <span class="text-danger">*</span></label>
        <input
            type="number"
            step="0.01"
            min="0"
            name="price"
            value="{{ old('price', $restaurantItemVariant?->price ?? $restaurantMenuItem->price) }}"
            class="form-control @error('price') is-invalid @enderror"
            required
        >

        @error('price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-2">
        <label class="form-label">سعر العرض</label>
        <input
            type="number"
            step="0.01"
            min="0"
            name="sale_price"
            value="{{ old('sale_price', $restaurantItemVariant?->sale_price) }}"
            class="form-control @error('sale_price') is-invalid @enderror"
        >

        @error('sale_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-2">
        <label class="form-label">العملة</label>
        <input
            type="text"
            name="currency"
            value="{{ old('currency', $restaurantItemVariant?->currency ?? $restaurantMenuItem->currency ?? 'EGP') }}"
            class="form-control @error('currency') is-invalid @enderror"
            required
        >

        @error('currency')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">الترتيب</label>
        <input
            type="number"
            min="0"
            name="sort_order"
            value="{{ old('sort_order', $restaurantItemVariant?->sort_order ?? 0) }}"
            class="form-control @error('sort_order') is-invalid @enderror"
        >

        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted">
            الأقل يظهر أولًا.
        </small>
    </div>

    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check form-switch mb-2">
            <input type="hidden" name="is_default" value="0">

            <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                name="is_default"
                value="1"
                id="is_default"
                @checked(old('is_default', $restaurantItemVariant?->is_default ?? false))
            >

            <label class="form-check-label" for="is_default">
                Variant افتراضي
            </label>
        </div>
    </div>

    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check form-switch mb-2">
            <input type="hidden" name="is_active" value="0">

            <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                name="is_active"
                value="1"
                id="is_active"
                @checked(old('is_active', $restaurantItemVariant?->is_active ?? true))
            >

            <label class="form-check-label" for="is_active">
                نشط
            </label>
        </div>
    </div>
</div>