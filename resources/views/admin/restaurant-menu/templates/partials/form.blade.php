{{-- resources/views/admin/restaurant-menu/templates/partials/form.blade.php --}}
@php
    $layout = $restaurantMenuTemplate?->layout_config ?? [];

    $selected = [
        'hero' => old('hero', $layout['hero'] ?? ''),
        'branch_switch' => old('branch_switch', $layout['branch_switch'] ?? ''),
        'category_tabs' => old('category_tabs', $layout['category_tabs'] ?? ''),
        'items' => old('items', $layout['items'] ?? ''),
        'item_modal' => old('item_modal', $layout['item_modal'] ?? ''),
        'cart' => old('cart', $layout['cart'] ?? ''),
        'invoice' => old('invoice', $layout['invoice'] ?? ''),
        'footer' => old('footer', $layout['footer'] ?? ''),
    ];

    $labels = [
        'hero' => 'Hero',
        'branch_switch' => 'Branch Switch',
        'category_tabs' => 'Category Tabs',
        'items' => 'Items',
        'item_modal' => 'Item Modal',
        'cart' => 'Cart',
        'invoice' => 'Invoice / Session',
        'footer' => 'Footer',
    ];
@endphp

<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label">اسم القالب</label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $restaurantMenuTemplate?->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            required
            placeholder="Modern Restaurant"
        >

        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Key</label>
        <input
            type="text"
            name="key"
            value="{{ old('key', $restaurantMenuTemplate?->key) }}"
            class="form-control @error('key') is-invalid @enderror"
            required
            dir="ltr"
            placeholder="modern_restaurant"
        >

        @error('key')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <div class="form-text">
            استخدم حروف صغيرة وأرقام و underscore فقط.
        </div>
    </div>

    <div class="col-12">
        <label class="form-label">الوصف</label>
        <textarea
            name="description"
            rows="3"
            class="form-control @error('description') is-invalid @enderror"
            placeholder="وصف مختصر يظهر لصاحب المطعم"
        >{{ old('description', $restaurantMenuTemplate?->description) }}</textarea>

        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">صورة المعاينة</label>
        <input
            type="file"
            name="preview_image"
            class="form-control @error('preview_image') is-invalid @enderror"
            accept="image/*"
        >

        @error('preview_image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        @if($restaurantMenuTemplate?->previewImageUrl())
            <div class="mt-2">
                <img
                    src="{{ $restaurantMenuTemplate->previewImageUrl() }}"
                    class="rounded border"
                    style="width:180px;height:110px;object-fit:cover;"
                    alt="{{ $restaurantMenuTemplate->name }}"
                >
            </div>
        @endif
    </div>

    <div class="col-md-2">
        <label class="form-label">الترتيب</label>
        <input
            type="number"
            name="sort_order"
            value="{{ old('sort_order', $restaurantMenuTemplate?->sort_order ?? 0) }}"
            class="form-control @error('sort_order') is-invalid @enderror"
            min="0"
        >

        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-2">
        <label class="form-label d-block">Premium</label>

        <input type="hidden" name="is_premium" value="0">

        <div class="form-check form-switch">
            <input
                class="form-check-input"
                type="checkbox"
                name="is_premium"
                value="1"
                @checked(old('is_premium', $restaurantMenuTemplate?->is_premium ?? false))
            >
        </div>
    </div>

    <div class="col-md-2">
        <label class="form-label d-block">Active</label>

        <input type="hidden" name="is_active" value="0">

        <div class="form-check form-switch">
            <input
                class="form-check-input"
                type="checkbox"
                name="is_active"
                value="1"
                @checked(old('is_active', $restaurantMenuTemplate?->is_active ?? true))
            >
        </div>
    </div>
</div>

<hr class="my-4">

<h5 class="fw-bold mb-3">
    أقسام القالب
</h5>

<div class="row g-4">
    @foreach($labels as $sectionType => $label)
        <div class="col-md-6">
            <label class="form-label">{{ $label }}</label>

            <select
                name="{{ $sectionType }}"
                class="form-select @error($sectionType) is-invalid @enderror"
                required
            >
                <option value="">اختر القسم</option>

                @foreach(($sectionOptions[$sectionType] ?? collect()) as $section)
                    <option
                        value="{{ $section->key }}"
                        @selected($selected[$sectionType] === $section->key)
                    >
                        {{ $section->name }}
                        —
                        {{ $section->key }}

                        @if($section->is_premium)
                            — Premium
                        @endif
                    </option>
                @endforeach
            </select>

            @error($sectionType)
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if(($sectionOptions[$sectionType] ?? collect())->isEmpty())
                <div class="form-text text-danger">
                    لا توجد أقسام نشطة من هذا النوع. أضف Section أولًا.
                </div>
            @endif
        </div>
    @endforeach
</div>