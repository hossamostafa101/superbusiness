{{-- resources/views/app/restaurant-menu/theme/partials/section-select.blade.php --}}
<div>
    <label class="form-label fw-semibold">
        {{ $label }}
    </label>

    <select
        name="{{ $name }}"
        class="form-select section-select @error($name) is-invalid @enderror"
        data-preview-target="preview-{{ $name }}"
        @disabled($disabled)
    >
        <option value="">الافتراضي</option>

        @foreach($sections as $section)
            <option
                value="{{ $section->id }}"
                {{-- data-preview="{{ $section->preview_image ? asset($section->preview_image) : '' }}" --}}
                data-preview="{{ $section->previewImageUrl() ?: '' }}"
                data-description="{{ $section->description }}"
                @selected((string) $selected === (string) $section->id)
            >
                {{ $section->name }}
                @if($section->is_premium)
                    — Premium
                @endif
            </option>
        @endforeach
    </select>

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    <div class="section-preview mt-2" id="preview-{{ $name }}">
        <div class="section-preview-empty">
            اختر تصميمًا لعرض المعاينة
        </div>
    </div>

    @if($sections->count())
        <div class="small text-muted mt-1">
            {{ $sections->count() }} اختيار متاح
        </div>
    @else
        <div class="small text-muted mt-1">
            لا توجد اختيارات متاحة لهذا القسم.
        </div>
    @endif
</div>