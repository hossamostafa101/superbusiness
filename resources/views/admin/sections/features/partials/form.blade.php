<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">اسم الخاصية <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $feature?->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            required
            placeholder="مثال: عدد المنتجات"
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">المفتاح البرمجي <span class="text-danger">*</span></label>
        <input
            type="text"
            name="key"
            value="{{ old('key', $feature?->key) }}"
            class="form-control @error('key') is-invalid @enderror"
            required
            placeholder="مثال: products_limit"
        >
        @error('key')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-body-secondary">
            استخدم حروف إنجليزية وشرطة سفلية فقط. مثال: products_limit
        </small>
    </div>

    <div class="col-md-4">
        <label class="form-label">النوع <span class="text-danger">*</span></label>
        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
            <option value="limit" @selected(old('type', $feature?->type ?? 'limit') === 'limit')>
                Limit
            </option>
            <option value="boolean" @selected(old('type', $feature?->type) === 'boolean')>
                Boolean
            </option>
            <option value="text" @selected(old('type', $feature?->type) === 'text')>
                Text
            </option>
        </select>

        @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">الموديول</label>
        <input
            type="text"
            name="module"
            value="{{ old('module', $feature?->module) }}"
            class="form-control @error('module') is-invalid @enderror"
            placeholder="مثال: catalog"
        >
        @error('module')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">الترتيب</label>
        <input
            type="number"
            name="sort_order"
            value="{{ old('sort_order', $feature?->sort_order ?? 0) }}"
            class="form-control @error('sort_order') is-invalid @enderror"
            min="0"
        >
        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">الوصف</label>
        <textarea
            name="description"
            rows="4"
            class="form-control @error('description') is-invalid @enderror"
            placeholder="وصف مختصر لاستخدام هذه الخاصية"
        >{{ old('description', $feature?->description) }}</textarea>

        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <div class="form-check form-switch">
            <input
                type="hidden"
                name="is_active"
                value="0"
            >

            <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                name="is_active"
                value="1"
                id="is_active"
                @checked(old('is_active', $feature?->is_active ?? true))
            >

            <label class="form-check-label" for="is_active">
                الخاصية نشطة
            </label>
        </div>
    </div>
</div>