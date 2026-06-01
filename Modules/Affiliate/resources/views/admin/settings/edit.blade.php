@extends('admin.layouts.app')

@section('title', 'إعدادات برنامج الشركاء')

@section('content')
<div class="card">
    <div class="card-body">
        <h1 class="h4 fw-bold mb-4">
            إعدادات برنامج الشركاء
        </h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.affiliate.settings.update') }}">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label">نوع العمولة</label>

                    <select name="commission_type" class="form-select">
                        <option value="percentage" @selected(old('commission_type', $settings->commission_type) === 'percentage')>
                            نسبة مئوية
                        </option>

                        <option value="fixed" @selected(old('commission_type', $settings->commission_type) === 'fixed')>
                            مبلغ ثابت
                        </option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">قيمة العمولة</label>

                    <input
                        type="number"
                        name="commission_value"
                        value="{{ old('commission_value', $settings->commission_value) }}"
                        class="form-control"
                        step="0.01"
                        min="0"
                        required
                    >

                    <div class="form-text">
                        لو النوع نسبة، اكتب مثلًا 20. لو ثابت، اكتب المبلغ.
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">العملة</label>

                    <input
                        type="text"
                        name="currency"
                        value="{{ old('currency', $settings->currency) }}"
                        class="form-control"
                        dir="ltr"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">مدة الحجز بالأيام</label>

                    <input
                        type="number"
                        name="hold_days"
                        value="{{ old('hold_days', $settings->hold_days) }}"
                        class="form-control"
                        min="0"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">الحد الأدنى للسحب</label>

                    <input
                        type="number"
                        name="minimum_withdrawal_amount"
                        value="{{ old('minimum_withdrawal_amount', $settings->minimum_withdrawal_amount) }}"
                        class="form-control"
                        step="0.01"
                        min="0"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">بونص التسجيل</label>

                    <input
                        type="number"
                        name="signup_bonus_amount"
                        value="{{ old('signup_bonus_amount', $settings->signup_bonus_amount) }}"
                        class="form-control"
                        step="0.01"
                        min="0"
                        required
                    >
                </div>

                <div class="col-md-6">
                    <input type="hidden" name="signup_bonus_enabled" value="0">

                    <label class="form-label d-block">تفعيل بونص التسجيل</label>

                    <div class="form-check form-switch">
                        <input
                            type="checkbox"
                            name="signup_bonus_enabled"
                            value="1"
                            class="form-check-input"
                            @checked(old('signup_bonus_enabled', $settings->signup_bonus_enabled))
                        >
                    </div>
                </div>

                <div class="col-md-6">
                    <input type="hidden" name="is_active" value="0">

                    <label class="form-label d-block">تفعيل برنامج الشركاء</label>

                    <div class="form-check form-switch">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            class="form-check-input"
                            @checked(old('is_active', $settings->is_active))
                        >
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button class="btn btn-primary">
                    حفظ الإعدادات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection