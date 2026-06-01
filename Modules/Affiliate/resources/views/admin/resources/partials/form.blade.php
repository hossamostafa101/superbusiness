<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label">التخصص</label>

        <select name="specification_id" class="form-select">
            <option value="">عام لكل التخصصات</option>

            @foreach($specifications as $specification)
                <option value="{{ $specification->id }}" @selected((string) old('specification_id', $resource?->specification_id) === (string) $specification->id)>
                    {{ $specification->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">النوع</label>

        <select name="type" class="form-select">
            <option value="text" @selected(old('type', $resource?->type ?? 'text') === 'text')>نص</option>
            <option value="link" @selected(old('type', $resource?->type) === 'link')>رابط</option>
            <option value="video" @selected(old('type', $resource?->type) === 'video')>فيديو</option>
            <option value="image" @selected(old('type', $resource?->type) === 'image')>صورة</option>
            <option value="pdf" @selected(old('type', $resource?->type) === 'pdf')>PDF</option>
            <option value="demo" @selected(old('type', $resource?->type) === 'demo')>ديمو</option>
            <option value="whatsapp_script" @selected(old('type', $resource?->type) === 'whatsapp_script')>نص واتساب</option>
            <option value="other" @selected(old('type', $resource?->type) === 'other')>أخرى</option>
        </select>
    </div>

    <div class="col-md-8">
        <label class="form-label">العنوان</label>

        <input
            type="text"
            name="title"
            value="{{ old('title', $resource?->title) }}"
            class="form-control @error('title') is-invalid @enderror"
            required
        >

        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">الترتيب</label>

        <input
            type="number"
            name="sort_order"
            value="{{ old('sort_order', $resource?->sort_order ?? 0) }}"
            class="form-control"
            min="0"
        >
    </div>

    <div class="col-12">
        <label class="form-label">الوصف</label>

        <textarea name="description" rows="3" class="form-control">{{ old('description', $resource?->description) }}</textarea>
    </div>

    <div class="col-12">
        <label class="form-label">المحتوى / النص</label>

        <textarea name="content" rows="6" class="form-control">{{ old('content', $resource?->content) }}</textarea>

        <div class="form-text">
            يستخدم للنصوص الجاهزة أو سكربت واتساب أو شرح المنتج.
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label">الرابط</label>

        <input
            type="url"
            name="url"
            value="{{ old('url', $resource?->url) }}"
            class="form-control"
            dir="ltr"
            placeholder="https://..."
        >
    </div>

    <div class="col-md-6">
        <label class="form-label">ملف</label>

        <input
            type="file"
            name="file"
            class="form-control"
            accept=".jpg,.jpeg,.png,.webp,.pdf"
        >

        @if($resource?->file_path)
            <div class="form-text">
                يوجد ملف حالي:
                <a href="{{ asset('storage/' . $resource->file_path) }}" target="_blank">
                    فتح
                </a>
            </div>
        @endif
    </div>

    <div class="col-12">
        <input type="hidden" name="is_active" value="0">

        <label class="form-label d-block">الحالة</label>

        <div class="form-check form-switch">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                class="form-check-input"
                @checked(old('is_active', $resource?->is_active ?? true))
            >

            <label class="form-check-label">
                نشط
            </label>
        </div>
    </div>
</div>