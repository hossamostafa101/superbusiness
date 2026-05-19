<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label">نوع القسم</label>
        <select name="section_type" class="form-select @error('section_type') is-invalid @enderror" required>
            @foreach($sectionTypes as $value => $label)
                <option value="{{ $value }}" @selected(old('section_type', $restaurantMenuTemplateSection?->section_type) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('section_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">الاسم</label>
        <input type="text" name="name" value="{{ old('name', $restaurantMenuTemplateSection?->name) }}" class="form-control @error('name') is-invalid @enderror" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Key</label>
        <input type="text" name="key" value="{{ old('key', $restaurantMenuTemplateSection?->key) }}" class="form-control @error('key') is-invalid @enderror" required dir="ltr" placeholder="items_cards_large">
        @error('key')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Blade View</label>
        <input type="text" name="view" value="{{ old('view', $restaurantMenuTemplateSection?->config['view'] ?? '') }}" class="form-control @error('view') is-invalid @enderror" required dir="ltr" placeholder="public.restaurant-menu.templates.sections.items.cards-large">
        @error('view')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label">الوصف</label>
        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $restaurantMenuTemplateSection?->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">صورة المعاينة</label>
        <input type="file" name="preview_image" class="form-control @error('preview_image') is-invalid @enderror" accept="image/*">
        @error('preview_image')<div class="invalid-feedback">{{ $message }}</div>@enderror

        @if($restaurantMenuTemplateSection?->previewImageUrl())
            <img src="{{ $restaurantMenuTemplateSection->previewImageUrl() }}" class="mt-2 rounded border" style="width:160px;height:100px;object-fit:cover;">
        @endif
    </div>

    <div class="col-md-2">
        <label class="form-label">الترتيب</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $restaurantMenuTemplateSection?->sort_order ?? 0) }}" class="form-control" min="0">
    </div>

    <div class="col-md-2">
        <label class="form-label d-block">Premium</label>
        <input type="hidden" name="is_premium" value="0">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_premium" value="1" @checked(old('is_premium', $restaurantMenuTemplateSection?->is_premium ?? false))>
        </div>
    </div>

    <div class="col-md-2">
        <label class="form-label d-block">Active</label>
        <input type="hidden" name="is_active" value="0">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $restaurantMenuTemplateSection?->is_active ?? true))>
        </div>
    </div>
</div>