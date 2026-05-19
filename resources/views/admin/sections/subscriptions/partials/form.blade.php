<div class="row g-3">
    @if(! $isEdit)
        <div class="col-md-6">
            <label class="form-label">مساحة العمل <span class="text-danger">*</span></label>

            <select name="workspace_id" class="form-select @error('workspace_id') is-invalid @enderror" required>
                <option value="">اختر مساحة العمل</option>

                @foreach($workspaces as $workspace)
                    <option
                        value="{{ $workspace->id }}"
                        @selected((int) old('workspace_id') === (int) $workspace->id)
                    >
                        {{ $workspace->name }}
                        —
                        {{ $workspace->slug }}
                        @if($workspace->owner)
                            —
                            {{ $workspace->owner->name }}
                        @endif
                    </option>
                @endforeach
            </select>

            @error('workspace_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    @else
        <div class="col-md-6">
            <label class="form-label">مساحة العمل</label>

            <input
                type="text"
                class="form-control"
                value="{{ $subscription->workspace?->name }} — {{ $subscription->workspace?->slug }}"
                disabled
            >

            <small class="text-body-secondary">
                لا يتم تغيير مساحة العمل بعد إنشاء الاشتراك.
            </small>
        </div>
    @endif

    <div class="col-md-6">
        <label class="form-label">الباقة <span class="text-danger">*</span></label>

        <select name="plan_id" class="form-select @error('plan_id') is-invalid @enderror" required>
            <option value="">اختر الباقة</option>

            @foreach($plans as $plan)
                <option
                    value="{{ $plan->id }}"
                    @selected((int) old('plan_id', $subscription?->plan_id) === (int) $plan->id)
                >
                    {{ $plan->name }}
                    —
                    شهري: {{ number_format((float) $plan->monthly_price, 2) }} {{ $plan->currency }}
                    @if($plan->yearly_price)
                        —
                        سنوي: {{ number_format((float) $plan->yearly_price, 2) }} {{ $plan->currency }}
                    @endif
                </option>
            @endforeach
        </select>

        @error('plan_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">الحالة <span class="text-danger">*</span></label>

        @php
            $selectedStatus = old('status', $subscription?->status ?? 'active');
        @endphp

        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="trialing" @selected($selectedStatus === 'trialing')>تجربة</option>
            <option value="active" @selected($selectedStatus === 'active')>نشط</option>
            <option value="past_due" @selected($selectedStatus === 'past_due')>متأخر</option>
            <option value="cancelled" @selected($selectedStatus === 'cancelled')>ملغي</option>
            <option value="expired" @selected($selectedStatus === 'expired')>منتهي</option>
        </select>

        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">دورة الدفع <span class="text-danger">*</span></label>

        @php
            $selectedCycle = old('billing_cycle', $subscription?->billing_cycle ?? 'monthly');
        @endphp

        <select name="billing_cycle" class="form-select @error('billing_cycle') is-invalid @enderror" required>
            <option value="monthly" @selected($selectedCycle === 'monthly')>شهري</option>
            <option value="yearly" @selected($selectedCycle === 'yearly')>سنوي</option>
        </select>

        @error('billing_cycle')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">تاريخ البداية</label>

        <input
            type="datetime-local"
            name="starts_at"
            value="{{ old('starts_at', $subscription?->starts_at?->format('Y-m-d\TH:i')) }}"
            class="form-control @error('starts_at') is-invalid @enderror"
        >

        @error('starts_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-body-secondary">
            لو تركته فارغًا سيتم استخدام الوقت الحالي.
        </small>
    </div>

    <div class="col-md-6">
        <label class="form-label">نهاية التجربة</label>

        <input
            type="datetime-local"
            name="trial_ends_at"
            value="{{ old('trial_ends_at', $subscription?->trial_ends_at?->format('Y-m-d\TH:i')) }}"
            class="form-control @error('trial_ends_at') is-invalid @enderror"
        >

        @error('trial_ends_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">تاريخ الانتهاء</label>

        <input
            type="datetime-local"
            name="ends_at"
            value="{{ old('ends_at', $subscription?->ends_at?->format('Y-m-d\TH:i')) }}"
            class="form-control @error('ends_at') is-invalid @enderror"
        >

        @error('ends_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-body-secondary">
            لو تركته فارغًا عند الإنشاء سيتم حسابه تلقائيًا حسب الدورة.
        </small>
    </div>
</div>