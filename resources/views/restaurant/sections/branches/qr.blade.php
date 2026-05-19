@extends('restaurant.layouts.app')

@section('title', 'QR Code - ' . $branch->name)

@section('content')
<div class="container py-4">

    <div class="mb-3">
        <h1 class="h4 mb-1">كود QR للفرع</h1>
        <p class="text-muted small mb-0">
            المطعم: <strong>{{ $restaurant->name }}</strong> —
            الفرع: <strong>{{ $branch->name }}</strong>
        </p>
    </div>

    <div class="row g-4">
        {{-- معلومات الرابط --}}
        <div class="col-12 col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h6 mb-3">رابط المنيو</h2>

                    <div class="mb-3">
                        <label class="form-label small text-muted mb-1">الرابط الذي سيتم تشفيره في QR:</label>
                        <div class="input-group">
                            <input type="text"
                                   class="form-control"
                                   readonly
                                   value="{{ $menuUrl }}">
                            <button type="button"
                                    class="btn btn-outline-secondary"
                                    onclick="navigator.clipboard.writeText('{{ $menuUrl }}');">
                                نسخ
                            </button>
                        </div>
                    </div>

                    <p class="small text-muted mb-0">
                        يمكن طباعة كود الـ QR وتعليقه على الطاولة؛ عند مسحه سيتم فتح هذا الرابط على موبايل العميل.
                    </p>
                </div>
            </div>
        </div>

        {{-- معاينة الـ QR --}}
        <div class="col-12 col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">

                    <h2 class="h6 mb-3">معاينة كود الـ QR</h2>

                    @php
                        // تحويل لون HEX لصيغة مناسبة لـ API (بدون #)
                        $qrColorHex   = ltrim($qrColor, '#');
                        $qrBgColorHex = ltrim($qrBgColor, '#');

                        // هنا بنستخدم API مجاني لتوليد صورة QR مباشرة في الـ <img>
                        // بدون أي مكتبات PHP أو JS.
                        $qrSrc = 'https://api.qrserver.com/v1/create-qr-code/?'
                            . http_build_query([
                                'data'    => $menuUrl,
                                'size'    => '600x600',
                                'color'   => $qrColorHex,
                                'bgcolor' => $qrBgColorHex,
                            ]);
                    @endphp

                    <div class="mb-3">
                        <img src="{{ $qrSrc }}"
                             alt="QR Code"
                             class="img-fluid"
                             style="max-width: 260px; height: auto; border-radius: 12px; background:#fff;">
                    </div>

                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <a href="{{ $qrSrc }}"
                           download="qr-{{ $restaurant->slug }}-branch-{{ $branch->id }}.png"
                           class="btn btn-primary">
                            <i class="bi bi-download ms-1"></i> تحميل الصورة
                        </a>

                        <a href="{{ $qrSrc }}"
                           target="_blank"
                           class="btn btn-outline-secondary">
                            فتح في تبويب جديد
                        </a>
                    </div>

                    <p class="small text-muted mt-3 mb-0 text-center">
                        يمكنك استخدام هذه الصورة في برامج التصميم (Canva / Photoshop)
                        لإضافة لوجو المطعم، إطار، نص، أو أي ديزاين تريده حول الكود نفسه.
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
