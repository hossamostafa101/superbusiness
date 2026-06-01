<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label">اسم القسم <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $department?->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            required
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">الفرع</label>
        <select name="branch_id" class="form-select">
            <option value="">كل الفروع</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}" @selected((string) old('branch_id', $department?->branch_id) === (string) $branch->id)>
                    {{ $branch->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12">
        <label class="form-label">الوصف</label>
        <textarea name="description" rows="3" class="form-control">{{ old('description', $department?->description) }}</textarea>
    </div>

    <div class="col-md-4">
        <label class="form-label">الأيقونة</label>
        <input
            type="text"
            name="icon"
            value="{{ old('icon', $department?->icon) }}"
            class="form-control"
            placeholder="bi-heart-pulse"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">اللون</label>
        <input
            type="color"
            name="color"
            value="{{ old('color', $department?->color ?: '#2563eb') }}"
            class="form-control form-control-color"
        >
    </div>

    <div class="col-md-2">
        <label class="form-label">الترتيب</label>
        <input
            type="number"
            name="sort_order"
            value="{{ old('sort_order', $department?->sort_order ?? 0) }}"
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
                @checked(old('is_active', $department?->is_active ?? true))
            >
        </div>
    </div>
</div>