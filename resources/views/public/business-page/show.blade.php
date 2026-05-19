{{-- resources/views/public/business-page/show.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $profile->display_name }}</title>

    <meta name="description" content="{{ $profile->tagline ?: \Illuminate\Support\Str::limit(strip_tags($profile->description), 150) }}">

    <meta property="og:title" content="{{ $profile->display_name }}">
    <meta property="og:description" content="{{ $profile->tagline ?: \Illuminate\Support\Str::limit(strip_tags($profile->description), 150) }}">

    @if($profile->cover_image)
        <meta property="og:image" content="{{ asset('storage/' . $profile->cover_image) }}">
    @elseif($profile->logo)
        <meta property="og:image" content="{{ asset('storage/' . $profile->logo) }}">
    @endif

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --theme-color: {{ $profile->theme_color ?: '#111827' }};
            --button-color: {{ $profile->button_color ?: '#2563eb' }};
            --text-color: {{ $profile->text_color ?: '#111827' }};
        }

        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.14), transparent 32%),
                linear-gradient(180deg, var(--theme-color), #f6f7fb 52%);
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text-color);
        }

        .page-wrapper {
            max-width: 620px;
            margin: 0 auto;
            padding: 24px 14px 40px;
        }

        .hero-card {
            background: #fff;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 18px 50px rgba(0,0,0,.12);
            border: 1px solid rgba(255,255,255,.7);
        }

        .cover {
            height: 180px;
            background: linear-gradient(135deg, var(--theme-color), var(--button-color));
            position: relative;
        }

        .cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .logo-wrap {
            width: 110px;
            height: 110px;
            border-radius: 28px;
            background: #fff;
            padding: 5px;
            box-shadow: 0 10px 30px rgba(0,0,0,.16);
            margin: -55px auto 16px;
            position: relative;
            z-index: 3;
        }

        .logo-wrap img {
            width: 100%;
            height: 100%;
            border-radius: 24px;
            object-fit: cover;
        }

        .logo-placeholder {
            width: 100%;
            height: 100%;
            border-radius: 24px;
            background: var(--button-color);
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 36px;
            font-weight: 800;
        }

        .business-name {
            font-weight: 800;
            font-size: 28px;
            margin-bottom: 6px;
        }

        .tagline {
            color: #6b7280;
            font-size: 15px;
        }

        .main-button {
            background: var(--button-color);
            color: #fff;
            border: none;
            border-radius: 16px;
            padding: 13px 18px;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: .2s ease;
        }

        .main-button:hover {
            color: #fff;
            transform: translateY(-2px);
            filter: brightness(.96);
        }

        .secondary-button {
            background: #f3f4f6;
            color: #111827;
            border-radius: 16px;
            padding: 13px 18px;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: .2s ease;
        }

        .secondary-button:hover {
            background: #e5e7eb;
            color: #111827;
        }

        .section-card {
            background: #fff;
            border-radius: 24px;
            padding: 20px;
            margin-top: 16px;
            box-shadow: 0 10px 28px rgba(0,0,0,.07);
            border: 1px solid #eef0f3;
        }

        .description {
            white-space: pre-line;
            line-height: 1.8;
            color: #374151;
        }

        .link-item {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #111827;
            background: #f9fafb;
            border: 1px solid #eef0f3;
            border-radius: 18px;
            padding: 14px 16px;
            margin-bottom: 10px;
            transition: .2s ease;
        }

        .link-item:hover {
            color: #111827;
            transform: translateY(-2px);
            background: #f3f4f6;
        }

        .link-icon {
            width: 38px;
            height: 38px;
            border-radius: 14px;
            background: var(--button-color);
            color: #fff;
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }

        .product-card {
            border: 1px solid #eef0f3;
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 14px;
            box-shadow: 0 8px 22px rgba(0,0,0,.05);
        }

        .product-image {
            width: 100%;
            height: 190px;
            background: #f3f4f6;
            object-fit: cover;
        }

        .product-placeholder {
            height: 190px;
            background: #f3f4f6;
            display: grid;
            place-items: center;
            color: #9ca3af;
            font-size: 32px;
        }

        .product-title {
            font-size: 17px;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .product-description {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.7;
            white-space: pre-line;
        }

        .price {
            font-weight: 800;
            font-size: 16px;
            color: #111827;
        }

        .old-price {
            color: #9ca3af;
            text-decoration: line-through;
            font-size: 13px;
        }

        .category-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 800;
            margin-bottom: 14px;
            margin-top: 4px;
        }

        .footer-brand {
            text-align: center;
            font-size: 13px;
            color: rgba(255,255,255,.75);
            margin-top: 24px;
        }

        .footer-brand a {
            color: #fff;
            text-decoration: none;
            font-weight: 700;
        }
    </style>
</head>
<body>

@php
    $links = $workspace->businessLinks;
    $categories = $workspace->businessCategories;
    $uncategorizedProducts = $workspace->businessProducts;

    $whatsappNumber = $profile->whatsapp_number
        ? preg_replace('/\D+/', '', $profile->whatsapp_number)
        : null;

    $whatsappMessage = urlencode('مرحبًا، وصلت لصفحتكم وأريد الاستفسار.');
    $whatsappUrl = $whatsappNumber
        ? "https://wa.me/{$whatsappNumber}?text={$whatsappMessage}"
        : null;

    function business_link_icon(?string $icon): string {
        return match ($icon) {
            'instagram' => 'bi-instagram',
            'facebook' => 'bi-facebook',
            'tiktok' => 'bi-tiktok',
            'youtube' => 'bi-youtube',
            'whatsapp' => 'bi-whatsapp',
            'location' => 'bi-geo-alt',
            'store' => 'bi-bag',
            'website' => 'bi-globe',
            default => 'bi-link-45deg',
        };
    }

    function product_whatsapp_url(?string $number, $product): ?string {
        if (! $number) {
            return null;
        }

        $price = $product->sale_price ?: $product->price;

        $message = "مرحبًا، أريد الاستفسار عن المنتج: {$product->name}";

        if ($price) {
            $message .= " - السعر: {$price} {$product->currency}";
        }

        return "https://wa.me/{$number}?text=" . urlencode($message);
    }
@endphp

<div class="page-wrapper">
    <div class="hero-card">
        <div class="cover">
            @if($profile->cover_image)
                <img src="{{ asset('storage/' . $profile->cover_image) }}" alt="{{ $profile->display_name }}">
            @endif
        </div>

        <div class="px-4 pb-4 text-center">
            <div class="logo-wrap">
                @if($profile->logo)
                    <img src="{{ asset('storage/' . $profile->logo) }}" alt="{{ $profile->display_name }}">
                @else
                    <div class="logo-placeholder">
                        {{ mb_substr($profile->display_name, 0, 1) }}
                    </div>
                @endif
            </div>

            <h1 class="business-name">{{ $profile->display_name }}</h1>

            @if($profile->tagline)
                <p class="tagline mb-3">{{ $profile->tagline }}</p>
            @endif

            <div class="d-grid gap-2 mt-4">
                @if($whatsappUrl)
                    {{-- <a href="{{ $whatsappUrl }}" target="_blank" class="main-button"> --}}
                    <a href="{{ route('public.business-page.track.whatsapp', $workspace) }}" target="_blank" class="main-button">
                        <i class="bi bi-whatsapp"></i>
                        تواصل عبر واتساب
                    </a>
                @endif

                <a href="{{ route('public.booking.create', $workspace) }}" class="main-button">
    <i class="bi bi-calendar-check"></i>
    احجز موعد
</a>

                <div class="row g-2">
                    @if($profile->phone)
                        <div class="col">
                            <a href="tel:{{ $profile->phone }}" class="secondary-button">
                                <i class="bi bi-telephone"></i>
                                اتصال
                            </a>
                        </div>
                    @endif

                    @if($profile->location_url)
                        <div class="col">
                            <a href="{{ $profile->location_url }}" target="_blank" class="secondary-button">
                                <i class="bi bi-geo-alt"></i>
                                الموقع
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($profile->description)
        <div class="section-card">
            <h2 class="h6 fw-bold mb-3">عن البزنس</h2>
            <div class="description">{{ $profile->description }}</div>
        </div>
    @endif

    @if($links->count())
        <div class="section-card">
            <h2 class="h6 fw-bold mb-3">روابط مهمة</h2>

            @foreach($links as $link)
                {{-- <a href="{{ $link->url }}" target="_blank" class="link-item"> --}}
                    <a href="{{ route('public.business-page.track.link', [$workspace, $link]) }}" target="_blank" class="link-item">
                    <span class="link-icon">
                        <i class="bi {{ business_link_icon($link->icon) }}"></i>
                    </span>

                    <span class="fw-semibold">
                        {{ $link->title }}
                    </span>

                    <i class="bi bi-arrow-up-left ms-auto text-muted"></i>
                </a>
            @endforeach
        </div>
    @endif

    @if($categories->where(fn($category) => $category->products->count() > 0)->count() || $uncategorizedProducts->count())
        <div class="section-card">
            <h2 class="h5 fw-bold mb-3">المنتجات والخدمات</h2>

            @foreach($categories as $category)
                @if($category->products->count())
                    <div class="category-title">
                        <i class="bi bi-tag"></i>
                        {{ $category->name }}
                    </div>

                    @foreach($category->products as $product)
                        @include('public.business-page.partials.product-card', [
    'workspace' => $workspace,
    'product' => $product,
    'whatsappNumber' => $whatsappNumber,
])
                    @endforeach
                @endif
            @endforeach

            @if($uncategorizedProducts->count())
                <div class="category-title">
                    <i class="bi bi-grid"></i>
                    منتجات أخرى
                </div>

                @foreach($uncategorizedProducts as $product)
                   @include('public.business-page.partials.product-card', [
    'workspace' => $workspace,
    'product' => $product,
    'whatsappNumber' => $whatsappNumber,
])
                @endforeach
            @endif
        </div>
    @endif

    @if($profile->address || $profile->email)
        <div class="section-card">
            <h2 class="h6 fw-bold mb-3">بيانات التواصل</h2>

            @if($profile->address)
                <div class="mb-2">
                    <i class="bi bi-geo-alt text-muted"></i>
                    {{ $profile->address }}
                </div>
            @endif

            @if($profile->email)
                <div>
                    <i class="bi bi-envelope text-muted"></i>
                    <a href="mailto:{{ $profile->email }}" class="text-decoration-none">
                        {{ $profile->email }}
                    </a>
                </div>
            @endif
        </div>
    @endif

    <div class="footer-brand">
        Powered by
        <a href="{{ url('/') }}">Smart Business</a>
    </div>
</div>

</body>
</html>