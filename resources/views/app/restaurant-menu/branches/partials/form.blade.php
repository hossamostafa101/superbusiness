{{-- resources/views/app/restaurant-menu/branches/partials/form.blade.php --}}
<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label">اسم الفرع <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $restaurantBranch?->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            required
            placeholder="مثال: فرع مدينة نصر"
        >

        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">رابط الفرع</label>
        <input
            type="text"
            name="slug"
            value="{{ old('slug', $restaurantBranch?->slug) }}"
            class="form-control @error('slug') is-invalid @enderror"
            placeholder="مثال: nasr-city"
            dir="ltr"
        >

        @error('slug')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted">
            اختياري. لو تركته فارغًا سيتم توليده من اسم الفرع.
        </small>
    </div>

    <div class="col-md-6">
        <label class="form-label">الهاتف</label>
        <input
            type="text"
            name="phone"
            value="{{ old('phone', $restaurantBranch?->phone) }}"
            class="form-control @error('phone') is-invalid @enderror"
            placeholder="02xxxxxxxx"
        >

        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">رقم واتساب</label>
        <input
            type="text"
            name="whatsapp_number"
            value="{{ old('whatsapp_number', $restaurantBranch?->whatsapp_number) }}"
            class="form-control @error('whatsapp_number') is-invalid @enderror"
            placeholder="2010xxxxxxxx"
        >

        @error('whatsapp_number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted">
            يفضل بصيغة دولية بدون علامة +.
        </small>
    </div>

    <div class="col-md-6">
        <label class="form-label">العنوان</label>
        <input
            type="text"
            name="address"
            value="{{ old('address', $restaurantBranch?->address) }}"
            class="form-control @error('address') is-invalid @enderror"
            placeholder="عنوان الفرع"
        >

        @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">رابط اللوكيشن</label>
        <input
            type="url"
            name="location_url"
            value="{{ old('location_url', $restaurantBranch?->location_url) }}"
            class="form-control @error('location_url') is-invalid @enderror"
            placeholder="Google Maps URL"
        >

        @error('location_url')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">الترتيب</label>
        <input
            type="number"
            name="sort_order"
            value="{{ old('sort_order', $restaurantBranch?->sort_order ?? 0) }}"
            class="form-control @error('sort_order') is-invalid @enderror"
            min="0"
        >

        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted">الأقل يظهر أولًا.</small>
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
                @checked(old('is_active', $restaurantBranch?->is_active ?? true))
            >

            <label class="form-check-label" for="is_active">
                الفرع نشط
            </label>
        </div>
    </div>

    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check form-switch mb-2">
            <input type="hidden" name="is_default" value="0">

            <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                name="is_default"
                value="1"
                id="is_default"
                @checked(old('is_default', $restaurantBranch?->is_default ?? false))
            >

            <label class="form-check-label" for="is_default">
                الفرع الافتراضي
            </label>
        </div>
    </div>

    @if(! $isEdit)
        <div class="col-12">
            <hr>

            <h6 class="fw-bold mb-2">نسخ بيانات من فرع موجود</h6>

            @if($cloneEnabled)
                <label class="form-label">اختر فرعًا لنسخ التصنيفات والأصناف منه</label>

                <select
                    name="clone_from_branch_id"
                    class="form-select @error('clone_from_branch_id') is-invalid @enderror"
                >
                    <option value="">بدون نسخ</option>

                    @foreach($cloneBranches as $cloneBranch)
                        <option
                            value="{{ $cloneBranch->id }}"
                            @selected((int) old('clone_from_branch_id') === (int) $cloneBranch->id)
                        >
                            {{ $cloneBranch->name }}
                        </option>
                    @endforeach
                </select>

                @error('clone_from_branch_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <small class="text-muted">
                    سيتم نسخ التصنيفات والأصناف بنفس الصور والأسعار إلى الفرع الجديد.
                </small>
            @else
                <div class="alert alert-light border mb-0">
                    نسخ بيانات فرع متاح في الباقات الأعلى.
                </div>
            @endif
        </div>
    @endif
</div>