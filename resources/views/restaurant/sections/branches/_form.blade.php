{{-- resources/views/restaurant/branches/_form.blade.php --}}
@php
    /** @var \App\Models\Branch|null $branch */
    $isEdit = isset($branch) && $branch?->id;
@endphp

<div class="row g-3">

    {{-- اسم الفرع --}}
    <div class="col-12 col-md-6">
        <label for="name" class="form-label">
            اسم الفرع <span class="text-danger">*</span>
        </label>
        <input type="text"
               id="name"
               name="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $branch->name ?? '') }}"
               placeholder="مثال: فرع التجمع الخامس"
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
               value="{{ old('slug', $branch->slug ?? '') }}"
               placeholder="مثال: new-cairo-branch">
        <div class="form-text">
            يُستخدم في الرابط، يترك فارغًا ليُولّد تلقائيًا من الاسم.
        </div>
        @error('slug')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- المدينة --}}
    <div class="col-12 col-md-6">
        <label for="city" class="form-label">
            المدينة (اختياري)
        </label>
        <input type="text"
               id="city"
               name="city"
               class="form-control @error('city') is-invalid @enderror"
               value="{{ old('city', $branch->city ?? '') }}"
               placeholder="القاهرة، دبي، الرياض...">
        @error('city')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- الهاتف --}}
    <div class="col-12 col-md-6">
        <label for="phone" class="form-label">
            رقم الهاتف (اختياري)
        </label>
        <input type="text"
               id="phone"
               name="phone"
               class="form-control @error('phone') is-invalid @enderror"
               value="{{ old('phone', $branch->phone ?? '') }}"
               placeholder="مثال: 01012345678">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- العنوان --}}
    <div class="col-12">
        <label for="address" class="form-label">
            العنوان التفصيلي (اختياري)
        </label>
        <textarea id="address"
                  name="address"
                  rows="2"
                  class="form-control @error('address') is-invalid @enderror"
                  placeholder="مثال: شارع رقم ١، بجوار مول كذا، الحي الخامس.">{{ old('address', $branch->address ?? '') }}</textarea>
        @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- الإحداثيات --}}
    <div class="col-12 col-md-6">
        <label for="lat" class="form-label">
            خط العرض Latitude (اختياري)
        </label>
        <input type="text"
               id="lat"
               name="lat"
               class="form-control @error('lat') is-invalid @enderror"
               value="{{ old('lat', $branch->lat ?? '') }}"
               placeholder="مثال: 30.0444">
        @error('lat')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="lng" class="form-label">
            خط الطول Longitude (اختياري)
        </label>
        <input type="text"
               id="lng"
               name="lng"
               class="form-control @error('lng') is-invalid @enderror"
               value="{{ old('lng', $branch->lng ?? '') }}"
               placeholder="مثال: 31.2357">
        @error('lng')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- الحالة --}}
    <div class="col-12">
        <div class="form-check form-switch">
            @php
                $defaultActive = $branch->is_active ?? true;
                $checked = old('is_active', $defaultActive) ? 'checked' : '';
            @endphp
            <input class="form-check-input"
                   type="checkbox"
                   id="is_active"
                   name="is_active"
                   value="1"
                   {{ $checked }}>
            <label class="form-check-label" for="is_active">
                الفرع مفعل
            </label>
        </div>
        @error('is_active')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        <div class="form-text">
            عند التعطيل لن يظهر هذا الفرع في واجهة العملاء.
        </div>
    </div>

</div>

<div class="mt-4 d-flex flex-wrap gap-2">
    <button type="submit" class="btn btn-primary">
        {{ $isEdit ? 'حفظ التعديلات' : 'حفظ الفرع' }}
    </button>

    <a href="{{ route('restaurant.branches.index') }}" class="btn btn-outline-secondary">
        إلغاء والرجوع للقائمة
    </a>
</div>
