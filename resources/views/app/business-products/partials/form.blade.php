{{-- resources/views/app/business-products/partials/form.blade.php --}}
<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label">اسم المنتج <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $businessProduct?->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            required
            placeholder="مثال: عباية سوداء، بوكس هدايا، خدمة تصميم"
        >

        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">التصنيف</label>
        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
            <option value="">بدون تصنيف</option>

            @foreach($categories as $category)
                <option
                    value="{{ $category->id }}"
                    @selected((int) old('category_id', $businessProduct?->category_id) === (int) $category->id)
                >
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">وصف المنتج</label>
        <textarea
            name="description"
            rows="4"
            class="form-control @error('description') is-invalid @enderror"
            placeholder="وصف مختصر يظهر للعميل"
        >{{ old('description', $businessProduct?->description) }}</textarea>

        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">السعر الأساسي</label>
        <input
            type="number"
            step="0.01"
            min="0"
            name="price"
            value="{{ old('price', $businessProduct?->price) }}"
            class="form-control @error('price') is-invalid @enderror"
            placeholder="مثال: 250"
        >

        @error('price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">سعر العرض</label>
        <input
            type="number"
            step="0.01"
            min="0"
            name="sale_price"
            value="{{ old('sale_price', $businessProduct?->sale_price) }}"
            class="form-control @error('sale_price') is-invalid @enderror"
            placeholder="اختياري"
        >

        @error('sale_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">العملة</label>
        <input
            type="text"
            name="currency"
            value="{{ old('currency', $businessProduct?->currency ?? 'EGP') }}"
            class="form-control @error('currency') is-invalid @enderror"
            required
        >

        @error('currency')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">صورة المنتج</label>
        <input
            type="file"
            name="image"
            class="form-control @error('image') is-invalid @enderror"
            accept="image/*"
        >

        @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        @if($businessProduct?->image)
            <div class="mt-3">
                <img
                    src="{{ asset('storage/' . $businessProduct->image) }}"
                    alt="{{ $businessProduct->name }}"
                    class="rounded border"
                    style="width: 120px; height: 120px; object-fit: cover;"
                >
            </div>
        @endif
    </div>

    <div class="col-md-6">
        <label class="form-label">الترتيب</label>
        <input
            type="number"
            min="0"
            name="sort_order"
            value="{{ old('sort_order', $businessProduct?->sort_order ?? 0) }}"
            class="form-control @error('sort_order') is-invalid @enderror"
        >

        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted">الأقل يظهر أولًا.</small>
    </div>

    <div class="col-md-6">
        <div class="form-check form-switch">
            <input type="hidden" name="is_available" value="0">

            <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                name="is_available"
                value="1"
                id="is_available"
                @checked(old('is_available', $businessProduct?->is_available ?? true))
            >

            <label class="form-check-label" for="is_available">
                المنتج متاح
            </label>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-check form-switch">
            <input type="hidden" name="is_featured" value="0">

            <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                name="is_featured"
                value="1"
                id="is_featured"
                @checked(old('is_featured', $businessProduct?->is_featured ?? false))
            >

            <label class="form-check-label" for="is_featured">
                منتج مميز
            </label>
        </div>
    </div>
</div>