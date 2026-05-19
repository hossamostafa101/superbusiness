<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">اسم الباقة <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $plan?->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            required
            placeholder="مثال: Starter"
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Slug <span class="text-danger">*</span></label>
        <input
            type="text"
            name="slug"
            value="{{ old('slug', $plan?->slug) }}"
            class="form-control @error('slug') is-invalid @enderror"
            required
            placeholder="مثال: starter"
        >
        @error('slug')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-body-secondary">
            يستخدم داخل الكود والروابط. مثال: free, starter, growth
        </small>
    </div>

    <div class="col-md-4">
        <label class="form-label">السعر الشهري <span class="text-danger">*</span></label>
        <input
            type="number"
            step="0.01"
            min="0"
            name="monthly_price"
            value="{{ old('monthly_price', $plan?->monthly_price ?? 0) }}"
            class="form-control @error('monthly_price') is-invalid @enderror"
            required
        >
        @error('monthly_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">السعر السنوي</label>
        <input
            type="number"
            step="0.01"
            min="0"
            name="yearly_price"
            value="{{ old('yearly_price', $plan?->yearly_price) }}"
            class="form-control @error('yearly_price') is-invalid @enderror"
        >
        @error('yearly_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">العملة <span class="text-danger">*</span></label>
        <input
            type="text"
            name="currency"
            value="{{ old('currency', $plan?->currency ?? 'EGP') }}"
            class="form-control @error('currency') is-invalid @enderror"
            required
        >
        @error('currency')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">الترتيب</label>
        <input
            type="number"
            min="0"
            name="sort_order"
            value="{{ old('sort_order', $plan?->sort_order ?? 0) }}"
            class="form-control @error('sort_order') is-invalid @enderror"
        >
        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-8">
        <label class="form-label">الوصف</label>
        <textarea
            name="description"
            rows="3"
            class="form-control @error('description') is-invalid @enderror"
            placeholder="وصف مختصر للباقة"
        >{{ old('description', $plan?->description) }}</textarea>

        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <div class="d-flex flex-wrap gap-4">
            <div class="form-check form-switch">
                <input type="hidden" name="is_free" value="0">
                <input
                    class="form-check-input"
                    type="checkbox"
                    role="switch"
                    name="is_free"
                    value="1"
                    id="is_free"
                    @checked(old('is_free', $plan?->is_free ?? false))
                >
                <label class="form-check-label" for="is_free">
                    باقة مجانية
                </label>
            </div>

            <div class="form-check form-switch">
                <input type="hidden" name="is_active" value="0">
                <input
                    class="form-check-input"
                    type="checkbox"
                    role="switch"
                    name="is_active"
                    value="1"
                    id="is_active"
                    @checked(old('is_active', $plan?->is_active ?? true))
                >
                <label class="form-check-label" for="is_active">
                    الباقة نشطة
                </label>
            </div>

            <div class="form-check form-switch">
                <input type="hidden" name="is_featured" value="0">
                <input
                    class="form-check-input"
                    type="checkbox"
                    role="switch"
                    name="is_featured"
                    value="1"
                    id="is_featured"
                    @checked(old('is_featured', $plan?->is_featured ?? false))
                >
                <label class="form-check-label" for="is_featured">
                    باقة مميزة
                </label>
            </div>
        </div>
    </div>

    <div class="col-12">
        <hr>

        <h5 class="mb-2">خصائص الباقة</h5>
        <p class="text-body-secondary small mb-3">
            استخدم -1 للقيم غير المحدودة. في خصائص Boolean استخدم 1 للتفعيل و 0 للتعطيل.
        </p>

        @error('features')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror

        @forelse($features as $module => $moduleFeatures)
            <div class="card mb-3">
                <div class="card-header">
                    <strong>{{ $module ?: 'عام' }}</strong>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        @foreach($moduleFeatures as $feature)
                            @php
                                $currentValue = $featureValues[$feature->id] ?? '';
                            @endphp

                            <div class="col-md-6">
                                <label class="form-label">
                                    {{ $feature->name }}
                                    <small class="text-body-secondary">({{ $feature->key }})</small>
                                </label>

                                @if($feature->type === 'boolean')
                                    <select
                                        name="features[{{ $feature->id }}]"
                                        class="form-select @error('features.' . $feature->id) is-invalid @enderror"
                                    >
                                        <option value="0" @selected((string) $currentValue === '0')>غير مفعل</option>
                                        <option value="1" @selected((string) $currentValue === '1')>مفعل</option>
                                    </select>
                                @else
                                    <input
                                        type="{{ $feature->type === 'limit' ? 'number' : 'text' }}"
                                        name="features[{{ $feature->id }}]"
                                        value="{{ $currentValue }}"
                                        class="form-control @error('features.' . $feature->id) is-invalid @enderror"
                                        placeholder="{{ $feature->type === 'limit' ? 'مثال: 100 أو -1' : 'مثال: basic' }}"
                                    >
                                @endif

                                @if($feature->description)
                                    <small class="text-body-secondary">
                                        {{ $feature->description }}
                                    </small>
                                @endif

                                @error('features.' . $feature->id)
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-warning">
                لا توجد خصائص نشطة. أضف خصائص أولًا من صفحة الخصائص.
            </div>
        @endforelse
    </div>
</div>