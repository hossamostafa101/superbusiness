{{-- resources/views/app/business-categories/partials/form.blade.php --}}
<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label">اسم التصنيف <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $businessCategory?->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            required
            placeholder="مثال: عبايات، إكسسوارات، عروض"
        >

        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">الترتيب</label>
        <input
            type="number"
            name="sort_order"
            value="{{ old('sort_order', $businessCategory?->sort_order ?? 0) }}"
            class="form-control @error('sort_order') is-invalid @enderror"
            min="0"
        >

        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted">الأقل يظهر أولًا.</small>
    </div>

    <div class="col-12">
        <label class="form-label">وصف التصنيف</label>
        <textarea
            name="description"
            rows="4"
            class="form-control @error('description') is-invalid @enderror"
            placeholder="وصف اختياري يظهر للعميل"
        >{{ old('description', $businessCategory?->description) }}</textarea>

        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <div class="form-check form-switch">
            <input type="hidden" name="is_active" value="0">

            <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                name="is_active"
                value="1"
                id="is_active"
                @checked(old('is_active', $businessCategory?->is_active ?? true))
            >

            <label class="form-check-label" for="is_active">
                التصنيف نشط
            </label>
        </div>
    </div>
</div>