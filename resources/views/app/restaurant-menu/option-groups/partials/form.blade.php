{{-- resources/views/app/restaurant-menu/option-groups/partials/form.blade.php --}}
<div class="row g-4">
    <div class="col-12">
        <div class="alert alert-light border">
            <div class="fw-bold mb-1">
                الصنف:
                {{ $restaurantMenuItem->name }}
            </div>

            <div class="small text-muted">
                استخدم المجموعة لتنظيم اختيارات الصنف مثل: الصوص، الإضافات، درجة السكر، نوع اللبن.
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label">اسم المجموعة <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $restaurantItemOptionGroup?->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            required
            placeholder="مثال: اختر الصوص، إضافات، درجة السكر"
        >

        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">نوع الاختيار <span class="text-danger">*</span></label>

        @php
            $selectedType = old('type', $restaurantItemOptionGroup?->type ?? 'multiple');
        @endphp

        <select name="type" id="option_group_type" class="form-select @error('type') is-invalid @enderror" required>
            <option value="single" @selected($selectedType === 'single')>
                اختيار واحد فقط
            </option>

            <option value="multiple" @selected($selectedType === 'multiple')>
                اختيارات متعددة
            </option>
        </select>

        @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted">
            مثال: درجة السكر = اختيار واحد، إضافات = اختيارات متعددة.
        </small>
    </div>

    <div class="col-md-4">
        <label class="form-label">الحد الأدنى للاختيارات</label>
        <input
            type="number"
            min="0"
            max="100"
            name="min_choices"
            id="min_choices"
            value="{{ old('min_choices', $restaurantItemOptionGroup?->min_choices ?? 0) }}"
            class="form-control @error('min_choices') is-invalid @enderror"
        >

        @error('min_choices')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted">
            لو المجموعة إجبارية اجعله 1 على الأقل.
        </small>
    </div>

    <div class="col-md-4">
        <label class="form-label">الحد الأقصى للاختيارات</label>
        <input
            type="number"
            min="1"
            max="100"
            name="max_choices"
            id="max_choices"
            value="{{ old('max_choices', $restaurantItemOptionGroup?->max_choices) }}"
            class="form-control @error('max_choices') is-invalid @enderror"
            placeholder="اتركه فارغًا لغير محدد"
        >

        @error('max_choices')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted">
            في نوع اختيار واحد سيتم ضبطه تلقائيًا على 1.
        </small>
    </div>

    <div class="col-md-4">
        <label class="form-label">الترتيب</label>
        <input
            type="number"
            min="0"
            name="sort_order"
            value="{{ old('sort_order', $restaurantItemOptionGroup?->sort_order ?? 0) }}"
            class="form-control @error('sort_order') is-invalid @enderror"
        >

        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted">
            الأقل يظهر أولًا.
        </small>
    </div>

    <div class="col-md-6">
        <div class="form-check form-switch">
            <input type="hidden" name="is_required" value="0">

            <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                name="is_required"
                value="1"
                id="is_required"
                @checked(old('is_required', $restaurantItemOptionGroup?->is_required ?? false))
            >

            <label class="form-check-label" for="is_required">
                هذه المجموعة إجبارية
            </label>
        </div>

        <small class="text-muted">
            العميل يجب أن يختار منها قبل إضافة الصنف للطلب.
        </small>
    </div>

    <div class="col-md-6">
        <div class="form-check form-switch">
            <input type="hidden" name="is_active" value="0">

            <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                name="is_active"
                value="1"
                id="is_active"
                @checked(old('is_active', $restaurantItemOptionGroup?->is_active ?? true))
            >

            <label class="form-check-label" for="is_active">
                المجموعة نشطة
            </label>
        </div>

        <small class="text-muted">
            المجموعات غير النشطة لا تظهر في المنيو العام.
        </small>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const typeSelect = document.getElementById('option_group_type');
        const minInput = document.getElementById('min_choices');
        const maxInput = document.getElementById('max_choices');
        const requiredInput = document.getElementById('is_required');

        function syncTypeRules() {
            if (!typeSelect || !maxInput) {
                return;
            }

            if (typeSelect.value === 'single') {
                maxInput.value = 1;
                maxInput.setAttribute('readonly', 'readonly');
            } else {
                maxInput.removeAttribute('readonly');
            }
        }

        function syncRequiredRules() {
            if (!requiredInput || !minInput) {
                return;
            }

            if (requiredInput.checked && parseInt(minInput.value || '0', 10) < 1) {
                minInput.value = 1;
            }
        }

        typeSelect?.addEventListener('change', syncTypeRules);
        requiredInput?.addEventListener('change', syncRequiredRules);

        syncTypeRules();
        syncRequiredRules();
    })();
</script>
@endpush