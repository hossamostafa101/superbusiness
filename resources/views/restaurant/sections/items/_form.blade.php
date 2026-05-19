@php
    /** @var \App\Models\MenuItem|null $item */
    $isEdit = isset($item) && $item?->id;
@endphp

<div class="row g-3">

    {{-- الاسم --}}
    <div class="col-12 col-md-6">
        <label for="name" class="form-label">
            اسم الصنف <span class="text-danger">*</span>
        </label>
        <input type="text"
               id="name"
               name="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $item->name ?? '') }}"
               placeholder="مثال: بيج برجر دبل تشيز"
               required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- التصنيف --}}
    <div class="col-12 col-md-6">
        <label for="category_id" class="form-label">
            التصنيف <span class="text-danger">*</span>
        </label>
        <select id="category_id"
                name="category_id"
                class="form-select @error('category_id') is-invalid @enderror"
                required>
            <option value="">اختر تصنيفًا</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}"
                    {{ (string)old('category_id', $item->category_id ?? '') === (string)$cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- السعر الأساسي --}}
    <div class="col-12 col-md-4">
        <label for="price" class="form-label">
            السعر الأساسي <span class="text-danger">*</span>
        </label>
        <input type="number"
               step="0.01"
               min="0"
               id="price"
               name="price"
               class="form-control @error('price') is-invalid @enderror"
               value="{{ old('price', $item->price ?? '') }}"
               placeholder="مثال: 120.00"
               required>
        @error('price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- سعر العرض --}}
    <div class="col-12 col-md-4">
        <label for="offer_price" class="form-label">
            سعر العرض (اختياري)
        </label>
        <input type="number"
               step="0.01"
               min="0"
               id="offer_price"
               name="offer_price"
               class="form-control @error('offer_price') is-invalid @enderror"
               value="{{ old('offer_price', $item->offer_price ?? '') }}"
               placeholder="مثال: 99.00">
        <div class="form-text">
            عند ضبطه أقل من السعر الأساسي سيُعرض كـ "عرض خاص".
        </div>
        @error('offer_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- التاجز --}}
    <div class="col-12 col-md-4">
        <label for="tags" class="form-label">
            التاجز (Tags) (اختياري)
        </label>
        <input type="text"
               id="tags"
               name="tags"
               class="form-control @error('tags') is-invalid @enderror"
               value="{{ old('tags', $item->tags ?? '') }}"
               placeholder="مثال: برجر, سبايسي, كومبو">
        <div class="form-text">
            افصل بين كل تاج بـ فاصلة (، أو ,) لاستخدامها في البحث و&quot;منتجات مشابهة&quot;.
        </div>
        @error('tags')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- الصورة --}}
    <div class="col-12 col-md-6">
        <label for="image" class="form-label">
            صورة الصنف {{ $isEdit ? '(يمكن تركها كما هي)' : '' }}
        </label>
        <input type="file"
               id="image"
               name="image"
               class="form-control @error('image') is-invalid @enderror"
               accept="image/*">
        <div class="form-text">
            يفضّل رفع صورة مربعة أو مستطيل بسيط بحجم مناسب للمنيو.
        </div>
        @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- معاينة الصورة الحالية في وضع التعديل --}}
    @if($isEdit && $item->image_path)
        <div class="col-12 col-md-6">
            <label class="form-label d-block">الصورة الحالية</label>
            <img src="{{ asset('storage/'.$item->image_path) }}"
                 alt="{{ $item->name }}"
                 class="img-thumbnail"
                 style="max-width: 180px; height: auto; object-fit: cover;">
        </div>
    @endif

    {{-- الحالة --}}
    <div class="col-12 col-md-4">
        <label class="form-label d-block">الحالة</label>
        @php
            $defaultActive = $item->is_active ?? true;
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
                الصنف مفعل
            </label>
        </div>
        <div class="form-text">
            عند التعطيل لن يظهر الصنف في المنيو.
        </div>
        @error('is_active')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    {{-- الوصف --}}
    <div class="col-12">
        <label for="description" class="form-label">
            وصف الصنف (اختياري)
        </label>
        <textarea id="description"
                  name="description"
                  rows="3"
                  class="form-control @error('description') is-invalid @enderror"
                  placeholder="مثال: برجر لحم بقري طازج، جبنة شيدر، خس، طماطم، وصوص خاص.">{{ old('description', $item->description ?? '') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

</div>

<div class="mt-4 d-flex flex-wrap gap-2">
    <button type="submit" class="btn btn-primary">
        {{ $isEdit ? 'حفظ التعديلات' : 'حفظ الصنف' }}
    </button>

    <a href="{{ route('restaurant.items.index') }}" class="btn btn-outline-secondary">
        إلغاء والرجوع للقائمة
    </a>
</div>
