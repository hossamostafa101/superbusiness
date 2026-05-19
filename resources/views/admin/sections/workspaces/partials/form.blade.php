<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">اسم مساحة العمل <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $workspace?->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            required
            placeholder="مثال: Luna Accessories"
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">الرابط المختصر / Slug <span class="text-danger">*</span></label>
        <input
            type="text"
            name="slug"
            value="{{ old('slug', $workspace?->slug) }}"
            class="form-control @error('slug') is-invalid @enderror"
            required
            placeholder="مثال: luna-accessories"
        >
        @error('slug')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-body-secondary">
            سيستخدم لاحقًا في رابط الصفحة العامة.
        </small>
    </div>

    <div class="col-md-6">
        <label class="form-label">المالك <span class="text-danger">*</span></label>
        <select name="owner_id" class="form-select @error('owner_id') is-invalid @enderror" required>
            <option value="">اختر المالك</option>

            @foreach($owners as $owner)
                <option
                    value="{{ $owner->id }}"
                    @selected((int) old('owner_id', $workspace?->owner_id) === (int) $owner->id)
                >
                    {{ $owner->name }} — {{ $owner->email }}
                    @if($owner->phone)
                        — {{ $owner->phone }}
                    @endif
                </option>
            @endforeach
        </select>

        @error('owner_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        @if($owners->isEmpty())
            <small class="text-danger">
                لا يوجد مستخدمون نشطون. أضف مستخدمًا أولًا.
            </small>
        @endif
    </div>

    <div class="col-md-3">
        <label class="form-label">النوع <span class="text-danger">*</span></label>
        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
            @php
                $selectedType = old('type', $workspace?->type ?? 'business_page');
            @endphp

            <option value="business_page" @selected($selectedType === 'business_page')>
                Business Page
            </option>

            <option value="menu" @selected($selectedType === 'menu')>
                Menu
            </option>

            <option value="clinic" @selected($selectedType === 'clinic')>
                Clinic
            </option>

            <option value="agency" @selected($selectedType === 'agency')>
                Agency
            </option>
        </select>

        @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">الحالة <span class="text-danger">*</span></label>
        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
            @php
                $selectedStatus = old('status', $workspace?->status ?? 'active');
            @endphp

            <option value="active" @selected($selectedStatus === 'active')>نشطة</option>
            <option value="pending" @selected($selectedStatus === 'pending')>قيد الانتظار</option>
            <option value="suspended" @selected($selectedStatus === 'suspended')>موقوفة</option>
            <option value="cancelled" @selected($selectedStatus === 'cancelled')>ملغاة</option>
        </select>

        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">تاريخ انتهاء التجربة</label>
        <input
            type="datetime-local"
            name="trial_ends_at"
            value="{{ old('trial_ends_at', $workspace?->trial_ends_at?->format('Y-m-d\TH:i')) }}"
            class="form-control @error('trial_ends_at') is-invalid @enderror"
        >
        @error('trial_ends_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-body-secondary">
            اختياري. عند الإنشاء سيتم إنشاء اشتراك مجاني افتراضي لمدة 14 يومًا إذا وجدت خطة free.
        </small>
    </div>
</div>