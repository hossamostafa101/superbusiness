{{-- resources/views/app/restaurant-menu/tables/partials/form.blade.php --}}
<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label">الفرع <span class="text-danger">*</span></label>
        <select name="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
            <option value="">اختر الفرع</option>

            @foreach($branches as $branch)
                <option
                    value="{{ $branch->id }}"
                    @selected((int) old('branch_id', $restaurantTable?->branch_id) === (int) $branch->id)
                >
                    {{ $branch->name }}
                </option>
            @endforeach
        </select>

        @error('branch_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">اسم الطاولة <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $restaurantTable?->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            required
            placeholder="مثال: طاولة 1، VIP 3، Outdoor 5"
        >

        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">رقم الطاولة <span class="text-danger">*</span></label>
        <input
            type="text"
            name="number"
            value="{{ old('number', $restaurantTable?->number) }}"
            class="form-control @error('number') is-invalid @enderror"
            required
            placeholder="مثال: 1"
        >

        @error('number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted">
            يجب ألا يتكرر داخل نفس الفرع.
        </small>
    </div>

    <div class="col-md-4">
        <label class="form-label">عدد المقاعد</label>
        <input
            type="number"
            name="seats"
            value="{{ old('seats', $restaurantTable?->seats) }}"
            class="form-control @error('seats') is-invalid @enderror"
            min="1"
            max="100"
            placeholder="مثال: 4"
        >

        @error('seats')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">الترتيب</label>
        <input
            type="number"
            name="sort_order"
            value="{{ old('sort_order', $restaurantTable?->sort_order ?? 0) }}"
            class="form-control @error('sort_order') is-invalid @enderror"
            min="0"
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
            <input type="hidden" name="is_active" value="0">

            <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                name="is_active"
                value="1"
                id="is_active"
                @checked(old('is_active', $restaurantTable?->is_active ?? true))
            >

            <label class="form-check-label" for="is_active">
                الطاولة نشطة
            </label>
        </div>

        <small class="text-muted">
            الطاولات غير النشطة لن تعمل روابط QR الخاصة بها.
        </small>
    </div>

    @if($isEdit && $restaurantTable?->code)
        <div class="col-12">
            <hr>

            <label class="form-label">كود QR</label>
            <input
                type="text"
                value="{{ $restaurantTable->code }}"
                class="form-control"
                dir="ltr"
                readonly
            >

            <small class="text-muted">
                يتم توليده تلقائيًا ولا يتم تعديله يدويًا.
            </small>
        </div>
    @endif
</div>