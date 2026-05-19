{{-- resources/views/app/business-services/partials/form.blade.php --}}
<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label">اسم الخدمة <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $businessService?->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            required
            placeholder="مثال: استشارة، كشف، جلسة تنظيف"
        >

        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">المدة بالدقائق <span class="text-danger">*</span></label>
        <input
            type="number"
            name="duration_minutes"
            value="{{ old('duration_minutes', $businessService?->duration_minutes ?? 30) }}"
            class="form-control @error('duration_minutes') is-invalid @enderror"
            min="5"
            max="1440"
            required
        >

        @error('duration_minutes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">الترتيب</label>
        <input
            type="number"
            name="sort_order"
            value="{{ old('sort_order', $businessService?->sort_order ?? 0) }}"
            class="form-control @error('sort_order') is-invalid @enderror"
            min="0"
        >

        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">وصف الخدمة</label>
        <textarea
            name="description"
            rows="4"
            class="form-control @error('description') is-invalid @enderror"
            placeholder="وصف مختصر يظهر لك أو للعميل لاحقًا"
        >{{ old('description', $businessService?->description) }}</textarea>

        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">السعر</label>
        <input
            type="number"
            step="0.01"
            min="0"
            name="price"
            value="{{ old('price', $businessService?->price) }}"
            class="form-control @error('price') is-invalid @enderror"
            placeholder="مثال: 250"
        >

        @error('price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">العملة</label>
        <input
            type="text"
            name="currency"
            value="{{ old('currency', $businessService?->currency ?? 'EGP') }}"
            class="form-control @error('currency') is-invalid @enderror"
            required
        >

        @error('currency')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
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
                @checked(old('is_active', $businessService?->is_active ?? true))
            >

            <label class="form-check-label" for="is_active">
                الخدمة نشطة
            </label>
        </div>
    </div>
</div>