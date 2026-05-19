{{-- resources/views/app/restaurant-menu/categories/partials/form.blade.php --}}
<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label">الفرع <span class="text-danger">*</span></label>
        <select name="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
            <option value="">اختر الفرع</option>

            @foreach($branches as $branch)
                <option
                    value="{{ $branch->id }}"
                    @selected((int) old('branch_id', $restaurantMenuCategory?->branch_id) === (int) $branch->id)
                >
                    {{ $branch->name }}
                </option>
            @endforeach
        </select>

        @error('branch_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">اسم التصنيف <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $restaurantMenuCategory?->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            required
            placeholder="مثال: مشروبات، بيتزا، حلويات"
        >

        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">وصف التصنيف</label>
        <textarea
            name="description"
            rows="4"
            class="form-control @error('description') is-invalid @enderror"
            placeholder="وصف اختياري يظهر في المنيو"
        >{{ old('description', $restaurantMenuCategory?->description) }}</textarea>

        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">صورة التصنيف</label>
        <input
            type="file"
            name="image"
            class="form-control @error('image') is-invalid @enderror"
            accept="image/*"
        >

        @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        @if($restaurantMenuCategory?->image)
            <div class="mt-3">
                <img
                    src="{{ asset('storage/' . $restaurantMenuCategory->image) }}"
                    alt="{{ $restaurantMenuCategory->name }}"
                    class="rounded border"
                    style="width: 120px; height: 120px; object-fit: cover;"
                >
            </div>

            @if($isEdit)
                <div class="form-check mt-2">
                    <input type="hidden" name="remove_image" value="0">

                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="remove_image"
                        value="1"
                        id="remove_image"
                    >

                    <label class="form-check-label" for="remove_image">
                        حذف الصورة الحالية
                    </label>
                </div>
            @endif
        @endif
    </div>

    <div class="col-md-3">
        <label class="form-label">الترتيب</label>
        <input
            type="number"
            name="sort_order"
            value="{{ old('sort_order', $restaurantMenuCategory?->sort_order ?? 0) }}"
            class="form-control @error('sort_order') is-invalid @enderror"
            min="0"
        >

        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted">
            الأقل يظهر أولًا.
        </small>
    </div>

    <div class="col-md-3 d-flex align-items-end">
        <div class="form-check form-switch mb-2">
            <input type="hidden" name="is_active" value="0">

            <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                name="is_active"
                value="1"
                id="is_active"
                @checked(old('is_active', $restaurantMenuCategory?->is_active ?? true))
            >

            <label class="form-check-label" for="is_active">
                التصنيف نشط
            </label>
        </div>
    </div>
</div>