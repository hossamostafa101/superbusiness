@extends('app.layouts.app')

@section('title', 'إعدادات النظام الطبي')
@section('page_title', 'إعدادات النظام الطبي')
@section('page_description', 'حدد نوع المنشأة الطبية وإعدادات الحجز والصفحة العامة.')

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.medical.settings.update', $workspace) }}">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label">نوع المنشأة <span class="text-danger">*</span></label>

                    <select name="facility_type" class="form-select @error('facility_type') is-invalid @enderror" required>
                        <option value="clinic" @selected(old('facility_type', $settings->facility_type) === 'clinic')>
                            عيادة
                        </option>

                        <option value="medical_center" @selected(old('facility_type', $settings->facility_type) === 'medical_center')>
                            مركز طبي
                        </option>

                        <option value="hospital" @selected(old('facility_type', $settings->facility_type) === 'hospital')>
                            مستشفى
                        </option>

                        <option value="lab" @selected(old('facility_type', $settings->facility_type) === 'lab')>
                            معمل تحاليل
                        </option>

                        <option value="scan_center" @selected(old('facility_type', $settings->facility_type) === 'scan_center')>
                            مركز أشعة
                        </option>
                    </select>

                    @error('facility_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">اسم الظهور</label>

                    <input
                        type="text"
                        name="display_name"
                        value="{{ old('display_name', $settings->display_name) }}"
                        class="form-control"
                        placeholder="{{ $workspace->name }}"
                    >
                </div>

                <div class="col-md-2">
                    <label class="form-label">العملة</label>

                    <input
                        type="text"
                        name="default_currency"
                        value="{{ old('default_currency', $settings->default_currency ?: 'EGP') }}"
                        class="form-control"
                        dir="ltr"
                        required
                    >
                </div>

                <div class="col-md-2">
                    <label class="form-label">مدة الزيارة</label>

                    <input
                        type="number"
                        name="default_visit_duration"
                        value="{{ old('default_visit_duration', $settings->default_visit_duration ?: 30) }}"
                        class="form-control"
                        min="5"
                        max="240"
                        required
                    >
                </div>

                <div class="col-12">
                    <label class="form-label">وصف المنشأة</label>

                    <textarea
                        name="description"
                        rows="4"
                        class="form-control"
                        placeholder="نبذة قصيرة تظهر في صفحة الحجز العامة"
                    >{{ old('description', $settings->description) }}</textarea>
                </div>

                <div class="col-md-3">
                    <label class="form-label d-block">تفعيل الحجز</label>

                    <input type="hidden" name="booking_enabled" value="0">

                    <div class="form-check form-switch mt-2">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="booking_enabled"
                            value="1"
                            @checked(old('booking_enabled', $settings->booking_enabled))
                        >
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label d-block">الدفع أونلاين</label>

                    <input type="hidden" name="online_payment_enabled" value="0">

                    <div class="form-check form-switch mt-2">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="online_payment_enabled"
                            value="1"
                            @checked(old('online_payment_enabled', $settings->online_payment_enabled))
                        >
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label d-block">بوابة المريض</label>

                    <input type="hidden" name="patient_portal_enabled" value="0">

                    <div class="form-check form-switch mt-2">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="patient_portal_enabled"
                            value="1"
                            @checked(old('patient_portal_enabled', $settings->patient_portal_enabled))
                        >
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label d-block">تحميل النتائج</label>

                    <input type="hidden" name="allow_results_download" value="0">

                    <div class="form-check form-switch mt-2">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="allow_results_download"
                            value="1"
                            @checked(old('allow_results_download', $settings->allow_results_download))
                        >
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label d-block">ملفات المرضى</label>

                    <input type="hidden" name="allow_patient_files" value="0">

                    <div class="form-check form-switch mt-2">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="allow_patient_files"
                            value="1"
                            @checked(old('allow_patient_files', $settings->allow_patient_files))
                        >
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label d-block">إشعارات واتساب</label>

                    <input type="hidden" name="whatsapp_notifications_enabled" value="0">

                    <div class="form-check form-switch mt-2">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="whatsapp_notifications_enabled"
                            value="1"
                            @checked(old('whatsapp_notifications_enabled', $settings->whatsapp_notifications_enabled))
                        >
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label d-block">إشعارات SMS</label>

                    <input type="hidden" name="sms_notifications_enabled" value="0">

                    <div class="form-check form-switch mt-2">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="sms_notifications_enabled"
                            value="1"
                            @checked(old('sms_notifications_enabled', $settings->sms_notifications_enabled))
                        >
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">اللون الأساسي</label>

                    <input
                        type="color"
                        name="primary_color"
                        value="{{ old('primary_color', $settings->primary_color ?: '#2563eb') }}"
                        class="form-control form-control-color"
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">اللون الثانوي</label>

                    <input
                        type="color"
                        name="secondary_color"
                        value="{{ old('secondary_color', $settings->secondary_color ?: '#0f172a') }}"
                        class="form-control form-control-color"
                    >
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.medical.dashboard', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    حفظ الإعدادات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection