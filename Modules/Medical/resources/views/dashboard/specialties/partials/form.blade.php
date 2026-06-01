<div class="row g-4">
    <div class="col-md-8">
        <label class="form-label">اسم التخصص <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $specialty?->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            required
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-2">
        <label class="form-label">الترتيب</label>
        <input
            type="number"
            name="sort_order"
            value="{{ old('sort_order', $specialty?->sort_order ?? 0) }}"
            class="form-control"
            min="0"
        >
    </div>

    <div class="col-md-2">
        <label class="form-label d-block">نشط</label>
        <input type="hidden" name="is_active" value="0">

        <div class="form-check form-switch mt-2">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                class="form-check-input"
                @checked(old('is_active', $specialty?->is_active ?? true))
            >
        </div>
    </div>

    <div class="col-12">
        <label class="form-label">الوصف</label>
        <textarea name="description" rows="3" class="form-control">{{ old('description', $specialty?->description) }}</textarea>
    </div>
</div>