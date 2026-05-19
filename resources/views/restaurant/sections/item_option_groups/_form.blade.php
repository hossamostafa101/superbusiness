{{-- resources/views/restaurant/item_option_groups/_form.blade.php --}}
@php
    /** @var \App\Models\ItemOptionGroup|null $group */
    $isEdit = isset($group) && $group?->id;
@endphp

<div class="row g-3">

    {{-- اسم المجموعة --}}
    <div class="col-12 col-md-6">
        <label for="name" class="form-label">
            اسم المجموعة <span class="text-danger">*</span>
        </label>
        <input type="text"
               id="name"
               name="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $group->name ?? '') }}"
               placeholder="مثال: الحجم، النوع، الإضافات"
               required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- النوع (اختياري) --}}
    <div class="col-12 col-md-6">
        <label for="type" class="form-label">
            نوع المجموعة (اختياري)
        </label>
        <input type="text"
               id="type"
               name="type"
               class="form-control @error('type') is-invalid @enderror"
               value="{{ old('type', $group->type ?? '') }}"
               placeholder="مثال: size / type / addon">
        <div class="form-text">
            فقط لتصنيف داخلي لك (لن يظهر للعميل).
        </div>
        @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- إجباري / اختياري --}}
    <div class="col-12 col-md-4">
        <label class="form-label d-block">هل الاختيار من هذه المجموعة إجباري؟</label>
        @php
            $defRequired = $group->is_required ?? false;
            $requiredChecked = old('is_required', $defRequired) ? 'checked' : '';
        @endphp
        <div class="form-check form-switch">
            <input class="form-check-input"
                   type="checkbox"
                   id="is_required"
                   name="is_required"
                   value="1"
                   {{ $requiredChecked }}>
            <label class="form-check-label" for="is_required">
                يجب على العميل اختيار خيار واحد على الأقل
            </label>
        </div>
        <div class="form-text">
            مثال: مجموعة "الحجم" عادة تكون إجبارية (صغير / وسط / كبير).
        </div>
        @error('is_required')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    {{-- متعدد / اختيار واحد --}}
    <div class="col-12 col-md-4">
        <label class="form-label d-block">هل يسمح باختيار أكثر من خيار؟</label>
        @php
            $defMulti = $group->is_multi ?? false;
            $multiChecked = old('is_multi', $defMulti) ? 'checked' : '';
        @endphp
        <div class="form-check form-switch">
            <input class="form-check-input"
                   type="checkbox"
                   id="is_multi"
                   name="is_multi"
                   value="1"
                   {{ $multiChecked }}>
            <label class="form-check-label" for="is_multi">
                يمكن اختيار أكثر من خيار (إضافات مثل: جبنة، صوص، إلخ)
            </label>
        </div>
        <div class="form-text">
            لو غير مفعّل → يسمح بخيار واحد فقط (radio).
        </div>
        @error('is_multi')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    {{-- الترتيب --}}
    <div class="col-12 col-md-4">
        <label for="sort_order" class="form-label">
            ترتيب العرض (اختياري)
        </label>
        <input type="number"
               id="sort_order"
               name="sort_order"
               class="form-control @error('sort_order') is-invalid @enderror"
               value="{{ old('sort_order', $group->sort_order ?? '') }}"
               placeholder="مثال: 1، 2، 3...">
        <div class="form-text">
            كلما كان الرقم أصغر ظهرت المجموعة في مكان أعلى.
        </div>
        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- الحالة --}}
    <div class="col-12 col-md-4">
        <label class="form-label d-block">الحالة</label>
        @php
            $defActive = $group->is_active ?? true;
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
                المجموعة مفعّلة
            </label>
        </div>
        <div class="form-text">
            عند التعطيل لن تظهر هذه المجموعة في المنيو.
        </div>
        @error('is_active')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

</div>

<div class="mt-4 d-flex flex-wrap gap-2">
    <button type="submit" class="btn btn-primary">
        {{ $isEdit ? 'حفظ التعديلات' : 'حفظ المجموعة' }}
    </button>

    <a href="{{ route('restaurant.items.option-groups.index', $item) }}" class="btn btn-outline-secondary">
        إلغاء والرجوع للقائمة
    </a>
</div>
