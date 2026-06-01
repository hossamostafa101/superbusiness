@php
    $items = old('items');

    if ($items === null) {
        if (!empty($prescription)) {
            $items = $prescription->items->map(function ($item) {
                return [
                    'medicine_name' => $item->medicine_name,
                    'dosage' => $item->dosage,
                    'frequency' => $item->frequency,
                    'duration' => $item->duration,
                    'route' => $item->route,
                    'instructions' => $item->instructions,
                ];
            })->toArray();
        } else {
            $items = [
                ['medicine_name' => '', 'dosage' => '', 'frequency' => '', 'duration' => '', 'route' => '', 'instructions' => ''],
            ];
        }
    }

    $visitValue = old('visit_id', $prescription?->visit_id ?? $visit?->id ?? request('visit_id'));
@endphp

@if(empty($prescription))
    <input type="hidden" name="visit_id" value="{{ $visitValue }}">
@endif

<div class="row g-4">
    @if(!empty($visit))
        <div class="col-12">
            <div class="alert alert-info rounded-4 mb-0">
                <div class="fw-bold mb-1">
                    الزيارة المرتبطة: {{ $visit->visit_number }}
                </div>

                <div>
                    المريض:
                    {{ $visit->patient?->full_name ?: $visit->patient_name }}
                    —
                    الطبيب:
                    {{ $visit->staff?->name ?: $visit->staff_name ?: '-' }}
                </div>
            </div>
        </div>
    @elseif(!empty($prescription) && $prescription->visit)
        <div class="col-12">
            <div class="alert alert-info rounded-4 mb-0">
                <div class="fw-bold mb-1">
                    الزيارة المرتبطة:
                    <a href="{{ route('app.medical.visits.show', [$workspace, $prescription->visit]) }}">
                        {{ $prescription->visit->visit_number }}
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div class="col-md-4">
        <label class="form-label">تاريخ الإصدار</label>

        <input
            type="datetime-local"
            name="issued_at"
            value="{{ old('issued_at', !empty($prescription) && $prescription->issued_at ? $prescription->issued_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
            class="form-control"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">الحالة</label>

        <select name="status" class="form-select">
            <option value="draft" @selected(old('status', $prescription?->status ?? 'issued') === 'draft')>
                مسودة
            </option>

            <option value="issued" @selected(old('status', $prescription?->status ?? 'issued') === 'issued')>
                صادرة
            </option>

            <option value="cancelled" @selected(old('status', $prescription?->status) === 'cancelled')>
                ملغية
            </option>
        </select>
    </div>

    <div class="col-12">
        <label class="form-label">ملخص التشخيص</label>

        <textarea
            name="diagnosis_summary"
            rows="3"
            class="form-control"
        >{{ old('diagnosis_summary', $prescription?->diagnosis_summary ?? $visit?->diagnosis ?? '') }}</textarea>
    </div>

    <div class="col-12">
        <label class="form-label">تعليمات عامة</label>

        <textarea
            name="instructions"
            rows="3"
            class="form-control"
        >{{ old('instructions', $prescription?->instructions) }}</textarea>
    </div>

    <div class="col-12">
        <label class="form-label">ملاحظات</label>

        <textarea
            name="notes"
            rows="3"
            class="form-control"
        >{{ old('notes', $prescription?->notes) }}</textarea>
    </div>
</div>

<hr class="my-4">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h5 fw-bold mb-0">
        الأدوية
    </h2>

    <button type="button" class="btn btn-sm btn-outline-dark" id="addPrescriptionItemBtn">
        <i class="bi bi-plus-circle"></i>
        إضافة دواء
    </button>
</div>

<div id="prescriptionItemsWrap" class="d-grid gap-3">
    @foreach($items as $index => $item)
        <div class="prescription-item-card border rounded-4 p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <strong>
                    دواء #<span class="item-number">{{ $loop->iteration }}</span>
                </strong>

                <button type="button" class="btn btn-sm btn-outline-danger remove-prescription-item">
                    حذف
                </button>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">اسم الدواء</label>
                    <input
                        type="text"
                        name="items[{{ $index }}][medicine_name]"
                        value="{{ $item['medicine_name'] ?? '' }}"
                        class="form-control"
                        placeholder="مثال: Augmentin"
                    >
                </div>

                <div class="col-md-2">
                    <label class="form-label">الجرعة</label>
                    <input
                        type="text"
                        name="items[{{ $index }}][dosage]"
                        value="{{ $item['dosage'] ?? '' }}"
                        class="form-control"
                        placeholder="1g"
                    >
                </div>

                <div class="col-md-2">
                    <label class="form-label">التكرار</label>
                    <input
                        type="text"
                        name="items[{{ $index }}][frequency]"
                        value="{{ $item['frequency'] ?? '' }}"
                        class="form-control"
                        placeholder="مرتين يوميًا"
                    >
                </div>

                <div class="col-md-2">
                    <label class="form-label">المدة</label>
                    <input
                        type="text"
                        name="items[{{ $index }}][duration]"
                        value="{{ $item['duration'] ?? '' }}"
                        class="form-control"
                        placeholder="5 أيام"
                    >
                </div>

                <div class="col-md-2">
                    <label class="form-label">الطريقة</label>
                    <input
                        type="text"
                        name="items[{{ $index }}][route]"
                        value="{{ $item['route'] ?? '' }}"
                        class="form-control"
                        placeholder="فموي"
                    >
                </div>

                <div class="col-12">
                    <label class="form-label">تعليمات الدواء</label>
                    <textarea
                        name="items[{{ $index }}][instructions]"
                        rows="2"
                        class="form-control"
                        placeholder="بعد الأكل / قبل النوم..."
                    >{{ $item['instructions'] ?? '' }}</textarea>
                </div>
            </div>
        </div>
    @endforeach
</div>

@push('scripts')
<script>
(function () {
    const wrap = document.getElementById('prescriptionItemsWrap');
    const addBtn = document.getElementById('addPrescriptionItemBtn');

    if (!wrap || !addBtn) {
        return;
    }

    function renumber() {
        wrap.querySelectorAll('.prescription-item-card').forEach(function (card, index) {
            const number = card.querySelector('.item-number');

            if (number) {
                number.textContent = index + 1;
            }

            card.querySelectorAll('input, textarea').forEach(function (input) {
                input.name = input.name.replace(/items\[\d+\]/, 'items[' + index + ']');
            });
        });
    }

    function itemTemplate(index) {
        return `
            <div class="prescription-item-card border rounded-4 p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <strong>دواء #<span class="item-number">${index + 1}</span></strong>

                    <button type="button" class="btn btn-sm btn-outline-danger remove-prescription-item">
                        حذف
                    </button>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">اسم الدواء</label>
                        <input type="text" name="items[${index}][medicine_name]" class="form-control" placeholder="مثال: Augmentin">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">الجرعة</label>
                        <input type="text" name="items[${index}][dosage]" class="form-control" placeholder="1g">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">التكرار</label>
                        <input type="text" name="items[${index}][frequency]" class="form-control" placeholder="مرتين يوميًا">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">المدة</label>
                        <input type="text" name="items[${index}][duration]" class="form-control" placeholder="5 أيام">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">الطريقة</label>
                        <input type="text" name="items[${index}][route]" class="form-control" placeholder="فموي">
                    </div>

                    <div class="col-12">
                        <label class="form-label">تعليمات الدواء</label>
                        <textarea name="items[${index}][instructions]" rows="2" class="form-control" placeholder="بعد الأكل / قبل النوم..."></textarea>
                    </div>
                </div>
            </div>
        `;
    }

    addBtn.addEventListener('click', function () {
        const index = wrap.querySelectorAll('.prescription-item-card').length;
        wrap.insertAdjacentHTML('beforeend', itemTemplate(index));
    });

    wrap.addEventListener('click', function (event) {
        const removeBtn = event.target.closest('.remove-prescription-item');

        if (!removeBtn) {
            return;
        }

        const cards = wrap.querySelectorAll('.prescription-item-card');

        if (cards.length <= 1) {
            removeBtn.closest('.prescription-item-card').querySelectorAll('input, textarea').forEach(function (input) {
                input.value = '';
            });

            return;
        }

        removeBtn.closest('.prescription-item-card').remove();
        renumber();
    });
})();
</script>
@endpush