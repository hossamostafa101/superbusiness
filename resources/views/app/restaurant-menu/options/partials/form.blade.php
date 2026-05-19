{{-- resources/views/app/restaurant-menu/options/partials/form.blade.php --}}
<div class="row g-4">
    <div class="col-12">
        <div class="alert alert-light border">
            <div class="fw-bold mb-1">
                الصنف:
                {{ $restaurantMenuItem->name }}
            </div>

            <div class="small text-muted">
                المجموعة:
                <strong>{{ $restaurantItemOptionGroup->name }}</strong>
                —
                @if($restaurantItemOptionGroup->type === 'single')
                    اختيار واحد فقط
                @else
                    اختيارات متعددة
                @endif
            </div>

            <div class="small text-muted mt-1">
                استخدم الخيارات مثل: جبنة إضافية، صوص، بدون سكر، لبن لوز، سبايسي.
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label">اسم الخيار <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $restaurantItemOption?->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            required
            placeholder="مثال: جبنة إضافية، باربكيو، بدون سكر"
        >

        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-2">
        <label class="form-label">السعر الإضافي</label>
        <input
            type="number"
            step="0.01"
            min="0"
            name="price"
            value="{{ old('price', $restaurantItemOption?->price ?? 0) }}"
            class="form-control @error('price') is-invalid @enderror"
            placeholder="0"
        >

        @error('price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted">
            اتركه 0 لو بدون تكلفة.
        </small>
    </div>

    <div class="col-md-2">
        <label class="form-label">العملة</label>
        <input
            type="text"
            name="currency"
            value="{{ old('currency', $restaurantItemOption?->currency ?? $restaurantMenuItem->currency ?? 'EGP') }}"
            class="form-control @error('currency') is-invalid @enderror"
            required
        >

        @error('currency')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-2">
        <label class="form-label">الترتيب</label>
        <input
            type="number"
            min="0"
            name="sort_order"
            value="{{ old('sort_order', $restaurantItemOption?->sort_order ?? 0) }}"
            class="form-control @error('sort_order') is-invalid @enderror"
        >

        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <div class="form-check form-switch">
            <input type="hidden" name="is_active" value="0">

            <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                name="is_active"
                value="1"
                id="is_active"
                @checked(old('is_active', $restaurantItemOption?->is_active ?? true))
            >

            <label class="form-check-label" for="is_active">
                الخيار نشط
            </label>
        </div>

        <small class="text-muted">
            الخيارات غير النشطة لا تظهر في المنيو العام.
        </small>
    </div>
</div>