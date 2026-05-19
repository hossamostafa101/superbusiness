{{-- resources/views/admin/plans/_form.blade.php --}}

@php
    /** @var \App\Models\Plan|null $plan */
    $planModel  = $plan ?? null;
    $formAction = $action ?? '';
    $formMethod = $method ?? 'POST';

    // تجهيز features (سواء من old() أو من الخطة نفسها)
    $features = old('features');

    if ($features === null) {
        $features = $planModel && is_array($planModel->features_json)
            ? $planModel->features_json
            : [];
    }

    if (empty($features)) {
        $features = [''];
    }
@endphp

@if($errors->any())
    <div class="alert alert-danger">
        <div class="fw-bold mb-1">من فضلك راجع الأخطاء التالية:</div>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ $formAction }}" method="POST">
    @csrf

    @if(in_array($formMethod, ['PUT','PATCH','DELETE'], true))
        @method($formMethod)
    @endif

    {{-- الاسم --}}
    <div class="mb-3 row">
        <label for="name" class="col-md-2 col-form-label">اسم الخطة</label>
        <div class="col-md-6">
            <input type="text"
                   id="name"
                   name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $planModel->name ?? '') }}"
                   required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- السعر --}}
    <div class="mb-3 row">
        <label for="price" class="col-md-2 col-form-label">السعر</label>
        <div class="col-md-4">
            <input type="number"
                   id="price"
                   name="price"
                   class="form-control @error('price') is-invalid @enderror"
                   step="0.01"
                   min="0"
                   value="{{ old('price', $planModel->price ?? 0) }}"
                   required>
            @error('price')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- المدة بالأيام --}}
    <div class="mb-3 row">
        <label for="duration_days" class="col-md-2 col-form-label">المدة (بالأيام)</label>
        <div class="col-md-3">
            <input type="number"
                   id="duration_days"
                   name="duration_days"
                   class="form-control @error('duration_days') is-invalid @enderror"
                   min="1"
                   value="{{ old('duration_days', $planModel->duration_days ?? 30) }}"
                   required>
            @error('duration_days')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- المميزات (features_json) --}}
    <div class="mb-3 row">
        <label class="col-md-2 col-form-label">مميزات الخطة</label>
        <div class="col-md-8">

            <div id="features-wrapper">
                @foreach($features as $idx => $feature)
                    <div class="input-group mb-2 feature-row">
                        <input type="text"
                               name="features[]"
                               class="form-control"
                               placeholder="مثال: عدد فروع غير محدود، عدد أصناف حتى 500..."
                               value="{{ $feature }}">
                        <button type="button"
                                class="btn btn-outline-danger btn-remove-feature"
                                title="حذف">
                            &times;
                        </button>
                    </div>
                @endforeach
            </div>

            <button type="button"
                    class="btn btn-outline-secondary btn-sm"
                    id="btn-add-feature">
                + إضافة سطر جديد
            </button>

            <div class="form-text mt-1">
                يتم تخزين هذه القائمة داخل حقل <code>features_json</code> كـ array.
            </div>
        </div>
    </div>

    {{-- حالة الخطة --}}
    <div class="mb-3 row">
        <label class="col-md-2 col-form-label">الحالة</label>
        <div class="col-md-4">
            <div class="form-check form-switch">
                <input class="form-check-input"
                       type="checkbox"
                       id="is_active"
                       name="is_active"
                       {{ old('is_active', $planModel->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">
                    الخطة مفعّلة
                </label>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">
            حفظ
        </button>
        <a href="{{ route('admin.plans.index') }}" class="btn btn-outline-secondary">
            إلغاء
        </a>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const wrapper = document.getElementById('features-wrapper');
    const addBtn  = document.getElementById('btn-add-feature');

    if (wrapper && addBtn) {
        addBtn.addEventListener('click', function () {
            const row = document.createElement('div');
            row.className = 'input-group mb-2 feature-row';
            row.innerHTML = `
                <input type="text"
                       name="features[]"
                       class="form-control"
                       placeholder="ميزة جديدة...">
                <button type="button"
                        class="btn btn-outline-danger btn-remove-feature"
                        title="حذف">&times;</button>
            `;
            wrapper.appendChild(row);
        });

        wrapper.addEventListener('click', function (e) {
            if (e.target.classList.contains('btn-remove-feature')) {
                const row = e.target.closest('.feature-row');
                if (row) {
                    row.remove();
                }
            }
        });
    }
});
</script>
@endpush
