{{-- resources/views/restaurant/item_options/_form.blade.php --}}
@php
    /** @var \App\Models\ItemOption|null $option */
    $isEdit = isset($option) && $option?->id;
@endphp

<div class="row g-3">

    {{-- اسم الخيار --}}
    <div class="col-12 col-md-6">
        <label for="name" class="form-label">
            اسم الخيار <span class="text-danger">*</span>
        </label>
        <input type="text"
               id="name"
               name="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $option->name ?? '') }}"
               placeholder="مثال: صغير، وسط، كبير، جبنة زيادة"
               required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- فرق السعر --}}
    <div class="col-12 col-md-3">
        <label for="price_delta" class="form-label">
            فرق السعر (اختياري)
        </label>
        <input type="number"
               step="0.01"
               min="0"
               id="price_delta"
               name="price_delta"
               class="form-control @error('price_delta') is-invalid @enderror"
               value="{{ old('price_delta', $option->price_delta ?? '') }}"
               placeholder="مثال: 5.00">
        <div class="form-text">
            يتم إضافته على السعر الأساسي للصنف (يمكن تركه 0).
        </div>
        @error('price_delta')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- الترتيب --}}
    <div class="col-12 col-md-3">
        <label for="sort_order" class="form-label">
            ترتيب العرض (اختياري)
        </label>
        <input type="number"
               id="sort_order"
               name="sort_order"
               class="form-control @error('sort_order') is-invalid @enderror"
               value="{{ old('sort_order', $option->sort_order ?? '') }}"
               placeholder="مثال: 1، 2، 3...">
        <div class="form-text">
            كلما كان الرقم أصغر ظهر الخيار في مكان أعلى داخل المجموعة.
        </div>
        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- الحالة --}}
    <div class="col-12 col-md-4">
        <label class="form-label d-block">الحالة</label>
        @php
            $defActive = $option->is_active ?? true;
            $activeChecked = old('is_active', $defActive) ? 'checked' : '';
        @endphp
        <div class="form-check form-switch">
            <input class="form-check-input"
                   type="checkbox"
                   id="is_active"
                   name="is_active"
                   value="1"
                   {{ $activeChecked }}>
            <label class="form-check-label" for="is_active">
                الخيار مفعّل
            </label>
        </div>
        <div class="form-text">
            عند التعطيل لن يظهر هذا الخيار في واجهة المنيو.
        </div>
        @error('is_active')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

</div>

<div class="mt-4 d-flex flex-wrap gap-2">
    <button type="submit" class="btn btn-primary">
        {{ $isEdit ? 'حفظ التعديلات' : 'حفظ الخيار' }}
    </button>

    <a href="{{ route('restaurant.items.option-groups.options.index', [$item, $group]) }}"
       class="btn btn-outline-secondary">
        إلغاء والرجوع للقائمة
    </a>
</div>
