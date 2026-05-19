@extends('restaurant.layouts.app')

@section('title', 'تصميم المنيو')

@section('content')
<div class="container py-4">

    <div class="mb-3">
        <h1 class="h4 mb-1">تصميم المنيو</h1>
        <p class="text-muted small mb-0">
            المطعم:
            <strong>{{ $restaurant->name }}</strong>
            @if($branch)
                — الفرع الحالي:
                <strong>{{ $branch->name }}</strong>
            @endif
        </p>
    </div>

    @php
        $sectionLabels = [
            'header'     => 'الهيدر',
            'categories' => 'التصنيفات',
            'items'      => 'الأصناف',
            'cart_bar'   => 'شريط السلة',
        ];
    @endphp

    <div class="row g-3">
        {{-- العمود الأيسر: اختيارات التمبلتس --}}
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <strong>إعدادات التصميم</strong>
                    <div class="small text-muted">اختر شكل كل جزء من المنيو</div>
                </div>
                <div class="card-body" style="max-height: 70vh; overflow-y:auto;">

                    <form method="POST" action="{{ route('restaurant.menu_style.update') }}" id="menuStyleForm">
                        @csrf

                        @foreach($templatesConfig as $sectionKey => $templates)
                            <div class="mb-4">
                                <h6 class="mb-2">
                                    {{ $sectionLabels[$sectionKey] ?? $sectionKey }}
                                </h6>

                                <div class="d-grid gap-2">
                                    @foreach($templates as $templateKey => $meta)
                                        @php
                                            $checked = ($currentStyles[$sectionKey] ?? null) === $templateKey;
                                        @endphp

                                        <label class="card border @if($checked) border-primary @else border-light @endif template-choice"
                                               data-section="{{ $sectionKey }}"
                                               data-template="{{ $templateKey }}"
                                               style="cursor:pointer;">
                                            <div class="card-body py-2 px-3">
                                                <div class="d-flex justify-content-between align-items-center gap-2">
                                                    <div>
                                                        <div class="fw-bold">
                                                            {{ $meta['label'] ?? $templateKey }}
                                                        </div>
                                                        @if(!empty($meta['description']))
                                                            <div class="small text-muted">
                                                                {{ $meta['description'] }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <input type="radio"
                                                               class="form-check-input"
                                                               name="sections[{{ $sectionKey }}][template_key]"
                                                               value="{{ $templateKey }}"
                                                               @checked($checked)>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <button type="submit" class="btn btn-primary w-100">
                            حفظ التصميم
                        </button>
                    </form>

                </div>
            </div>
        </div>

        {{-- العمود الأيمن: معاينة المنيو --}}
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <strong>معاينة المنيو</strong>
                        <div class="small text-muted">
                            المعاينة بدون وظائف (واتساب / إضافة للسلة)
                        </div>
                    </div>
                    <button type="button"
                            class="btn btn-sm btn-outline-secondary"
                            id="refreshPreviewBtn">
                        إعادة تحميل المعاينة
                    </button>
                </div>
                <div class="card-body p-0" style="background:#0f172a;">
                    @if($branch)
                        <iframe
                            id="menuPreviewFrame"
                            src="{{ route('public.menu', [
                                'restaurant' => $restaurant->slug,
                                'branch'     => $branch->id,
                                'preview'    => 1,
                            ]) }}"
                            style="width:100%;height:100%;border:0;"
                            loading="lazy">
                        </iframe>
                    @else
                        <div class="p-4 text-center text-muted">
                            لا يوجد فرع متاح لعرض المعاينة.
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // تظبيط البوردر لما يختار تمبلت
    document.querySelectorAll('.template-choice').forEach(function (card) {
        card.addEventListener('click', function (e) {
            const section = card.dataset.section;
            const radio   = card.querySelector('input[type="radio"]');

            if (radio) {
                radio.checked = true;
            }

            // شيل الـ border-primary من باقي تمبلتات السكشن
            document.querySelectorAll('.template-choice[data-section="'+section+'"]')
                .forEach(function (c) {
                    c.classList.remove('border-primary');
                    c.classList.add('border-light');
                });

            card.classList.remove('border-light');
            card.classList.add('border-primary');
        });
    });

    // زر إعادة تحميل المعاينة
    const frame = document.getElementById('menuPreviewFrame');
    const refreshBtn = document.getElementById('refreshPreviewBtn');
    if (frame && refreshBtn) {
        refreshBtn.addEventListener('click', function () {
            const url = new URL(frame.src, window.location.origin);
            // نحط باراميتر عشوائي علشان ما يكاشّش
            url.searchParams.set('t', Date.now().toString());
            frame.src = url.toString();
        });
    }
});
</script>
@endpush
