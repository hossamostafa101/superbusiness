{{-- resources/views/app/business-links/partials/form.blade.php --}}
<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label">عنوان الرابط <span class="text-danger">*</span></label>
        <input
            type="text"
            name="title"
            value="{{ old('title', $businessLink?->title) }}"
            class="form-control @error('title') is-invalid @enderror"
            required
            placeholder="مثال: Instagram"
        >
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">الأيقونة</label>
        <select name="icon" class="form-select @error('icon') is-invalid @enderror">
            @php
                $selectedIcon = old('icon', $businessLink?->icon);
            @endphp

            <option value="">بدون أيقونة</option>
            <option value="instagram" @selected($selectedIcon === 'instagram')>Instagram</option>
            <option value="facebook" @selected($selectedIcon === 'facebook')>Facebook</option>
            <option value="tiktok" @selected($selectedIcon === 'tiktok')>TikTok</option>
            <option value="youtube" @selected($selectedIcon === 'youtube')>YouTube</option>
            <option value="whatsapp" @selected($selectedIcon === 'whatsapp')>WhatsApp</option>
            <option value="website" @selected($selectedIcon === 'website')>Website</option>
            <option value="location" @selected($selectedIcon === 'location')>Location</option>
            <option value="store" @selected($selectedIcon === 'store')>Store</option>
        </select>

        @error('icon')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">الرابط <span class="text-danger">*</span></label>
        <input
            type="url"
            name="url"
            value="{{ old('url', $businessLink?->url) }}"
            class="form-control @error('url') is-invalid @enderror"
            required
            placeholder="https://example.com"
        >
        @error('url')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">الترتيب</label>
        <input
            type="number"
            name="sort_order"
            value="{{ old('sort_order', $businessLink?->sort_order ?? 0) }}"
            class="form-control @error('sort_order') is-invalid @enderror"
            min="0"
        >
        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted">الأقل يظهر أولًا.</small>
    </div>

    <div class="col-md-6 d-flex align-items-end">
        <div class="form-check form-switch mb-2">
            <input type="hidden" name="is_active" value="0">
            <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                name="is_active"
                value="1"
                id="is_active"
                @checked(old('is_active', $businessLink?->is_active ?? true))
            >
            <label class="form-check-label" for="is_active">
                الرابط نشط
            </label>
        </div>
    </div>
</div>