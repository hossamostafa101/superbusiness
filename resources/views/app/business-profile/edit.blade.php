{{-- resources/views/app/business-profile/edit.blade.php --}}
@extends('app.layouts.app')

@section('title', 'بيانات الصفحة')
@section('page_title', 'بيانات الصفحة')
@section('page_description', 'عدّل بيانات البزنس التي تظهر في الصفحة العامة.')

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.business-profile.update', $workspace) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">اسم البزنس <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        name="display_name"
                        value="{{ old('display_name', $profile?->display_name ?? $workspace->name) }}"
                        class="form-control @error('display_name') is-invalid @enderror"
                        required
                    >
                    @error('display_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">جملة قصيرة</label>
                    <input
                        type="text"
                        name="tagline"
                        value="{{ old('tagline', $profile?->tagline) }}"
                        class="form-control @error('tagline') is-invalid @enderror"
                        placeholder="مثال: منتجات هاند ميد بتصميمات مميزة"
                    >
                    @error('tagline')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">وصف البزنس</label>
                    <textarea
                        name="description"
                        rows="5"
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="اكتب وصفًا مختصرًا عن نشاطك"
                    >{{ old('description', $profile?->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">الشعار</label>
                    <input
                        type="file"
                        name="logo"
                        class="form-control @error('logo') is-invalid @enderror"
                        accept="image/*"
                    >
                    @error('logo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    @if($profile?->logo)
                        <div class="mt-3">
                            <img src="{{ asset('storage/' . $profile->logo) }}" class="rounded border" style="height: 80px; width: 80px; object-fit: cover;" alt="Logo">
                        </div>
                    @endif
                </div>

                <div class="col-md-6">
                    <label class="form-label">صورة الغلاف</label>
                    <input
                        type="file"
                        name="cover_image"
                        class="form-control @error('cover_image') is-invalid @enderror"
                        accept="image/*"
                    >
                    @error('cover_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    @if($profile?->cover_image)
                        <div class="mt-3">
                            <img src="{{ asset('storage/' . $profile->cover_image) }}" class="rounded border w-100" style="height: 120px; object-fit: cover;" alt="Cover">
                        </div>
                    @endif
                </div>

                <div class="col-md-4">
                    <label class="form-label">رقم واتساب</label>
                    <input
                        type="text"
                        name="whatsapp_number"
                        value="{{ old('whatsapp_number', $profile?->whatsapp_number) }}"
                        class="form-control @error('whatsapp_number') is-invalid @enderror"
                        placeholder="2010xxxxxxxx"
                    >
                    @error('whatsapp_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">اكتب الرقم بصيغة دولية بدون +</small>
                </div>

                <div class="col-md-4">
                    <label class="form-label">الهاتف</label>
                    <input
                        type="text"
                        name="phone"
                        value="{{ old('phone', $profile?->phone) }}"
                        class="form-control @error('phone') is-invalid @enderror"
                    >
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $profile?->email) }}"
                        class="form-control @error('email') is-invalid @enderror"
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">العنوان</label>
                    <input
                        type="text"
                        name="address"
                        value="{{ old('address', $profile?->address) }}"
                        class="form-control @error('address') is-invalid @enderror"
                    >
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">رابط اللوكيشن</label>
                    <input
                        type="url"
                        name="location_url"
                        value="{{ old('location_url', $profile?->location_url) }}"
                        class="form-control @error('location_url') is-invalid @enderror"
                        placeholder="Google Maps URL"
                    >
                    @error('location_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">لون الخلفية</label>
                    <input
                        type="color"
                        name="theme_color"
                        value="{{ old('theme_color', $profile?->theme_color ?? '#111827') }}"
                        class="form-control form-control-color @error('theme_color') is-invalid @enderror"
                    >
                    @error('theme_color')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">لون الأزرار</label>
                    <input
                        type="color"
                        name="button_color"
                        value="{{ old('button_color', $profile?->button_color ?? '#2563eb') }}"
                        class="form-control form-control-color @error('button_color') is-invalid @enderror"
                    >
                    @error('button_color')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">لون النص</label>
                    <input
                        type="color"
                        name="text_color"
                        value="{{ old('text_color', $profile?->text_color ?? '#111827') }}"
                        class="form-control form-control-color @error('text_color') is-invalid @enderror"
                    >
                    @error('text_color')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="form-check form-switch">
                        <input type="hidden" name="is_published" value="0">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            role="switch"
                            name="is_published"
                            value="1"
                            id="is_published"
                            @checked(old('is_published', $profile?->is_published ?? true))
                        >
                        <label class="form-check-label" for="is_published">
                            نشر الصفحة
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('public.business-page.show', $workspace) }}" target="_blank" class="btn btn-outline-secondary">
                    معاينة
                </a>

                <button type="submit" class="btn btn-primary">
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection