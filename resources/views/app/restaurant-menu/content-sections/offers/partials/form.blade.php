@php
    $selectedItemId = old('item_id', $offer?->item_id);
@endphp

<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label">الصنف المرتبط</label>

        <select name="item_id" class="form-select @error('item_id') is-invalid @enderror">
            <option value="">بدون صنف مرتبط</option>

            @foreach($items as $item)
                <option value="{{ $item->id }}" @selected((string) $selectedItemId === (string) $item->id)>
                    {{ $item->name }}
                    —
                    {{ number_format((float) ($item->sale_price ?: $item->price), 2) }}
                    {{ $item->currency }}
                </option>
            @endforeach
        </select>

        @error('item_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <div class="form-text">
            لو اخترت صنفًا، الضغط على العرض في المنيو سيفتح تفاصيل الصنف.
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label">شارة العرض</label>

        <input
            type="text"
            name="badge_text"
            value="{{ old('badge_text', $offer?->badge_text) }}"
            class="form-control @error('badge_text') is-invalid @enderror"
            placeholder="مثال: عرض اليوم"
        >

        @error('badge_text')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">عنوان العرض <span class="text-danger">*</span></label>

        <input
            type="text"
            name="title"
            value="{{ old('title', $offer?->title) }}"
            class="form-control @error('title') is-invalid @enderror"
            required
            placeholder="Combo Burger"
        >

        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">عنوان فرعي</label>

        <input
            type="text"
            name="subtitle"
            value="{{ old('subtitle', $offer?->subtitle) }}"
            class="form-control @error('subtitle') is-invalid @enderror"
            placeholder="وفر أكثر مع الكومبو"
        >

        @error('subtitle')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">الوصف</label>

        <textarea
            name="description"
            rows="3"
            class="form-control @error('description') is-invalid @enderror"
            placeholder="وصف مختصر للعرض"
        >{{ old('description', $offer?->description) }}</textarea>

        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>



    <div class="col-md-6">
    <label class="form-label">نص الزر</label>

    <input
        type="text"
        name="button_text"
        value="{{ old('button_text', $offer?->button_text) }}"
        class="form-control @error('button_text') is-invalid @enderror"
        placeholder="مثال: اطلب الآن"
    >

    @error('button_text')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-6">
    <label class="form-label">رابط الزر</label>

    <input
        type="text"
        name="button_url"
        value="{{ old('button_url', $offer?->button_url) }}"
        class="form-control @error('button_url') is-invalid @enderror"
        placeholder="https://..."
        dir="ltr"
    >

    @error('button_url')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    <div class="form-text">
        يستخدم إذا كان العرض لا يفتح صنفًا من المنيو.
    </div>
</div>


    <div class="col-md-4">
        <label class="form-label">السعر القديم</label>

        <input
            type="number"
            step="0.01"
            name="old_price"
            value="{{ old('old_price', $offer?->old_price) }}"
            class="form-control @error('old_price') is-invalid @enderror"
        >

        @error('old_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">السعر الجديد</label>

        <input
            type="number"
            step="0.01"
            name="new_price"
            value="{{ old('new_price', $offer?->new_price) }}"
            class="form-control @error('new_price') is-invalid @enderror"
        >

        @error('new_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">العملة</label>

        <input
            type="text"
            name="currency"
            value="{{ old('currency', $offer?->currency ?? 'EGP') }}"
            class="form-control @error('currency') is-invalid @enderror"
        >

        @error('currency')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">صورة العرض</label>

        <input
            type="file"
            name="image"
            class="form-control @error('image') is-invalid @enderror"
            accept="image/*"
        >

        @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        @if($offer?->imageUrl())
            <img
                src="{{ $offer->imageUrl() }}"
                class="rounded border mt-2"
                style="width:180px;height:110px;object-fit:cover;"
            >
        @endif
    </div>

    <div class="col-md-3">
        <label class="form-label">لون الخلفية</label>

        <input
            type="color"
            name="background_color"
            value="{{ old('background_color', $offer?->background_color ?? '#111827') }}"
            class="form-control form-control-color w-100"
        >
    </div>

    <div class="col-md-3">
        <label class="form-label">لون النص</label>

        <input
            type="color"
            name="text_color"
            value="{{ old('text_color', $offer?->text_color ?? '#ffffff') }}"
            class="form-control form-control-color w-100"
        >
    </div>

    <div class="col-md-3">
        <label class="form-label">الترتيب</label>

        <input
            type="number"
            name="sort_order"
            value="{{ old('sort_order', $offer?->sort_order ?? 0) }}"
            class="form-control"
            min="0"
        >
    </div>

    <div class="col-md-3">
        <label class="form-label d-block">الحالة</label>

        <input type="hidden" name="is_active" value="0">

        <div class="form-check form-switch mt-2">
            <input
                class="form-check-input"
                type="checkbox"
                name="is_active"
                value="1"
                @checked(old('is_active', $offer?->is_active ?? true))
            >

            <label class="form-check-label">
                ظاهر
            </label>
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label">يبدأ في</label>

        <input
            type="datetime-local"
            name="starts_at"
            value="{{ old('starts_at', $offer?->starts_at?->format('Y-m-d\TH:i')) }}"
            class="form-control @error('starts_at') is-invalid @enderror"
        >

        @error('starts_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">ينتهي في</label>

        <input
            type="datetime-local"
            name="ends_at"
            value="{{ old('ends_at', $offer?->ends_at?->format('Y-m-d\TH:i')) }}"
            class="form-control @error('ends_at') is-invalid @enderror"
        >

        @error('ends_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>