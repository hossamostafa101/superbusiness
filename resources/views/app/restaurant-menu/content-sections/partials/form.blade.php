{{-- resources/views/app/restaurant-menu/content-sections/partials/form.blade.php --}}
@php
    $selectedType = old('type', $contentSection?->type ?? 'featured_items');
    $selectedBackgroundType = old('background_type', $contentSection?->background_type ?? 'solid');
@endphp

<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label">نوع القسم <span class="text-danger">*</span></label>

        <select name="type" id="sectionType" class="form-select @error('type') is-invalid @enderror" required>
            <option value="featured_items" @selected($selectedType === 'featured_items')>
                أصناف مميزة - Scroll أفقي
            </option>

            <option value="item_collection" @selected($selectedType === 'item_collection')>
                مجموعة أصناف بعنوان وخلفية مخصصة
            </option>

            <option value="offers_slider" @selected($selectedType === 'offers_slider')>
                سلايدر عروض
            </option>
        </select>

        @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <div class="form-text">
            عند اختيار سلايدر عروض، ستضيف العروض من صفحة منفصلة بعد حفظ القسم.
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label">الفرع</label>

        <select name="branch_id" class="form-select @error('branch_id') is-invalid @enderror">
            <option value="">عام لكل الفروع</option>

            @foreach($branches as $branch)
                <option value="{{ $branch->id }}" @selected((string) old('branch_id', $contentSection?->branch_id) === (string) $branch->id)>
                    {{ $branch->name }}
                </option>
            @endforeach
        </select>

        @error('branch_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <div class="form-text">
            لو اخترت فرعًا، سيظهر القسم في هذا الفرع فقط.
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label">العنوان <span class="text-danger">*</span></label>

        <input
            type="text"
            name="title"
            value="{{ old('title', $contentSection?->title) }}"
            class="form-control @error('title') is-invalid @enderror"
            required
            placeholder="مثال: الأكثر طلبًا"
        >

        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">وصف قصير</label>

        <input
            type="text"
            name="subtitle"
            value="{{ old('subtitle', $contentSection?->subtitle) }}"
            class="form-control @error('subtitle') is-invalid @enderror"
            placeholder="مثال: اختيارات مفضلة من عملائنا"
        >

        @error('subtitle')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">الترتيب</label>

        <input
            type="number"
            name="sort_order"
            value="{{ old('sort_order', $contentSection?->sort_order ?? 0) }}"
            class="form-control @error('sort_order') is-invalid @enderror"
            min="0"
        >

        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">تاريخ البداية</label>

        <input
            type="datetime-local"
            name="starts_at"
            value="{{ old('starts_at', $contentSection?->starts_at?->format('Y-m-d\TH:i')) }}"
            class="form-control @error('starts_at') is-invalid @enderror"
        >

        @error('starts_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">تاريخ النهاية</label>

        <input
            type="datetime-local"
            name="ends_at"
            value="{{ old('ends_at', $contentSection?->ends_at?->format('Y-m-d\TH:i')) }}"
            class="form-control @error('ends_at') is-invalid @enderror"
        >

        @error('ends_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label d-block">الحالة</label>

        <input type="hidden" name="is_active" value="0">

        <div class="form-check form-switch mt-2">
            <input
                class="form-check-input"
                type="checkbox"
                name="is_active"
                value="1"
                id="is_active"
                @checked(old('is_active', $contentSection?->is_active ?? true))
            >

            <label class="form-check-label" for="is_active">
                ظاهر
            </label>
        </div>
    </div>
</div>

<hr class="my-4">

<div class="{{ ! $customBgEnabled ? 'opacity-50' : '' }}">
    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
        <div>
            <h5 class="fw-bold mb-1">الخلفية والألوان</h5>

            <div class="text-muted small">
                خصص شكل القسم في صفحة المنيو.
            </div>
        </div>

        @if(! $customBgEnabled)
            <span class="badge bg-secondary">
                غير متاح في الباقة
            </span>
        @endif
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <label class="form-label">نوع الخلفية</label>

            <select
                name="background_type"
                id="backgroundType"
                class="form-select @error('background_type') is-invalid @enderror"
                @disabled(! $customBgEnabled)
            >
                <option value="solid" @selected($selectedBackgroundType === 'solid')>لون ثابت</option>
                <option value="gradient" @selected($selectedBackgroundType === 'gradient')>Gradient</option>
            </select>

            @error('background_type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4 solid-bg-field">
            <label class="form-label">لون الخلفية</label>

            <input
                type="color"
                name="background_color"
                value="{{ old('background_color', $contentSection?->background_color ?? '#ffffff') }}"
                class="form-control form-control-color w-100"
                @disabled(! $customBgEnabled)
            >
        </div>

        <div class="col-md-4 gradient-bg-field">
            <label class="form-label">Gradient From</label>

            <input
                type="color"
                name="background_gradient_from"
                value="{{ old('background_gradient_from', $contentSection?->background_gradient_from ?? '#111827') }}"
                class="form-control form-control-color w-100"
                @disabled(! $customBgEnabled)
            >
        </div>

        <div class="col-md-4 gradient-bg-field">
            <label class="form-label">Gradient To</label>

            <input
                type="color"
                name="background_gradient_to"
                value="{{ old('background_gradient_to', $contentSection?->background_gradient_to ?? '#2563eb') }}"
                class="form-control form-control-color w-100"
                @disabled(! $customBgEnabled)
            >
        </div>

        <div class="col-md-4">
            <label class="form-label">لون النص</label>

            <input
                type="color"
                name="text_color"
                value="{{ old('text_color', $contentSection?->text_color ?? '#111827') }}"
                class="form-control form-control-color w-100"
                @disabled(! $customBgEnabled)
            >
        </div>

        <div class="col-md-4">
            <label class="form-label">لون الأزرار</label>

            <input
                type="color"
                name="button_color"
                value="{{ old('button_color', $contentSection?->button_color ?? '#2563eb') }}"
                class="form-control form-control-color w-100"
                @disabled(! $customBgEnabled)
            >
        </div>
    </div>

    @if(! $customBgEnabled)
        <div class="form-text text-danger mt-2">
            سيتم استخدام الخلفية الافتراضية في باقتك الحالية.
        </div>
    @endif
</div>

<hr class="my-4">

<div id="itemsBox">
    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
        <div>
            <h5 class="fw-bold mb-1">اختيار الأصناف</h5>

            <div class="text-muted small">
                اختر الأصناف التي ستظهر داخل هذا القسم.
            </div>
        </div>
    </div>

    @error('item_ids')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    <div class="row g-3">
        @forelse($items as $item)
            <div class="col-md-6">
                <label class="border rounded-4 p-3 d-flex gap-3 align-items-start h-100">
                    <input
                        type="checkbox"
                        name="item_ids[]"
                        value="{{ $item->id }}"
                        class="form-check-input mt-1"
                        @checked(in_array($item->id, array_map('intval', (array) $selectedItemIds), true))
                    >

                    <div>
                        <div class="fw-bold">
                            {{ $item->name }}
                        </div>

                        <div class="small text-muted">
                            {{ $item->branch?->name ?: 'بدون فرع' }}
                            —
                            {{ number_format((float) ($item->sale_price ?: $item->price), 2) }}
                            {{ $item->currency }}
                        </div>
                    </div>
                </label>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning mb-0">
                    لا توجد أصناف متاحة. أضف أصناف المنيو أولًا.
                </div>
            </div>
        @endforelse
    </div>
</div>

<div id="offersHintBox" style="display:none;">
    <div class="alert alert-info mb-0">
        هذا القسم من نوع سلايدر عروض. بعد حفظ القسم ستظهر لك صفحة لإضافة العروض والصور والأسعار.
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const sectionType = document.getElementById('sectionType');
        const itemsBox = document.getElementById('itemsBox');
        const offersHintBox = document.getElementById('offersHintBox');

        const backgroundType = document.getElementById('backgroundType');
        const solidFields = document.querySelectorAll('.solid-bg-field');
        const gradientFields = document.querySelectorAll('.gradient-bg-field');

        function syncSectionType() {
            const type = sectionType ? sectionType.value : 'featured_items';

            if (itemsBox) {
                itemsBox.style.display = type === 'offers_slider' ? 'none' : 'block';
            }

            if (offersHintBox) {
                offersHintBox.style.display = type === 'offers_slider' ? 'block' : 'none';
            }
        }

        function syncBackgroundType() {
            const type = backgroundType ? backgroundType.value : 'solid';

            solidFields.forEach(function (field) {
                field.style.display = type === 'solid' ? 'block' : 'none';
            });

            gradientFields.forEach(function (field) {
                field.style.display = type === 'gradient' ? 'block' : 'none';
            });
        }

        sectionType?.addEventListener('change', syncSectionType);
        backgroundType?.addEventListener('change', syncBackgroundType);

        syncSectionType();
        syncBackgroundType();
    })();
</script>
@endpush