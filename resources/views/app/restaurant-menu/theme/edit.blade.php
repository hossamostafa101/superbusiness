{{-- resources/views/app/restaurant-menu/theme/edit.blade.php --}}
@extends('app.layouts.app')

@section('title', 'تصميم المنيو')
@section('page_title', 'تصميم المنيو')
@section('page_description', 'اختر قالبًا كاملًا أو خصص أقسام المنيو حسب هوية المطعم.')

@section('content')
    @php
        $colors = $assignment->colors ?? [];
        $typography = $assignment->typography ?? [];

        $selectedMode = old('mode', $assignment->mode ?? 'template');

        $selectedTemplateId = old('template_id', $assignment->template_id);

        $selectedHeroSectionId = old('hero_section_id', $assignment->hero_section_id);
        $selectedBranchSwitchSectionId = old('branch_switch_section_id', $assignment->branch_switch_section_id);
        $selectedCategoryTabsSectionId = old('category_tabs_section_id', $assignment->category_tabs_section_id);
        $selectedItemsSectionId = old('items_section_id', $assignment->items_section_id);
        $selectedItemModalSectionId = old('item_modal_section_id', $assignment->item_modal_section_id);
        $selectedCartSectionId = old('cart_section_id', $assignment->cart_section_id);
        $selectedInvoiceSectionId = old('invoice_section_id', $assignment->invoice_section_id);
        $selectedFooterSectionId = old('footer_section_id', $assignment->footer_section_id);

        $themeColor = old('theme_color', $colors['theme_color'] ?? '#111827');
        $buttonColor = old('button_color', $colors['button_color'] ?? '#2563eb');
        $backgroundColor = old('background_color', $colors['background_color'] ?? '#f6f7fb');
        $textColor = old('text_color', $colors['text_color'] ?? '#111827');

        $fontFamily = old('font_family', $typography['font_family'] ?? 'system');
    @endphp

    <div class="row g-4">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('app.restaurant-menu.theme.update', $workspace) }}">
                @csrf
                @method('PUT')

                <div class="card content-card mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">
                            طريقة التصميم
                        </h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="theme-mode-card border rounded-4 p-3 d-block h-100">
                                    <input type="radio" name="mode" value="template" class="theme-mode-input"
                                        @checked($selectedMode === 'template')>

                                    <span class="fw-bold ms-1">
                                        قالب كامل
                                    </span>

                                    <div class="small text-muted mt-2">
                                        اختر تصميمًا جاهزًا يطبق كل أقسام المنيو مرة واحدة.
                                    </div>
                                </label>
                            </div>

                            <div class="col-md-6">
                                <label
                                    class="theme-mode-card border rounded-4 p-3 d-block h-100 {{ !$customSectionsEnabled ? 'opacity-50' : '' }}">
                                    <input type="radio" name="mode" value="custom" class="theme-mode-input"
                                        @checked($selectedMode === 'custom') @disabled(!$customSectionsEnabled)>

                                    <span class="fw-bold ms-1">
                                        تخصيص الأقسام
                                    </span>

                                    <div class="small text-muted mt-2">
                                        اختر شكل كل جزء من المنيو منفصلًا، مثل الهيدر والأصناف والسلة.
                                    </div>

                                    @if (!$customSectionsEnabled)
                                        <div class="badge bg-secondary mt-2">
                                            غير متاح في الباقة
                                        </div>
                                    @endif
                                </label>
                            </div>
                        </div>

                        @error('mode')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card content-card mb-4" id="templateModeBox">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <h5 class="fw-bold mb-1">
                                    القوالب الكاملة
                                </h5>

                                <div class="text-muted small">
                                    اختر قالبًا جاهزًا للمنيو بالكامل.
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            @foreach ($options['templates'] as $template)
                                <div class="col-md-6">
                                    <label class="template-card d-block h-100">
                                        <input type="radio" name="template_id" value="{{ $template->id }}"
                                            @checked((string) $selectedTemplateId === (string) $template->id)>

                                        <div class="template-box border rounded-4 p-3 h-100">
                                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                                <div>
                                                    <div class="fw-bold">
                                                        {{ $template->name }}
                                                    </div>

                                                    <div class="small text-muted">
                                                        {{ $template->description }}
                                                    </div>
                                                </div>



                                                <div class="mt-2">
                                                    <a href="{{ route('public.restaurant-menu.workspace', $workspace) }}?preview_template={{ $template->key }}"
                                                        target="_blank" class="btn btn-sm btn-outline-primary"
                                                        onclick="event.stopPropagation();">
                                                        معاينة
                                                    </a>
                                                </div>



                                                @if ($template->is_premium)
                                                    <span class="badge bg-warning text-dark">
                                                        Premium
                                                    </span>
                                                @else
                                                    <span class="badge bg-light text-dark border">
                                                        Free
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="template-preview rounded-4 border mt-3">
                                                @if ($template->previewImageUrl())
                                                    <img src="{{ $template->previewImageUrl() }}"
                                                        alt="{{ $template->name }}">
                                                @else
                                                    <div class="template-preview-empty">
                                                        <i class="bi bi-layout-text-window-reverse"></i>
                                                        <span>{{ $template->key }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        @error('template_id')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card content-card mb-4" id="customModeBox">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <h5 class="fw-bold mb-1">
                                    تخصيص أقسام المنيو
                                </h5>

                                <div class="text-muted small">
                                    اختر تصميم كل قسم بشكل منفصل.
                                </div>
                            </div>

                            @if (!$customSectionsEnabled)
                                <span class="badge bg-secondary">
                                    غير متاح في الباقة
                                </span>
                            @endif
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                @include('app.restaurant-menu.theme.partials.section-select', [
                                    'label' => 'الهيدر',
                                    'name' => 'hero_section_id',
                                    'sections' => $options['sections']['hero'] ?? collect(),
                                    'selected' => $selectedHeroSectionId,
                                    'disabled' => !$customSectionsEnabled,
                                ])
                            </div>

                            <div class="col-md-6">
                                @include('app.restaurant-menu.theme.partials.section-select', [
                                    'label' => 'اختيار الفروع',
                                    'name' => 'branch_switch_section_id',
                                    'sections' => $options['sections']['branch_switch'] ?? collect(),
                                    'selected' => $selectedBranchSwitchSectionId,
                                    'disabled' => !$customSectionsEnabled,
                                ])
                            </div>

                            <div class="col-md-6">
                                @include('app.restaurant-menu.theme.partials.section-select', [
                                    'label' => 'تصنيفات المنيو',
                                    'name' => 'category_tabs_section_id',
                                    'sections' => $options['sections']['category_tabs'] ?? collect(),
                                    'selected' => $selectedCategoryTabsSectionId,
                                    'disabled' => !$customSectionsEnabled,
                                ])
                            </div>

                            <div class="col-md-6">
                                @include('app.restaurant-menu.theme.partials.section-select', [
                                    'label' => 'عرض الأصناف',
                                    'name' => 'items_section_id',
                                    'sections' => $options['sections']['items'] ?? collect(),
                                    'selected' => $selectedItemsSectionId,
                                    'disabled' => !$customSectionsEnabled,
                                ])
                            </div>

                            <div class="col-md-6">
                                @include('app.restaurant-menu.theme.partials.section-select', [
                                    'label' => 'نافذة الصنف',
                                    'name' => 'item_modal_section_id',
                                    'sections' => $options['sections']['item_modal'] ?? collect(),
                                    'selected' => $selectedItemModalSectionId,
                                    'disabled' => !$customSectionsEnabled,
                                ])
                            </div>

                            <div class="col-md-6">
                                @include('app.restaurant-menu.theme.partials.section-select', [
                                    'label' => 'السلة',
                                    'name' => 'cart_section_id',
                                    'sections' => $options['sections']['cart'] ?? collect(),
                                    'selected' => $selectedCartSectionId,
                                    'disabled' => !$customSectionsEnabled,
                                ])
                            </div>

                            <div class="col-md-6">
                                @include('app.restaurant-menu.theme.partials.section-select', [
                                    'label' => 'الفاتورة / الجلسة',
                                    'name' => 'invoice_section_id',
                                    'sections' => $options['sections']['invoice'] ?? collect(),
                                    'selected' => $selectedInvoiceSectionId,
                                    'disabled' => !$customSectionsEnabled,
                                ])
                            </div>

                            <div class="col-md-6">
                                @include('app.restaurant-menu.theme.partials.section-select', [
                                    'label' => 'الفوتر',
                                    'name' => 'footer_section_id',
                                    'sections' => $options['sections']['footer'] ?? collect(),
                                    'selected' => $selectedFooterSectionId,
                                    'disabled' => !$customSectionsEnabled,
                                ])
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card content-card mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">
                            الألوان والخط
                        </h5>

                        <div class="row g-4">
                            <div class="col-md-3">
                                <label class="form-label">لون الهوية</label>
                                <input type="color" name="theme_color" value="{{ $themeColor }}"
                                    class="form-control form-control-color w-100">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">لون الأزرار</label>
                                <input type="color" name="button_color" value="{{ $buttonColor }}"
                                    class="form-control form-control-color w-100">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">لون الخلفية</label>
                                <input type="color" name="background_color" value="{{ $backgroundColor }}"
                                    class="form-control form-control-color w-100">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">لون النص</label>
                                <input type="color" name="text_color" value="{{ $textColor }}"
                                    class="form-control form-control-color w-100">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الخط</label>
                                <select name="font_family" class="form-select">
                                    <option value="system" @selected($fontFamily === 'system')>System</option>
                                    <option value="cairo" @selected($fontFamily === 'cairo')>Cairo</option>
                                    <option value="tajawal" @selected($fontFamily === 'tajawal')>Tajawal</option>
                                    <option value="ibm-plex-sans-arabic" @selected($fontFamily === 'ibm-plex-sans-arabic')>IBM Plex Arabic
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card content-card mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <h5 class="fw-bold mb-1">
                                    CSS مخصص
                                </h5>

                                <div class="text-muted small">
                                    أضف تعديلات CSS خاصة بالمنيو العام.
                                </div>
                            </div>

                            @if (!$customCssEnabled)
                                <span class="badge bg-secondary">
                                    غير متاح في الباقة
                                </span>
                            @endif
                        </div>

                        <textarea name="custom_css" rows="8"
                            class="form-control font-monospace @error('custom_css') is-invalid @enderror"
                            placeholder=".item-card { border-radius: 20px; }" @disabled(!$customCssEnabled)>{{ old('custom_css', $assignment->custom_css) }}</textarea>

                        @error('custom_css')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        @if (!$customCssEnabled)
                            <div class="form-text text-muted">
                                CSS المخصص متاح في الباقات الأعلى.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mb-4">
                    <a href="{{ route('public.restaurant-menu.workspace', $workspace) }}" target="_blank"
                        class="btn btn-outline-primary">
                        معاينة المنيو
                    </a>

                    <button type="submit" class="btn btn-primary">
                        حفظ التصميم
                    </button>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card content-card sticky-top" style="top: 90px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold mb-1">
                                معاينة مباشرة
                            </h6>

                            <div class="small text-muted">
                                تتحدث تلقائيًا عند تغيير التصميم.
                            </div>
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-secondary" id="refreshPreviewBtn">
                            تحديث
                        </button>
                    </div>

                    <div class="live-preview-frame-wrap">
                        <div class="live-preview-loading" id="livePreviewLoading">
                            جاري تحديث المعاينة...
                        </div>

                        <iframe id="menuLivePreviewFrame" src="{{ $previewUrl }}?theme_preview=1"
                            class="live-preview-frame" loading="lazy"></iframe>
                    </div>

                    <div class="d-grid mt-3">
                        <a href="{{ $previewUrl }}" target="_blank" class="btn btn-outline-primary btn-sm">
                            فتح المنيو في تبويب جديد
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .template-card input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .template-card {
            position: relative;
            cursor: pointer;
        }

        .template-card input:checked+.template-box {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .12);
        }

        .template-box {
            transition: .18s ease;
        }

        .template-preview {
            height: 135px;
            overflow: hidden;
            background: #f6f7fb;
            display: grid;
            place-items: center;
        }

        .template-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .template-preview-empty {
            display: grid;
            place-items: center;
            gap: 8px;
            color: #6b7280;
            font-size: 13px;
        }

        .template-preview-empty i {
            font-size: 32px;
        }

        .preview-phone {
            max-width: 280px;
            margin: 0 auto;
            border: 10px solid #111827;
            border-radius: 30px;
            padding: 12px;
            min-height: 450px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, .18);
        }

        .preview-hero {
            border-radius: 22px;
            color: #fff;
            padding: 18px;
            margin-bottom: 12px;
        }

        .preview-title {
            font-weight: 900;
            font-size: 18px;
        }

        .preview-subtitle {
            font-size: 12px;
            opacity: .8;
        }

        .preview-tabs {
            display: flex;
            gap: 6px;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .preview-tabs span {
            white-space: nowrap;
            border-radius: 999px;
            background: #fff;
            border: 1px solid #e5e7eb;
            padding: 6px 10px;
            font-size: 12px;
        }

        .preview-item {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 12px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 10px;
        }

        .preview-button {
            width: 100%;
            border: 0;
            color: #fff;
            border-radius: 16px;
            min-height: 44px;
            font-weight: 800;
            margin-top: 10px;
        }










        .section-preview {
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            min-height: 92px;
            background: #f8fafc;
            overflow: hidden;
        }

        .section-preview img {
            width: 100%;
            height: 92px;
            object-fit: cover;
            display: block;
        }

        .section-preview-empty {
            height: 92px;
            display: grid;
            place-items: center;
            color: #6b7280;
            font-size: 12px;
            text-align: center;
            padding: 10px;
        }















        .live-preview-frame-wrap {
            position: relative;
            width: 100%;
            height: 680px;
            border: 10px solid #111827;
            border-radius: 34px;
            overflow: hidden;
            background: #f3f4f6;
            box-shadow: 0 18px 45px rgba(15, 23, 42, .18);
        }

        .live-preview-frame {
            width: 100%;
            height: 100%;
            border: 0;
            background: #fff;
        }

        .live-preview-loading {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, .72);
            display: none;
            place-items: center;
            z-index: 2;
            font-size: 13px;
            font-weight: 700;
            color: #111827;
        }

        @media (max-width: 991px) {
            .live-preview-frame-wrap {
                height: 620px;
            }
        }
    </style>

    @push('scripts')
        <script>
            (function() {
                const modeInputs = document.querySelectorAll('.theme-mode-input');
                const templateBox = document.getElementById('templateModeBox');
                const customBox = document.getElementById('customModeBox');

                function syncMode() {
                    const checked = document.querySelector('.theme-mode-input:checked');
                    const mode = checked ? checked.value : 'template';

                    if (templateBox) {
                        templateBox.style.display = mode === 'template' ? 'block' : 'none';
                    }

                    if (customBox) {
                        customBox.style.display = mode === 'custom' ? 'block' : 'none';
                    }
                }

                modeInputs.forEach(function(input) {
                    input.addEventListener('change', syncMode);
                });

                syncMode();

                const sectionSelects = document.querySelectorAll('.section-select');

                function syncSectionPreview(select) {
                    const targetId = select.getAttribute('data-preview-target');
                    const target = document.getElementById(targetId);

                    if (!target) {
                        return;
                    }

                    const selectedOption = select.options[select.selectedIndex];
                    const preview = selectedOption?.getAttribute('data-preview');
                    const description = selectedOption?.getAttribute('data-description');

                    if (preview) {
                        target.innerHTML = `<img src="${preview}" alt="">`;
                        return;
                    }

                    target.innerHTML = `
                <div class="section-preview-empty">
                    ${description || 'لا توجد معاينة متاحة'}
                </div>
            `;
                }

                sectionSelects.forEach(function(select) {
                    select.addEventListener('change', function() {
                        syncSectionPreview(select);
                    });

                    syncSectionPreview(select);
                });
















                const previewBaseUrl = @json($previewUrl);
const previewFrame = document.getElementById('menuLivePreviewFrame');
const previewLoading = document.getElementById('livePreviewLoading');
const refreshPreviewBtn = document.getElementById('refreshPreviewBtn');

let previewTimer = null;

function getCheckedMode() {
    const checked = document.querySelector('.theme-mode-input:checked');
    return checked ? checked.value : 'template';
}

function selectedValue(name) {
    const field = document.querySelector(`[name="${name}"]`);

    if (!field) {
        return '';
    }

    if (field.type === 'radio') {
        const checked = document.querySelector(`[name="${name}"]:checked`);
        return checked ? checked.value : '';
    }

    return field.value || '';
}

function buildPreviewUrl() {
    const params = new URLSearchParams();

    params.set('theme_preview', '1');
    params.set('preview_mode', getCheckedMode());

    params.set('preview_template_id', selectedValue('template_id'));

    params.set('preview_hero_section_id', selectedValue('hero_section_id'));
    params.set('preview_branch_switch_section_id', selectedValue('branch_switch_section_id'));
    params.set('preview_category_tabs_section_id', selectedValue('category_tabs_section_id'));
    params.set('preview_items_section_id', selectedValue('items_section_id'));
    params.set('preview_item_modal_section_id', selectedValue('item_modal_section_id'));
    params.set('preview_cart_section_id', selectedValue('cart_section_id'));
    params.set('preview_invoice_section_id', selectedValue('invoice_section_id'));
    params.set('preview_footer_section_id', selectedValue('footer_section_id'));

    params.set('preview_theme_color', selectedValue('theme_color'));
    params.set('preview_button_color', selectedValue('button_color'));
    params.set('preview_background_color', selectedValue('background_color'));
    params.set('preview_text_color', selectedValue('text_color'));
    params.set('preview_font_family', selectedValue('font_family'));

    params.set('_t', Date.now().toString());

    return previewBaseUrl + '?' + params.toString();
}

function updateLivePreview() {
    if (!previewFrame) {
        return;
    }

    if (previewLoading) {
        previewLoading.style.display = 'grid';
    }

    previewFrame.src = buildPreviewUrl();
}

function scheduleLivePreviewUpdate() {
    clearTimeout(previewTimer);

    previewTimer = setTimeout(function () {
        updateLivePreview();
    }, 450);
}

const livePreviewFields = document.querySelectorAll(`
    .theme-mode-input,
    [name="template_id"],
    [name="hero_section_id"],
    [name="branch_switch_section_id"],
    [name="category_tabs_section_id"],
    [name="items_section_id"],
    [name="item_modal_section_id"],
    [name="cart_section_id"],
    [name="invoice_section_id"],
    [name="footer_section_id"],
    [name="theme_color"],
    [name="button_color"],
    [name="background_color"],
    [name="text_color"],
    [name="font_family"]
`);

livePreviewFields.forEach(function (field) {
    field.addEventListener('change', scheduleLivePreviewUpdate);
    field.addEventListener('input', scheduleLivePreviewUpdate);
});

previewFrame?.addEventListener('load', function () {
    if (previewLoading) {
        previewLoading.style.display = 'none';
    }
});

refreshPreviewBtn?.addEventListener('click', function () {
    updateLivePreview();
});

setTimeout(function () {
    updateLivePreview();
}, 300);
            })();
        </script>
    @endpush
@endsection
