{{-- resources/views/app/restaurant-menu/items/partials/form.blade.php --}}
<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label">الفرع <span class="text-danger">*</span></label>
        <select name="branch_id" id="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
            <option value="">اختر الفرع</option>

            @foreach($branches as $branch)
                <option
                    value="{{ $branch->id }}"
                    @selected((int) old('branch_id', $restaurantMenuItem?->branch_id) === (int) $branch->id)
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
        <label class="form-label">التصنيف</label>
        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
            <option value="">بدون تصنيف</option>

            @foreach($categories as $category)
                <option
                    value="{{ $category->id }}"
                    data-branch="{{ $category->branch_id }}"
                    @selected((int) old('category_id', $restaurantMenuItem?->category_id) === (int) $category->id)
                >
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted">
            سيتم عرض التصنيفات الخاصة بالفرع المختار فقط.
        </small>
    </div>

    <div class="col-md-6">
        <label class="form-label">اسم الصنف <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $restaurantMenuItem?->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            required
            placeholder="مثال: برجر كلاسيك، لاتيه، بيتزا مارغريتا"
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
            value="{{ old('price', $restaurantMenuItem?->price) }}"
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
            value="{{ old('sale_price', $restaurantMenuItem?->sale_price) }}"
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
            value="{{ old('currency', $restaurantMenuItem?->currency ?? 'EGP') }}"
            class="form-control @error('currency') is-invalid @enderror"
            required
        >

        @error('currency')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">وصف الصنف</label>
        <textarea
            name="description"
            rows="4"
            class="form-control @error('description') is-invalid @enderror"
            placeholder="وصف مختصر للصنف ومكوناته"
        >{{ old('description', $restaurantMenuItem?->description) }}</textarea>

        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">صورة الصنف</label>
        <input
            type="file"
            name="image"
            class="form-control @error('image') is-invalid @enderror"
            accept="image/*"
        >

        @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        @if($restaurantMenuItem?->image)
            <div class="mt-3">
                <img
                    src="{{ asset('storage/' . $restaurantMenuItem->image) }}"
                    alt="{{ $restaurantMenuItem->name }}"
                    class="rounded border"
                    style="width: 130px; height: 130px; object-fit: cover;"
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

    <div class="col-md-2">
        <label class="form-label">السعرات</label>
        <input
            type="number"
            min="0"
            name="calories"
            value="{{ old('calories', $restaurantMenuItem?->calories) }}"
            class="form-control @error('calories') is-invalid @enderror"
        >

        @error('calories')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-2">
        <label class="form-label">وقت التحضير</label>
        <input
            type="number"
            min="0"
            max="1440"
            name="preparation_time_minutes"
            value="{{ old('preparation_time_minutes', $restaurantMenuItem?->preparation_time_minutes) }}"
            class="form-control @error('preparation_time_minutes') is-invalid @enderror"
            placeholder="دقائق"
        >

        @error('preparation_time_minutes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-2">
        <label class="form-label">الترتيب</label>
        <input
            type="number"
            min="0"
            name="sort_order"
            value="{{ old('sort_order', $restaurantMenuItem?->sort_order ?? 0) }}"
            class="form-control @error('sort_order') is-invalid @enderror"
        >

        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <div class="form-check form-switch">
            <input type="hidden" name="is_available" value="0">

            <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                name="is_available"
                value="1"
                id="is_available"
                @checked(old('is_available', $restaurantMenuItem?->is_available ?? true))
            >

            <label class="form-check-label" for="is_available">
                الصنف متاح
            </label>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-check form-switch">
            <input type="hidden" name="is_featured" value="0">

            <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                name="is_featured"
                value="1"
                id="is_featured"
                @checked(old('is_featured', $restaurantMenuItem?->is_featured ?? false))
            >

            <label class="form-check-label" for="is_featured">
                صنف مميز
            </label>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const branchSelect = document.getElementById('branch_id');
        const categorySelect = document.getElementById('category_id');

        if (!branchSelect || !categorySelect) {
            return;
        }

        const allOptions = Array.from(categorySelect.querySelectorAll('option'));

        function filterCategories() {
            const branchId = branchSelect.value;
            const selectedValue = categorySelect.value;

            categorySelect.innerHTML = '';

            allOptions.forEach(function (option) {
                if (!option.value) {
                    categorySelect.appendChild(option.cloneNode(true));
                    return;
                }

                if (!branchId || option.dataset.branch === branchId) {
                    categorySelect.appendChild(option.cloneNode(true));
                }
            });

            const hasOldSelected = Array.from(categorySelect.options).some(function (option) {
                return option.value === selectedValue;
            });

            if (hasOldSelected) {
                categorySelect.value = selectedValue;
            }
        }

        branchSelect.addEventListener('change', filterCategories);

        filterCategories();
    })();
</script>
@endpush