{{-- resources/views/restaurant/categories/_form.blade.php --}}
@php
    /** @var \App\Models\MenuCategory|null $category */
    $isEdit = isset($category) && $category?->id;
@endphp

<div class="row g-3">

    {{-- اسم التصنيف --}}
    <div class="col-12 col-md-6">
        <label for="name" class="form-label">
            اسم التصنيف <span class="text-danger">*</span>
        </label>
        <input type="text"
               id="name"
               name="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $category->name ?? '') }}"
               placeholder="مثال: البرجر، البيتزا، المشروبات..."
               required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- slug --}}
    <div class="col-12 col-md-6">
        <label for="slug" class="form-label">
            المعرف (Slug) (اختياري)
        </label>
        <input type="text"
               id="slug"
               name="slug"
               class="form-control @error('slug') is-invalid @enderror"
               value="{{ old('slug', $category->slug ?? '') }}"
               placeholder="مثال: burgers أو pizza">
        <div class="form-text">
            يُستخدم في الروابط. اتركه فارغًا ليُولّد تلقائيًا من الاسم.
        </div>
        @error('slug')
            <div class="invalid-feedback">{{ $message }}</div>
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
               value="{{ old('sort_order', $category->sort_order ?? '') }}"
               placeholder="مثال: 1، 2، 3...">
        <div class="form-text">
            كلما كان الرقم أصغر ظهر التصنيف في مكان أعلى في القائمة.
        </div>
        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- الحالة --}}
    <div class="col-12 col-md-4">
        <label class="form-label d-block">الحالة</label>
        @php
            $defaultActive = $category->is_active ?? true;
            $checked = old('is_active', $defaultActive) ? 'checked' : '';
        @endphp
        <div class="form-check form-switch">
            <input class="form-check-input"
                   type="checkbox"
                   id="is_active"
                   name="is_active"
                   value="1"
                   {{ $checked }}>
            <label class="form-check-label" for="is_active">
                التصنيف مفعل
            </label>
        </div>
        <div class="form-text">
            عند التعطيل لن يظهر هذا التصنيف في منيو الفرع.
        </div>
        @error('is_active')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

</div>

<div class="mt-4 d-flex flex-wrap gap-2">
    <button type="submit" class="btn btn-primary">
        {{ $isEdit ? 'حفظ التعديلات' : 'حفظ التصنيف' }}
    </button>

    <a href="{{ route('restaurant.categories.index') }}" class="btn btn-outline-secondary">
        إلغاء والرجوع للقائمة
    </a>
</div>
