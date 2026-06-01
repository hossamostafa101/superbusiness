{{-- resources/views/public/restaurant-menu/show.blade.php --}}
@php
    $profile = $workspace->businessProfile ?? null;

    // $themeColor = $profile?->theme_color ?: '#111827';
    // $buttonColor = $profile?->button_color ?: '#2563eb';

    $themeColors = $menuTheme['colors'] ?? [];

    $themeColor = $themeColors['theme_color'] ?? ($profile?->theme_color ?: '#111827');
    $buttonColor = $themeColors['button_color'] ?? ($profile?->button_color ?: '#2563eb');
    $backgroundColor = $themeColors['background_color'] ?? '#f6f7fb';
    $textColor = $themeColors['text_color'] ?? '#111827';

    $fontFamily = $menuTheme['typography']['font_family'] ?? 'system';

    $itemsPayload = [];

    foreach ($branch->categories as $category) {
        foreach ($category->items as $item) {
            $itemsPayload[$item->id] = [
                'id' => $item->id,
                'name' => $translate($item, 'name', $item->name),
                'description' => $translate($item, 'description', $item->description),
                'image' => $item->image ? asset('storage/' . $item->image) : null,
                'price' => (float) $item->price,
                'sale_price' => $item->sale_price !== null ? (float) $item->sale_price : null,
                'currency' => $item->currency,
                'category_name' => $translate($category, 'name', $category->name),
                'preparation_time_minutes' => $item->preparation_time_minutes,
                'variants' => $item->activeVariants
                    ->map(
                        fn($variant) => [
                            'id' => $variant->id,
                            'name' => $variant->name,
                            'price' => (float) $variant->price,
                            'sale_price' => $variant->sale_price !== null ? (float) $variant->sale_price : null,
                            'currency' => $variant->currency,
                            'is_default' => (bool) $variant->is_default,
                        ],
                    )
                    ->values(),
                'option_groups' => $item->activeOptionGroups
                    ->map(
                        fn($group) => [
                            'id' => $group->id,
                            'name' => $group->name,
                            'type' => $group->type,
                            'is_required' => (bool) $group->is_required,
                            'min_choices' => (int) $group->min_choices,
                            'max_choices' => $group->max_choices ? (int) $group->max_choices : null,
                            'options' => $group->options
                                ->map(
                                    fn($option) => [
                                        'id' => $option->id,
                                        'name' => $option->name,
                                        'price' => (float) $option->price,
                                        'currency' => $option->currency,
                                    ],
                                )
                                ->values(),
                        ],
                    )
                    ->values(),
            ];
        }
    }

    $offersPayload = [];

    if (!empty($contentSections)) {
        foreach ($contentSections as $contentSection) {
            foreach ($contentSection->activeOffers ?? collect() as $offer) {
                if (!$offer->is_orderable) {
                    continue;
                }

                $imageUrl = $offer->imageUrl();

                if (!$imageUrl && $offer->item?->image) {
                    $imageUrl = asset('storage/' . $offer->item->image);
                }

                $offersPayload[$offer->id] = [
                    'id' => $offer->id,
                    'line_type' => 'offer',
                    'offer_id' => $offer->id,
                    'title' => $translate($offer, 'title', $offer->title),
                    'description' => $translate($offer, 'description', $offer->description),
                    'image' => $imageUrl,
                    'price' => (float) ($offer->new_price ?: $offer->old_price ?: 0),
                    'old_price' => $offer->old_price ? (float) $offer->old_price : null,
                    'currency' => $offer->currency ?: 'EGP',
                    'order_mode' => $offer->order_mode ?: 'standalone',
                    'item_id' => $offer->item_id,
                ];
            }
        }
    }
@endphp

<!DOCTYPE html>
{{-- <html lang="ar" dir="rtl"> --}}
<html lang="{{ $currentLanguage?->code ?? 'ar' }}" dir="{{ $currentLanguage?->direction ?? 'rtl' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <title>منيو {{ $workspace->name }} - {{ $branch->name }}</title> --}}
    <title>
        منيو {{ $translate($workspace->businessProfile, 'display_name', $workspace->name) }}
        -
        {{ $translate($branch, 'name', $branch->name) }}
    </title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">






        <link rel="manifest" href="{{ route('public.restaurant-menu.pwa.manifest', [$workspace, $branch]) }}">

<meta name="theme-color" content="#111827">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="{{ $workspace->businessProfile?->display_name ?: $workspace->name }}">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

@if($workspace->businessProfile?->logo)
    @php
        $appleIcon = str_starts_with($workspace->businessProfile->logo, 'http')
            ? $workspace->businessProfile->logo
            : asset('storage/' . $workspace->businessProfile->logo);
    @endphp

    <link rel="apple-touch-icon" href="{{ $appleIcon }}">
@endif










    <style>
        /* :root {
            --theme-color: {{ $themeColor }};
            --button-color: {{ $buttonColor }};
            --soft-bg: #f6f7fb;
            --border: #e5e7eb;
            --text: #111827;
            --muted: #6b7280;
        } */

        :root {
            --theme-color: {{ $themeColor }};
            --button-color: {{ $buttonColor }};
            --soft-bg: {{ $backgroundColor }};
            --border: #e5e7eb;
            --text: {{ $textColor }};
            --muted: #6b7280;

            --font-ar: "Cairo", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            --font-en: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        body {
            background: var(--soft-bg);
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text);
        }

        .menu-wrapper {
            max-width: 820px;
            margin: 0 auto;
            padding: 14px 12px 90px;
        }

        .hero {
            background:
                radial-gradient(circle at top left, rgba(255, 255, 255, .20), transparent 34%),
                linear-gradient(135deg, var(--theme-color), var(--button-color));
            color: #fff;
            border-radius: 28px;
            padding: 24px;
            margin-bottom: 14px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, .16);
        }

        .branch-switch {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 12px;
            margin-bottom: 14px;
            overflow-x: auto;
            display: flex;
            gap: 8px;
        }

        .branch-pill {
            white-space: nowrap;
            border-radius: 999px;
            border: 1px solid var(--border);
            padding: 8px 13px;
            color: var(--text);
            text-decoration: none;
            background: #fff;
            font-size: 14px;
        }

        .branch-pill.active {
            color: #fff;
            background: var(--button-color);
            border-color: var(--button-color);
        }

        .category-tabs {
            position: sticky;
            top: 0;
            z-index: 10;
            background: rgba(246, 247, 251, .92);
            backdrop-filter: blur(12px);
            padding: 10px 0;
            overflow-x: auto;
            display: flex;
            gap: 8px;
            margin-bottom: 12px;
        }

        .category-tab {
            white-space: nowrap;
            border-radius: 999px;
            background: #fff;
            color: var(--text);
            border: 1px solid var(--border);
            padding: 8px 13px;
            text-decoration: none;
            font-size: 14px;
        }

        .category-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 18px;
            margin-bottom: 16px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .04);
        }

        .item-card {
            border-bottom: 1px solid #edf0f3;
            padding: 14px 0;
            cursor: pointer;
        }

        .item-card:last-child {
            border-bottom: 0;
        }

        .item-image {
            width: 92px;
            height: 92px;
            border-radius: 18px;
            object-fit: cover;
            background: #f3f4f6;
            flex-shrink: 0;
        }

        .item-title {
            font-weight: 800;
            line-height: 1.35;
        }

        .item-desc {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
        }

        .price {
            font-weight: 900;
            white-space: nowrap;
        }

        .old-price {
            color: var(--muted);
            text-decoration: line-through;
            font-size: 12px;
        }

        .tag {
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 999px;
            background: #eff6ff;
            color: #2563eb;
        }

        .modal-content {
            border: 0;
            border-radius: 26px;
            overflow: hidden;
        }

        .modal-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: #f3f4f6;
        }

        .item-bottom-sheet-modal .modal-image {
            height: 190px;
        }

        .item-luxury-modal .modal-image {
            height: 240px;
        }

        .option-box {
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 12px;
            margin-bottom: 12px;
        }

        .choice-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            padding: 9px 0;
            border-bottom: 1px solid #f1f3f5;
        }

        .choice-row:last-child {
            border-bottom: 0;
        }

        .btn-main {
            background: var(--button-color);
            color: #fff;
            border: 0;
            border-radius: 14px;
            min-height: 46px;
            font-weight: 800;
        }

        .btn-main:hover {
            color: #fff;
            filter: brightness(.97);
        }

        @media (max-width: 575px) {
            .hero {
                padding: 20px;
            }

            .item-image {
                width: 82px;
                height: 82px;
            }

            .category-card {
                padding: 15px;
            }
        }








        .cart-float {
            position: fixed;
            right: 12px;
            left: 12px;
            bottom: 14px;
            z-index: 1040;
            max-width: 820px;
            margin: 0 auto;
        }

        .cart-button {
            width: 100%;
            min-height: 54px;
            border: 0;
            border-radius: 18px;
            background: var(--button-color);
            color: #fff;
            box-shadow: 0 18px 40px rgba(15, 23, 42, .22);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 18px;
            font-weight: 900;
        }










        /* Theme sections */
        .hero-modern {
            position: relative;
            overflow: hidden;
            min-height: 190px;
        }

        .hero-modern-bg {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 15% 20%, rgba(255, 255, 255, .28), transparent 28%),
                radial-gradient(circle at 90% 10%, rgba(255, 255, 255, .18), transparent 22%);
        }

        .hero-eyebrow {
            display: inline-flex;
            border: 1px solid rgba(255, 255, 255, .35);
            border-radius: 999px;
            padding: 5px 11px;
            font-size: 12px;
            color: rgba(255, 255, 255, .9);
        }

        .hero-modern-title {
            font-size: 34px;
            font-weight: 950;
            letter-spacing: -1px;
        }

        .hero-modern-branch {
            font-size: 15px;
            opacity: .85;
        }

        .hero-whatsapp {
            width: 46px;
            height: 46px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            background: rgba(255, 255, 255, .95);
            color: #16a34a;
            text-decoration: none;
            font-size: 22px;
        }

        .hero-luxury {
            background:
                linear-gradient(135deg, #111827, #27272a);
            border: 1px solid rgba(255, 255, 255, .12);
        }

        .luxury-label {
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 11px;
            opacity: .75;
        }

        .luxury-title {
            font-size: 34px;
            font-weight: 950;
        }

        .luxury-line {
            width: 70px;
            height: 2px;
            background: rgba(255, 255, 255, .6);
        }

        .branch-cards {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 14px;
        }

        .branch-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 14px;
            display: flex;
            gap: 12px;
            align-items: center;
            color: var(--text);
            text-decoration: none;
        }

        .branch-card.active {
            border-color: var(--button-color);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .10);
        }

        .branch-card-icon {
            width: 38px;
            height: 38px;
            border-radius: 14px;
            background: #eff6ff;
            color: var(--button-color);
            display: grid;
            place-items: center;
        }

        .branch-minimal {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 12px;
            margin-bottom: 14px;
        }

        .category-tabs-underline {
            gap: 18px;
        }

        .category-tab-underline {
            white-space: nowrap;
            color: var(--text);
            text-decoration: none;
            font-weight: 800;
            padding: 8px 0;
            border-bottom: 2px solid transparent;
        }

        .category-tab-underline:hover {
            border-bottom-color: var(--button-color);
        }

        .items-grid-large {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .item-card-large {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 22px;
            overflow: hidden;
            cursor: pointer;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .04);
        }

        .item-card-large-image-wrap {
            position: relative;
        }

        .item-card-large-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            background: #f3f4f6;
        }

        .item-card-large-placeholder {
            display: grid;
            place-items: center;
            color: #9ca3af;
            font-size: 28px;
        }

        .item-card-large-featured {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--button-color);
            color: #fff;
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 800;
        }

        .category-elegant {
            background: #fffdf8;
        }

        .elegant-item {
            padding: 16px 0;
            border-bottom: 1px solid #ede7dc;
            cursor: pointer;
        }

        .elegant-item:last-child {
            border-bottom: 0;
        }

        .elegant-item-main {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 12px;
            align-items: end;
        }

        .elegant-item-title {
            font-weight: 900;
            font-size: 16px;
        }

        .elegant-item-desc {
            color: var(--muted);
            font-size: 13px;
            margin-top: 4px;
        }

        .elegant-dots {
            border-bottom: 1px dashed #d6cbbb;
            transform: translateY(-7px);
        }

        .elegant-price {
            font-weight: 900;
            white-space: nowrap;
        }

        @media (max-width: 575px) {
            .branch-cards {
                grid-template-columns: 1fr;
            }

            .items-grid-large {
                grid-template-columns: 1fr;
            }

            .hero-modern-title,
            .luxury-title {
                font-size: 28px;
            }
        }












        /* Item modal themes */
        .modal-dialog-bottom {
            position: fixed;
            right: 0;
            left: 0;
            bottom: 0;
            margin: 0;
            max-width: 100%;
        }

        .modal-dialog-bottom .modal-content {
            border-radius: 28px 28px 0 0;
            max-height: 92vh;
        }

        .bottom-sheet-handle {
            width: 52px;
            height: 5px;
            border-radius: 999px;
            background: #d1d5db;
            margin: 12px auto 8px;
        }

        .bottom-sheet-price {
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 14px;
            background: #fff;
        }

        .luxury-modal-content {
            background: #fffdf8;
            border: 1px solid #e7dcc9;
        }

        .luxury-close {
            position: absolute;
            top: 16px;
            left: 16px;
        }

        .luxury-price-card {
            text-align: center;
            border: 1px solid #e7dcc9;
            border-radius: 22px;
            padding: 15px;
            background: #fff;
        }

        /* Cart themes */
        .cart-bottom-bar {
            bottom: 0;
            right: 0;
            left: 0;
            max-width: 100%;
        }

        .cart-button-bottom-bar {
            border-radius: 22px 22px 0 0;
            min-height: 64px;
            max-width: 820px;
            margin: 0 auto;
        }

        .cart-bottom-icon {
            width: 42px;
            height: 42px;
            border-radius: 16px;
            background: rgba(255, 255, 255, .18);
            display: grid;
            place-items: center;
            font-size: 20px;
        }






























        .menu-content-section {
            border-radius: 26px;
            padding: 18px;
            margin-bottom: 18px;
            border: 1px solid rgba(229, 231, 235, .65);
            overflow: hidden;
        }

        .featured-scroll {
            display: flex;
            gap: 14px;
            overflow-x: auto;
            padding-bottom: 6px;
            scroll-snap-type: x mandatory;
        }

        .featured-scroll::-webkit-scrollbar,
        .offers-slider::-webkit-scrollbar {
            height: 6px;
        }

        .featured-item-card {
            min-width: 210px;
            max-width: 210px;
            background: rgba(255, 255, 255, .92);
            color: #111827;
            border-radius: 22px;
            overflow: hidden;
            cursor: pointer;
            scroll-snap-align: start;
            border: 1px solid rgba(229, 231, 235, .75);
        }

        .featured-item-image {
            width: 100%;
            height: 125px;
            object-fit: cover;
            background: #f3f4f6;
        }

        .featured-item-placeholder {
            display: grid;
            place-items: center;
            color: #9ca3af;
        }

        .collection-grid {
            display: grid;
            gap: 10px;
        }

        .collection-item {
            background: rgba(255, 255, 255, .92);
            color: #111827;
            border: 1px solid rgba(229, 231, 235, .75);
            border-radius: 18px;
            padding: 14px;
            display: flex;
            justify-content: space-between;
            gap: 14px;
            cursor: pointer;
        }

        .offers-slider {
            display: flex;
            gap: 14px;
            overflow-x: auto;
            padding-bottom: 6px;
            scroll-snap-type: x mandatory;
        }

        .offer-slide {
            position: relative;
            min-width: 82%;
            border-radius: 24px;
            padding: 20px;
            min-height: 170px;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            gap: 14px;
            scroll-snap-align: start;
            cursor: pointer;
        }

        .offer-content {
            position: relative;
            z-index: 2;
            max-width: 64%;
        }

        .offer-badge {
            display: inline-flex;
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 800;
            background: rgba(255, 255, 255, .18);
            margin-bottom: 10px;
        }

        .offer-image {
            position: absolute;
            left: -8px;
            bottom: -10px;
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 999px;
            border: 6px solid rgba(255, 255, 255, .18);
        }

        @media (max-width: 575px) {
            .featured-item-card {
                min-width: 190px;
                max-width: 190px;
            }

            .offer-slide {
                min-width: 92%;
            }

            .offer-content {
                max-width: 70%;
            }

            .offer-image {
                width: 120px;
                height: 120px;
            }
        }






















        /* Ordoraa Default Template */
        body {
            background: var(--soft-bg);
            color: var(--text);
        }

        .menu-wrapper {
            max-width: 640px;
            margin: 0 auto;
            padding: 0 14px 90px;
        }

        .od-hero {
            margin: 0 -14px 22px;
            background: var(--soft-bg);
            overflow: hidden;
        }

        .od-hero-cover {
            height: 168px;
            background-size: cover;
            background-position: center;
            background-color: #e5e7eb;
        }

        .od-hero-cover-fallback {
            width: 100%;
            height: 100%;
            background:
                linear-gradient(135deg, rgba(17, 24, 39, .2), rgba(37, 99, 235, .12)),
                radial-gradient(circle at 20% 20%, rgba(255, 255, 255, .5), transparent 25%),
                linear-gradient(135deg, var(--button-color), var(--theme-color));
        }

        .od-hero-body {
            text-align: center;
            padding: 0 20px 18px;
            background: var(--soft-bg);
        }

        .od-logo-wrap {
            width: 104px;
            height: 104px;
            margin: -52px auto 14px;
            padding: 6px;
            border-radius: 30px;
            background: #fff;
            box-shadow: 0 16px 35px rgba(15, 23, 42, .16);
            position: relative;
            z-index: 3;
        }

        .od-logo {
            width: 100%;
            height: 100%;
            border-radius: 24px;
            object-fit: cover;
            display: grid;
            place-items: center;
        }

        .od-logo-placeholder {
            background: linear-gradient(135deg, var(--theme-color), var(--button-color));
            color: #fff;
            font-size: 38px;
            font-weight: 900;
        }

        .od-title {
            font-size: 29px;
            font-weight: 950;
            letter-spacing: -.6px;
            margin: 0;
            color: var(--text);
        }

        .od-subtitle {
            color: var(--muted);
            font-size: 14px;
            margin-top: 5px;
            display: flex;
            justify-content: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .od-actions-main {
            margin-top: 20px;
        }

        .od-main-btn {
            width: 100%;
            min-height: 50px;
            border-radius: 18px;
            background: var(--button-color);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            font-weight: 850;
            text-decoration: none;
            box-shadow: 0 12px 24px rgba(37, 99, 235, .18);
        }

        .od-actions-secondary {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .od-secondary-btn {
            min-height: 48px;
            border-radius: 16px;
            background: #f3f4f6;
            color: var(--text);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 750;
            text-decoration: none;
        }

        .od-branch-switch {
            margin-bottom: 18px;
        }

        .od-branch-select {
            width: 100%;
            border: 0;
            background: #fff;
            border-radius: 18px;
            min-height: 48px;
            padding: 0 16px;
            font-weight: 800;
            color: var(--text);
            box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
        }

        .od-categories-wrap {
            position: sticky;
            top: 0;
            z-index: 20;
            background: color-mix(in srgb, var(--soft-bg) 88%, white);
            backdrop-filter: blur(12px);
            margin: 0 -14px 22px;
            padding: 12px 14px 10px;
        }

        .od-section-head {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }

        .od-section-head h2 {
            font-size: 25px;
            line-height: 1.1;
            font-weight: 950;
            margin: 0;
            color: var(--text);
        }

        .od-section-head p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        .od-section-head-inline h2 {
            font-size: 18px;
        }

        .od-category-tabs {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 4px;
        }

        .od-category-tabs::-webkit-scrollbar {
            display: none;
        }

        .od-category-pill {
            flex: 0 0 auto;
            min-height: 42px;
            padding: 0 18px;
            border-radius: 999px;
            background: #ece7de;
            color: #4b4036;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-weight: 850;
            box-shadow: 0 8px 18px rgba(15, 23, 42, .04);
        }

        .od-category-pill.active,
        .od-category-pill:hover {
            background: var(--theme-color);
            color: #fff;
        }

        .od-menu-section {
            margin-bottom: 30px;
        }

        .od-items-count {
            color: var(--muted);
            font-size: 13px;
            white-space: nowrap;
        }

        .od-items-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .od-food-card {
            background: #fff;
            border-radius: 28px;
            overflow: hidden;
            cursor: pointer;
            box-shadow:
                0 10px 26px rgba(15, 23, 42, .07),
                inset 0 0 0 1px rgba(229, 231, 235, .85);
        }

        .od-food-image-wrap {
            position: relative;
        }

        .od-food-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background: #f3f4f6;
            display: block;
        }

        .od-food-placeholder {
            display: grid;
            place-items: center;
            color: #9ca3af;
            font-size: 28px;
        }

        .od-food-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            border-radius: 999px;
            background: rgba(17, 24, 39, .78);
            color: #fff;
            font-size: 11px;
            padding: 5px 10px;
            font-weight: 850;
        }

        .od-food-body {
            padding: 13px;
        }

        .od-food-body h3 {
            font-size: 15px;
            font-weight: 900;
            margin: 0;
            color: var(--text);
        }

        .od-food-body p {
            font-size: 12.5px;
            color: var(--muted);
            margin: 6px 0 0;
            min-height: 34px;
        }

        .od-food-bottom {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 10px;
            margin-top: 12px;
        }

        .od-price {
            font-size: 16px;
            font-weight: 950;
            color: var(--text);
        }

        .od-price-sale {
            color: #059669;
        }

        .od-old-price {
            color: var(--muted);
            font-size: 12px;
            text-decoration: line-through;
            margin-top: 2px;
        }

        .od-add-mini {
            width: 34px;
            height: 34px;
            border: 0;
            border-radius: 14px;
            background: var(--button-color);
            color: #fff;
            display: grid;
            place-items: center;
        }

        .od-empty-section {
            background: #fff;
            border-radius: 24px;
            padding: 30px;
            text-align: center;
            color: var(--muted);
        }

        .od-footer {
            text-align: center;
            color: var(--muted);
            padding: 20px 0 34px;
            font-size: 13px;
        }

        .od-footer small {
            display: block;
            margin-top: 4px;
        }

        /* Ordoraa cart */
        .od-cart-bar {
            position: fixed;
            right: 0;
            left: 0;
            bottom: 0;
            z-index: 1040;
            padding: 12px 14px max(12px, env(safe-area-inset-bottom));
            background: linear-gradient(to top, rgba(246, 247, 251, .98), rgba(246, 247, 251, .72), transparent);
        }

        .od-cart-button {
            width: 100%;
            max-width: 640px;
            margin: 0 auto;
            min-height: 64px;
            border: 0;
            border-radius: 22px;
            padding: 10px 14px;
            background: var(--theme-color);
            color: #fff;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 18px 38px rgba(15, 23, 42, .28);
        }

        .od-cart-icon {
            width: 42px;
            height: 42px;
            border-radius: 16px;
            background: rgba(255, 255, 255, .16);
            display: grid;
            place-items: center;
            font-size: 20px;
        }

        .od-cart-text {
            flex: 1;
            text-align: start;
            line-height: 1.2;
        }

        .od-cart-text strong {
            display: block;
            font-size: 15px;
        }

        .od-cart-text small {
            display: block;
            opacity: .72;
            font-size: 12px;
        }

        .od-cart-total {
            font-weight: 950;
            white-space: nowrap;
        }

        /* Ordoraa item bottom sheet */
        .od-sheet-dialog {
            position: fixed;
            right: 0;
            left: 0;
            bottom: 0;
            margin: 0;
            max-width: 100%;
        }

        .od-sheet-content {
            border: 0;
            border-radius: 30px 30px 0 0;
            max-height: 92vh;
            overflow: hidden;
        }

        .od-sheet-handle {
            width: 54px;
            height: 5px;
            border-radius: 999px;
            background: #d1d5db;
            margin: 12px auto 8px;
        }

        .od-sheet-body {
            padding: 20px;
        }

        .od-item-sheet .modal-image {
            width: 100%;
            height: 215px;
            object-fit: cover;
            background: #f3f4f6;
        }

        .od-modal-title {
            font-size: 22px;
            font-weight: 950;
            margin: 0;
            color: var(--text);
        }

        .od-modal-desc {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        .od-modal-price-card {
            border-radius: 22px;
            padding: 14px;
            background: #f7f4ee;
            border: 1px solid rgba(229, 231, 235, .8);
        }

        .od-modal-price {
            font-size: 22px;
            font-weight: 950;
            color: var(--text);
        }

        .od-qty-control {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f3f4f6;
            border-radius: 18px;
            padding: 6px;
        }

        .od-qty-control button {
            width: 38px;
            height: 38px;
            border: 0;
            border-radius: 14px;
            background: #fff;
            font-size: 20px;
            font-weight: 900;
        }

        .od-qty-control input {
            width: 70px;
            height: 38px;
            border: 0;
            background: transparent;
            text-align: center;
            font-weight: 900;
        }

        .od-notes-input {
            border-radius: 18px;
            border-color: #e5e7eb;
        }

        .od-add-to-cart-btn {
            width: 100%;
            min-height: 52px;
            border: 0;
            border-radius: 18px;
            background: var(--button-color);
            color: #fff;
            font-weight: 900;
        }

        /* Content sections blending with Ordoraa */
        /* Ordoraa content sections */
        .od-featured-section,
        .od-offers-section,
        .od-collection-section {
            margin-bottom: 28px;
        }

        .od-swipe-hint {
            color: var(--muted);
            font-size: 13px;
            white-space: nowrap;
        }

        .od-featured-scroll,
        .od-offers-scroll {
            display: flex;
            gap: 16px;
            overflow-x: auto;
            padding: 2px 2px 12px;
            scroll-snap-type: x mandatory;
        }

        .od-featured-scroll::-webkit-scrollbar,
        .od-offers-scroll::-webkit-scrollbar {
            display: none;
        }

        .od-featured-card {
            min-width: 230px;
            max-width: 230px;
            background: #fff;
            border-radius: 30px;
            overflow: hidden;
            scroll-snap-align: start;
            cursor: pointer;
            box-shadow: 0 16px 34px rgba(15, 23, 42, .10);
        }

        .od-featured-image-wrap {
            position: relative;
        }

        .od-featured-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background: #f3f4f6;
            display: block;
        }

        .od-featured-badge {
            position: absolute;
            right: 12px;
            bottom: 12px;
            background: rgba(17, 24, 39, .76);
            color: #fff;
            border-radius: 999px;
            padding: 6px 11px;
            font-size: 12px;
            font-weight: 850;
        }

        .od-featured-body {
            padding: 17px;
        }

        .od-featured-body h3 {
            margin: 0 0 12px;
            font-size: 17px;
            font-weight: 950;
            color: var(--text);
        }

        .od-featured-price {
            font-size: 21px;
            font-weight: 950;
            color: var(--theme-color);
        }

        .od-collection-section {
            background: color-mix(in srgb, var(--collection-bg) 18%, white);
            color: var(--collection-text);
            border-radius: 30px;
            padding: 18px;
        }

        .od-collection-list {
            display: grid;
            gap: 12px;
        }

        .od-collection-item {
            display: grid;
            grid-template-columns: 92px 1fr;
            gap: 14px;
            align-items: center;
            background: rgba(255, 255, 255, .88);
            color: var(--text);
            border-radius: 24px;
            padding: 10px;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(15, 23, 42, .05);
        }

        .od-collection-image {
            width: 92px;
            height: 92px;
            border-radius: 20px;
            object-fit: cover;
            background: #f3f4f6;
        }

        .od-collection-info h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 950;
        }

        .od-collection-info p {
            margin: 5px 0 8px;
            color: var(--muted);
            font-size: 13px;
        }

        .od-collection-info strong {
            font-size: 16px;
        }

        .od-offer-card {
            position: relative;
            min-width: 88%;
            min-height: 178px;
            border-radius: 32px;
            padding: 22px;
            overflow: hidden;
            scroll-snap-align: start;
            cursor: pointer;
            box-shadow: 0 16px 34px rgba(15, 23, 42, .12);
        }

        .od-offer-content {
            position: relative;
            z-index: 2;
            max-width: 68%;
        }

        .od-offer-badge {
            display: inline-flex;
            border-radius: 999px;
            padding: 6px 11px;
            font-size: 11px;
            font-weight: 900;
            background: rgba(255, 255, 255, .18);
            margin-bottom: 10px;
        }

        .od-offer-content h3 {
            font-size: 21px;
            font-weight: 950;
            margin: 0 0 5px;
        }

        .od-offer-content p {
            margin: 0;
            opacity: .78;
            font-size: 13px;
        }

        .od-offer-price {
            margin-top: 12px;
            display: flex;
            gap: 8px;
            align-items: baseline;
        }

        .od-offer-price strong {
            font-size: 20px;
        }

        .od-offer-price span {
            opacity: .65;
            text-decoration: line-through;
            font-size: 13px;
        }

        .od-offer-btn {
            display: inline-flex;
            margin-top: 12px;
            background: rgba(255, 255, 255, .95);
            color: #111827;
            text-decoration: none;
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 850;
        }

        .od-offer-image {
            position: absolute;
            left: -18px;
            bottom: -16px;
            width: 155px;
            height: 155px;
            object-fit: cover;
            border-radius: 999px;
            border: 7px solid rgba(255, 255, 255, .18);
        }

        @media (max-width: 420px) {
            .od-featured-card {
                min-width: 215px;
                max-width: 215px;
            }

            .od-featured-image {
                height: 200px;
            }

            .od-offer-card {
                min-width: 92%;
            }

            .od-offer-image {
                width: 128px;
                height: 128px;
            }

            .od-offer-content {
                max-width: 72%;
            }
        }

        .od-hero+.od-branch-switch+.alert,
        .menu-wrapper>.alert {
            border: 0 !important;
            border-radius: 24px !important;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
        }


        .od-social-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 16px;
        }

        .od-social-icon {
            width: 40px;
            height: 40px;
            border-radius: 999px;
            display: inline-grid;
            place-items: center;
            text-decoration: none;
            background: rgba(255, 255, 255, .88);
            color: var(--text);
            border: 1px solid rgba(229, 231, 235, .8);
            box-shadow: 0 8px 22px rgba(15, 23, 42, .08);
            transition: transform .16s ease, box-shadow .16s ease, background .16s ease;
        }

        .od-social-icon i {
            font-size: 18px;
            line-height: 1;
        }

        .od-social-icon:hover {
            transform: translateY(-2px);
            color: var(--button-color);
            box-shadow: 0 12px 28px rgba(15, 23, 42, .13);
        }

        @media (max-width: 360px) {
            .od-social-row {
                gap: 8px;
            }

            .od-social-icon {
                width: 37px;
                height: 37px;
            }
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
















        .od-table-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 16px;
        }

        .od-table-action-btn {
            width: 100%;
            min-height: 44px;
            border: 0;
            border-radius: 16px;
            background: rgba(255, 255, 255, .92);
            color: var(--text);
            font-weight: 850;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            box-shadow: 0 8px 22px rgba(15, 23, 42, .08);
            border: 1px solid rgba(229, 231, 235, .85);
        }

        .od-table-action-btn i {
            color: var(--button-color);
        }

        .od-table-action-btn:active {
            transform: scale(.98);
        }










        /* Ordoraa Cover Social Hero */
        .od-cover-hero {
            position: relative;
            margin: 0 -14px 12px;
            background: var(--soft-bg);
        }

        .od-cover-hero-media {
            position: relative;
            height: 330px;
            overflow: hidden;
            background: #111827;
        }

        .od-cover-hero-media img,
        .od-cover-hero-fallback {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .od-cover-hero-fallback {
            background:
                linear-gradient(135deg, rgba(42, 18, 13, .22), rgba(140, 175, 80, .18)),
                radial-gradient(circle at 20% 20%, rgba(255, 255, 255, .3), transparent 28%),
                linear-gradient(135deg, var(--theme-color), var(--button-color));
        }

        .od-cover-hero-overlay {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(to bottom, rgba(0, 0, 0, .25), rgba(0, 0, 0, .18), rgba(0, 0, 0, .55));
        }

        .od-cover-back {
            position: absolute;
            top: 78px;
            right: 9%;
            width: 72px;
            height: 72px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            background: rgba(184, 119, 83, .82);
            color: #fff;
            text-decoration: none;
            font-size: 34px;
            backdrop-filter: blur(8px);
            box-shadow: 0 14px 34px rgba(0, 0, 0, .18);
        }

        .od-cover-title {
            position: absolute;
            right: 24px;
            left: 24px;
            bottom: 86px;
            color: #fff;
            text-align: center;
        }

        .od-cover-title h1 {
            margin: 0;
            font-size: 38px;
            line-height: 1.1;
            font-weight: 950;
            letter-spacing: -.8px;
            text-shadow: 0 3px 12px rgba(0, 0, 0, .25);
        }

        .od-cover-mini {
            display: inline-flex;
            margin-bottom: 10px;
            background: rgba(0, 0, 0, .42);
            color: #fff;
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 850;
            backdrop-filter: blur(8px);
        }

        .od-cover-bottom {
            position: relative;
            min-height: 150px;
            margin-top: -42px;
            padding: 48px 16px 18px;
            background: color-mix(in srgb, var(--soft-bg) 78%, #fff);
            border-radius: 42px 42px 0 0;
        }

        .od-cover-logo-wrap {
            position: absolute;
            top: -82px;
            left: 26px;
            width: 140px;
            height: 140px;
            border-radius: 999px;
            padding: 5px;
            background: #fff;
            border: 8px solid #000;
            box-shadow: 0 20px 45px rgba(0, 0, 0, .18);
            z-index: 3;
        }

        .od-cover-logo {
            width: 100%;
            height: 100%;
            border-radius: 999px;
            object-fit: cover;
            display: grid;
            place-items: center;
        }

        .od-cover-logo-placeholder {
            background: #fff;
            color: var(--theme-color);
            font-size: 54px;
            font-weight: 950;
        }

        .od-cover-socials {
            position: absolute;
            top: -40px;
            right: 18px;
            left: 190px;
            display: flex;
            gap: 22px;
            align-items: center;
            flex-wrap: wrap;
            z-index: 4;
        }

        .od-cover-social {
            width: 74px;
            height: 74px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            background: #fff6f6;
            color: var(--button-color);
            text-decoration: none;
            border: 6px solid #000;
            font-size: 36px;
            box-shadow: 0 16px 35px rgba(0, 0, 0, .14);
        }

        .od-cover-social:hover {
            color: var(--theme-color);
            transform: translateY(-2px);
        }

        .od-cover-info {
            padding-inline-end: 170px;
            min-height: 70px;
        }

        .od-cover-info strong {
            display: block;
            font-size: 22px;
            font-weight: 950;
            color: var(--text);
            margin-bottom: 4px;
        }

        .od-cover-info span {
            display: block;
            color: var(--muted);
            font-size: 13px;
        }

        .od-cover-table-actions {
            margin-top: 18px;
        }

        @media (max-width: 575px) {
            .od-cover-hero-media {
                height: 225px;
            }

            .od-cover-title {
                bottom: 82px;
            }

            .od-cover-title h1 {
                font-size: 31px;
            }

            .od-cover-back {
                top: 64px;
                right: 8%;
                width: 62px;
                height: 62px;
                font-size: 30px;
            }

            .od-cover-bottom {
                min-height: 60px;
                margin-top: -38px;
                padding-top: 60px;
                border-radius: 34px 34px 0 0;
            }

            .od-cover-logo-wrap {
                top: -60px;
                left: 16px;
                width: 115px;
                height: 115px;
                border-width: 5px;
            }

            .od-cover-socials {
                top: -30px;
                right: 18px;
                left: 158px;
                gap: 12px;
            }

            .od-cover-social {
                width: 58px;
                height: 58px;
                border-width: 5px;
                font-size: 27px;
            }

            .od-cover-info {
                padding-inline-end: 0;
                padding-inline-start: 132px;
                min-height: 56px;
            }

            .od-cover-info strong {
                font-size: 18px;
            }
        }

        @media (max-width: 390px) {
            .od-cover-socials {
                gap: 8px;
                left: 145px;
            }

            .od-cover-social {
                width: 50px;
                height: 50px;
                border-width: 4px;
                font-size: 23px;
            }

            .od-cover-logo-wrap {
                width: 118px;
                height: 118px;
            }

            .od-cover-info {
                padding-inline-start: 118px;
            }
        }















        /* .od-lang-switcher {
    position: absolute;
    top: 18px;
    left: 18px;
    z-index: 5;
    display: inline-flex;
    gap: 6px;
    background: rgba(0,0,0,.35);
    border-radius: 999px;
    padding: 5px;
    backdrop-filter: blur(8px);
}

.od-lang-link {
    min-width: 38px;
    height: 32px;
    border-radius: 999px;
    padding: 0 10px;
    color: #fff;
    text-decoration: none;
    font-weight: 850;
    font-size: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.od-lang-link.active {
    background: #fff;
    color: #111827;
} */
        /* Language switcher */
        .od-lang-toggle,
        .od-lang-dropdown {
            position: absolute;
            top: 18px;
            left: 18px;
            z-index: 7;
        }

        .od-lang-toggle {
            min-height: 40px;
            border-radius: 999px;
            padding: 5px 7px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(0, 0, 0, .38);
            color: #fff;
            text-decoration: none;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, .16);
        }

        .od-lang-toggle span {
            min-width: 38px;
            height: 30px;
            border-radius: 999px;
            display: inline-grid;
            place-items: center;
            font-size: 12px;
            font-weight: 900;
        }

        .od-lang-current {
            background: #fff;
            color: #111827;
        }

        .od-lang-next {
            background: rgba(255, 255, 255, .13);
            color: #fff;
        }

        .od-lang-toggle i {
            font-size: 13px;
            opacity: .85;
        }

        .od-lang-dropdown {
            min-width: 116px;
        }

        .od-lang-dropdown summary {
            list-style: none;
            cursor: pointer;
            min-height: 40px;
            border-radius: 999px;
            padding: 5px 10px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(0, 0, 0, .38);
            color: #fff;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, .16);
            font-size: 12px;
            font-weight: 900;
        }

        .od-lang-dropdown summary::-webkit-details-marker {
            display: none;
        }

        .od-lang-dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            min-width: 168px;
            padding: 8px;
            border-radius: 20px;
            background: rgba(0, 0, 0, .48);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            box-shadow: 0 16px 35px rgba(0, 0, 0, .22);
        }

        .od-lang-dropdown-menu a {
            min-height: 38px;
            padding: 0 10px;
            border-radius: 14px;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-size: 13px;
            font-weight: 800;
        }

        .od-lang-dropdown-menu a small {
            opacity: .72;
            font-size: 11px;
        }

        .od-lang-dropdown-menu a.active {
            background: #fff;
            color: #111827;
        }

        .od-lang-dropdown-menu a.active small {
            opacity: .7;
        }

        @media (max-width: 420px) {

            .od-lang-toggle,
            .od-lang-dropdown {
                top: 14px;
                left: 14px;
            }

            .od-lang-toggle {
                min-height: 37px;
            }

            .od-lang-toggle span {
                min-width: 34px;
                height: 28px;
                font-size: 11px;
            }

            .od-lang-dropdown summary {
                min-height: 37px;
                font-size: 11px;
            }
        }

        /* /////////////////////////////////////// */
        /* Language switcher */
        .od-lang-single-toggle,
        .od-lang-dropdown {
            position: absolute;
            top: 18px;
            left: 18px;
            z-index: 7;
        }

        .od-lang-single-toggle {
            min-width: 54px;
            height: 40px;
            border-radius: 999px;
            padding: 0 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, .38);
            color: #fff;
            text-decoration: none;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, .16);
            font-size: 13px;
            font-weight: 950;
            letter-spacing: .3px;
        }

        .od-lang-single-toggle:hover {
            color: #fff;
            background: rgba(0, 0, 0, .5);
        }

        .od-lang-dropdown {
            min-width: 116px;
        }

        .od-lang-dropdown summary {
            list-style: none;
            cursor: pointer;
            min-height: 40px;
            border-radius: 999px;
            padding: 5px 10px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(0, 0, 0, .38);
            color: #fff;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, .16);
            font-size: 12px;
            font-weight: 900;
        }

        .od-lang-dropdown summary::-webkit-details-marker {
            display: none;
        }

        .od-lang-dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            min-width: 168px;
            padding: 8px;
            border-radius: 20px;
            background: rgba(0, 0, 0, .48);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            box-shadow: 0 16px 35px rgba(0, 0, 0, .22);
        }

        .od-lang-dropdown-menu a {
            min-height: 38px;
            padding: 0 10px;
            border-radius: 14px;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-size: 13px;
            font-weight: 800;
        }

        .od-lang-dropdown-menu a small {
            opacity: .72;
            font-size: 11px;
        }

        .od-lang-dropdown-menu a.active {
            background: #fff;
            color: #111827;
        }

        .od-lang-dropdown-menu a.active small {
            opacity: .7;
        }

        @media (max-width: 420px) {

            .od-lang-single-toggle,
            .od-lang-dropdown {
                top: 14px;
                left: 14px;
            }

            .od-lang-single-toggle {
                min-width: 48px;
                height: 37px;
                font-size: 12px;
            }

            .od-lang-dropdown summary {
                min-height: 37px;
                font-size: 11px;
            }
        }

        /* /////////////////////////////////////// */





















        /* Offers carousel with background image */
        .od-offers-carousel-section {
            margin-bottom: 28px;
        }

        .od-offers-carousel {
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 18px 38px rgba(15, 23, 42, .12);
        }

        .od-offer-carousel-slide {
            position: relative;
            min-height: 230px;
            border-radius: 32px;
            overflow: hidden;
            cursor: pointer;
            background: var(--theme-color);
        }

        .od-offer-bg-image,
        .od-offer-bg-fallback,
        .od-offer-bg-overlay {
            position: absolute;
            inset: 0;
        }

        .od-offer-bg-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
        }

        .od-offer-bg-fallback {
            z-index: 0;
        }

        .od-offer-bg-overlay {
            z-index: 2;
            background:
                linear-gradient(90deg,
                    rgba(0, 0, 0, .72),
                    rgba(0, 0, 0, .46),
                    rgba(0, 0, 0, .18));
        }

        html[dir="rtl"] .od-offer-bg-overlay {
            background:
                linear-gradient(270deg,
                    rgba(0, 0, 0, .72),
                    rgba(0, 0, 0, .46),
                    rgba(0, 0, 0, .18));
        }

        .od-offer-carousel-content {
            position: relative;
            z-index: 3;
            min-height: 230px;
            padding: 24px;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-width: 72%;
        }

        .od-offer-carousel-badge {
            width: fit-content;
            display: inline-flex;
            border-radius: 999px;
            padding: 6px 12px;
            background: rgba(255, 255, 255, .18);
            backdrop-filter: blur(8px);
            font-size: 12px;
            font-weight: 900;
            margin-bottom: 12px;
        }

        .od-offer-carousel-content h3 {
            font-size: 28px;
            font-weight: 950;
            margin: 0;
            line-height: 1.1;
            text-shadow: 0 3px 14px rgba(0, 0, 0, .24);
        }

        .od-offer-carousel-subtitle {
            margin-top: 6px;
            font-size: 14px;
            opacity: .86;
            font-weight: 800;
        }

        .od-offer-carousel-content p {
            margin: 10px 0 0;
            font-size: 13px;
            line-height: 1.6;
            opacity: .86;
        }

        .od-offer-carousel-price {
            display: flex;
            align-items: baseline;
            gap: 10px;
            margin-top: 14px;
        }

        .od-offer-carousel-price strong {
            font-size: 22px;
            font-weight: 950;
        }

        .od-offer-carousel-price span {
            font-size: 13px;
            opacity: .72;
            text-decoration: line-through;
        }

        .od-offer-carousel-btn {
            width: fit-content;
            margin-top: 14px;
            border-radius: 999px;
            padding: 9px 14px;
            background: rgba(255, 255, 255, .94);
            color: #111827;
            text-decoration: none;
            font-size: 13px;
            font-weight: 900;
        }

        .od-offers-indicators {
            margin-bottom: 10px;
            z-index: 5;
        }

        .od-offers-indicators [data-bs-target] {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            border: 0;
            opacity: .45;
        }

        .od-offers-indicators .active {
            width: 22px;
            opacity: 1;
        }

        @media (max-width: 420px) {

            .od-offer-carousel-slide,
            .od-offer-carousel-content {
                min-height: 205px;
            }

            .od-offer-carousel-content {
                max-width: 82%;
                padding: 20px;
            }

            .od-offer-carousel-content h3 {
                font-size: 23px;
            }

            .od-offer-carousel-content p {
                font-size: 12.5px;
            }
        }









        /* ///////////////////////////////////////////////////////compact overlay//////////////////////////////////////////////// */

        /* Compact Ordoraa Menu Overrides */
        .menu-wrapper {
            max-width: 440px;
            padding: 0 10px 72px;
        }

        .od-cover-hero {
            margin: 0 -10px 16px;
        }

        .od-cover-hero-media {
            height: 230px;
        }

        .od-cover-title {
            bottom: 62px;
            right: 18px;
            left: 18px;
        }

        .od-cover-title h1 {
            font-size: 28px;
            line-height: 1.05;
        }

        .od-cover-mini {
            font-size: 12px;
            padding: 5px 12px;
            margin-bottom: 8px;
        }

        .od-cover-back {
            width: 48px;
            height: 48px;
            top: 48px;
            right: 18px;
            font-size: 24px;
        }

        .od-cover-bottom {
            margin-top: -30px;
            min-height: 50px;
            padding: 34px 12px 14px;
            border-radius: 30px 30px 0 0;
        }

        .od-cover-logo-wrap {
            top: -58px;
            left: 16px;
            width: 104px;
            height: 104px;
            padding: 5px;
            border-width: 5px;
        }

        .od-cover-socials {
            top: -38px;
            right: auto;
            left: 136px;
            gap: 8px;
        }

        .od-cover-social {
            width: 48px;
            height: 48px;
            border-width: 4px;
            font-size: 22px;
        }

        .od-cover-info {
            padding-inline-start: 108px;
            padding-inline-end: 0;
            min-height: 42px;
        }

        .od-cover-info strong {
            font-size: 16px;
            margin-bottom: 2px;
        }

        .od-cover-info span {
            font-size: 12px;
        }

        .od-lang-single-toggle {
            top: 14px;
            left: 14px;
            min-width: 48px;
            height: 34px;
            font-size: 12px;
            padding: 0 12px;
        }

        /* Table actions compact */
        .od-table-actions {
            gap: 8px;
            margin-top: 12px;
        }

        .od-table-action-btn {
            min-height: 42px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: 850;
            gap: 7px;
        }

        /* Table/session banner compact */
        .menu-wrapper>.alert,
        .od-hero+.od-branch-switch+.alert {
            padding: 12px 14px !important;
            border-radius: 20px !important;
            font-size: 14px;
            margin-bottom: 14px;
        }

        /* Section spacing */
        .od-section-head {
            margin-bottom: 5px;
        }

        .od-section-head h2 {
            font-size: 22px;
        }

        .od-section-head p {
            font-size: 12px;
            margin-top: 4px;
        }

        .od-featured-section,
        .od-offers-section,
        .od-collection-section,
        .od-offers-carousel-section {
            margin-bottom: 20px;
        }

        /* Offers carousel compact */
        .od-offers-carousel {
            border-radius: 24px;
        }

        .od-offer-carousel-slide,
        .od-offer-carousel-content {
            min-height: 176px;
        }

        .od-offer-carousel-slide {
            border-radius: 24px;
        }

        .od-offer-carousel-content {
            padding: 18px;
            max-width: 78%;
        }

        .od-offer-carousel-badge {
            font-size: 11px;
            padding: 5px 10px;
            margin-bottom: 8px;
        }

        .od-offer-carousel-content h3 {
            font-size: 23px;
        }

        .od-offer-carousel-subtitle {
            font-size: 13px;
            margin-top: 4px;
        }

        .od-offer-carousel-content p {
            font-size: 12px;
            line-height: 1.45;
            margin-top: 7px;
        }

        .od-offer-carousel-price {
            margin-top: 9px;
        }

        .od-offer-carousel-price strong {
            font-size: 19px;
        }

        .od-offer-carousel-price span {
            font-size: 12px;
        }

        .od-offers-indicators {
            margin-bottom: 6px;
        }

        /* Category pills compact */
        .od-categories-wrap {
            margin: 0 -10px 16px;
            padding: 10px 10px 8px;
        }

        .od-category-tabs {
            gap: 8px;
        }

        .od-category-pill {
            min-height: 36px;
            padding: 0 14px;
            font-size: 13px;
        }

        /* Items compact */
        .od-menu-section {
            margin-bottom: 22px;
        }

        .od-items-grid {
            gap: 10px;
        }

        .od-food-card {
            border-radius: 22px;
        }

        .od-food-image {
            height: 112px;
        }

        .od-food-body {
            padding: 10px;
        }

        .od-food-body h3 {
            font-size: 13.5px;
        }

        .od-food-body p {
            font-size: 11.5px;
            min-height: 28px;
            margin-top: 4px;
        }

        .od-food-bottom {
            margin-top: 8px;
        }

        .od-price {
            font-size: 14px;
        }

        .od-old-price {
            font-size: 11px;
        }

        .od-add-mini {
            width: 30px;
            height: 30px;
            border-radius: 12px;
        }

        /* Featured compact */
        .od-featured-scroll {
            gap: 12px;
        }

        .od-featured-card {
            min-width: 155px;
            max-width: 185px;
            border-radius: 24px;
        }

        .od-featured-image {
            height: 155px;
        }

        .od-featured-body {
            padding: 12px;
        }

        .od-featured-body h3 {
            font-size: 15px;
            margin-bottom: 8px;
        }

        .od-featured-price {
            font-size: 18px;
        }

        /* Footer compact */
        .od-footer {
            padding: 14px 0 24px;
            font-size: 12px;
        }

        @media (max-width: 380px) {
            .od-cover-hero-media {
                height: 215px;
            }

            .od-cover-title h1 {
                font-size: 25px;
            }

            .od-cover-logo-wrap {
                width: 92px;
                height: 92px;
                top: -50px;
            }

            .od-cover-socials {
                left: 120px;
                top: -34px;
                gap: 7px;
            }

            .od-cover-social {
                width: 42px;
                height: 42px;
                font-size: 19px;
            }

            .od-cover-info {
                padding-inline-start: 96px;
            }

            .od-offer-carousel-slide,
            .od-offer-carousel-content {
                min-height: 165px;
            }

            .od-offer-carousel-content h3 {
                font-size: 21px;
            }

            .od-food-image {
                height: 170px;
            }
        }

        /*  */

        /* Ordoraa compact typography and spacing */
        .menu-wrapper {
            max-width: 430px;
            padding-inline: 10px;
            padding-bottom: 76px;
        }

        /* Hero */
        .od-cover-hero {
            margin: 0 -10px 16px;
        }

        .od-cover-hero-media {
            height: 220px;
        }

        .od-cover-title {
            bottom: 58px;
            right: 18px;
            left: 18px;
        }

        .od-cover-title h1 {
            font-size: 27px;
            line-height: 1.12;
            font-weight: 900;
        }

        .od-cover-mini {
            font-size: 11.5px;
            line-height: 1;
            padding: 6px 12px;
            margin-bottom: 8px;
        }



        .od-cover-logo-wrap {
            top: -54px;
            left: 16px;
            width: 98px;
            height: 98px;
            padding: 5px;
            border-width: 5px;
        }

        .od-cover-socials {
            top: -25px;
            left: 128px;
            right: auto;
            gap: 8px;
        }

        .od-cover-social {
            width: 46px;
            height: 46px;
            border-width: 4px;
            font-size: 21px;
        }

        .od-cover-info {
            padding-inline-start: 104px;
            padding-inline-end: 0;
            min-height: 40px;
        }

        .od-cover-info strong {
            font-size: 15.5px;
            line-height: 1.25;
            font-weight: 900;
            margin-bottom: 2px;
        }

        .od-cover-info span {
            font-size: 11.5px;
            line-height: 1.4;
        }

        /* language */
        .od-lang-single-toggle {
            top: 14px;
            left: 14px;
            min-width: 46px;
            height: 34px;
            font-size: 12px;
            font-weight: 900;
        }

        /* table buttons */
        .od-table-actions {
            margin-top: 12px;
            gap: 8px;
        }

        .od-table-action-btn {
            min-height: 40px;
            border-radius: 14px;
            font-size: 13.5px;
            font-weight: 800;
            gap: 7px;
        }

        /* alerts / invoice */
        .menu-wrapper>.alert,
        .od-hero+.od-branch-switch+.alert {
            padding: 11px 13px !important;
            border-radius: 18px !important;
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 14px;
        }

        /* headings */
        .od-section-head {
            margin-bottom: 9px;
            align-items: flex-end;
        }

        .od-section-head h2 {
            font-size: 21px;
            line-height: 1.2;
            font-weight: 900;
        }

        .od-section-head p {
            font-size: 12px;
            margin-top: 3px;
            line-height: 1.4;
        }

        .od-swipe-hint,
        .od-items-count {
            font-size: 12px;
        }

        /* sections spacing */
        .od-featured-section,
        .od-offers-section,
        .od-collection-section,
        .od-offers-carousel-section,
        .od-menu-section {
            margin-bottom: 20px;
        }

        /* offers carousel */
        .od-offers-carousel {
            border-radius: 24px;
        }

        .od-offer-carousel-slide,
        .od-offer-carousel-content {
            min-height: 170px;
        }

        .od-offer-carousel-slide {
            border-radius: 24px;
        }

        .od-offer-carousel-content {
            padding: 17px;
            max-width: 78%;
        }

        .od-offer-carousel-badge {
            font-size: 10.5px;
            padding: 5px 9px;
            margin-bottom: 7px;
        }

        .od-offer-carousel-content h3 {
            font-size: 22px;
            line-height: 1.15;
            font-weight: 900;
        }

        .od-offer-carousel-subtitle {
            font-size: 12.5px;
            margin-top: 4px;
            font-weight: 700;
        }

        .od-offer-carousel-content p {
            font-size: 11.5px;
            line-height: 1.45;
            margin-top: 6px;
        }

        .od-offer-carousel-price {
            margin-top: 8px;
            gap: 8px;
        }

        .od-offer-carousel-price strong {
            font-size: 18px;
            line-height: 1;
        }

        .od-offer-carousel-price span {
            font-size: 11.5px;
        }

        .od-offers-indicators {
            margin-bottom: 6px;
        }

        /* categories */
        .od-categories-wrap {
            margin: 0 -10px 16px;
            padding: 9px 10px 7px;
        }

        .od-category-tabs {
            gap: 7px;
        }

        .od-category-pill {
            min-height: 34px;
            padding: 0 13px;
            font-size: 12.5px;
            font-weight: 800;
        }

        /* item cards */
        .od-items-grid {
            gap: 10px;
        }

        .od-food-card {
            border-radius: 20px;
        }

        /* .od-food-image {
    height: 108px;
} */

        .od-food-body {
            padding: 9px;
        }

        .od-food-body h3 {
            font-size: 13px;
            line-height: 1.35;
            font-weight: 850;
        }

        .od-food-body p {
            font-size: 11px;
            line-height: 1.4;
            min-height: 26px;
            margin-top: 4px;
        }

        .od-food-bottom {
            margin-top: 7px;
        }

        .od-price {
            font-size: 13.5px;
            font-weight: 900;
        }

        .od-old-price {
            font-size: 10.5px;
        }

        .od-add-mini {
            width: 28px;
            height: 28px;
            border-radius: 11px;
        }

        /* featured horizontal */
        .od-featured-scroll {
            gap: 10px;
            padding-bottom: 8px;
        }

        .od-featured-card {
            min-width: 176px;
            max-width: 176px;
            border-radius: 22px;
        }

        /* .od-featured-image {
    height: 124px;
} */

        .od-featured-badge {
            right: 9px;
            bottom: 9px;
            font-size: 10.5px;
            padding: 5px 9px;
        }

        .od-featured-body {
            padding: 11px;
        }

        .od-featured-body h3 {
            font-size: 14px;
            line-height: 1.35;
            margin-bottom: 7px;
        }

        .od-featured-price {
            font-size: 17px;
        }

        /* collection list */
        .od-collection-section {
            border-radius: 24px;
            padding: 14px;
        }

        .od-collection-list {
            gap: 10px;
        }

        .od-collection-item {
            grid-template-columns: 76px 1fr;
            border-radius: 20px;
            padding: 9px;
            gap: 11px;
        }

        .od-collection-image {
            width: 76px;
            height: 76px;
            border-radius: 16px;
        }

        .od-collection-info h3 {
            font-size: 14px;
        }

        .od-collection-info p {
            font-size: 11.5px;
            margin: 4px 0 6px;
        }

        .od-collection-info strong {
            font-size: 14px;
        }

        /* cart */
        .od-cart-bar {
            padding: 10px 10px max(10px, env(safe-area-inset-bottom));
        }

        .od-cart-button {
            min-height: 56px;
            border-radius: 18px;
            padding: 9px 12px;
        }

        .od-cart-icon {
            width: 38px;
            height: 38px;
            border-radius: 14px;
        }

        .od-cart-text strong {
            font-size: 14px;
        }

        .od-cart-text small {
            font-size: 11px;
        }

        /* modal bottom sheet */
        .od-sheet-content {
            border-radius: 24px 24px 0 0;
        }

        .od-item-sheet .modal-image {
            height: 185px;
        }

        .od-sheet-body {
            padding: 16px;
        }

        .od-modal-title {
            font-size: 20px;
        }

        .od-modal-desc {
            font-size: 12.5px;
        }

        .od-modal-price-card {
            border-radius: 18px;
            padding: 12px;
        }

        .od-modal-price {
            font-size: 20px;
        }

        /* footer */
        .od-footer {
            padding: 14px 0 22px;
            font-size: 11.5px;
        }

        @media (max-width: 380px) {
            .menu-wrapper {
                padding-inline: 9px;
            }

            .od-cover-hero {
                margin-inline: -9px;
            }

            .od-cover-hero-media {
                height: 205px;
            }

            .od-cover-title h1 {
                font-size: 24px;
            }

            .od-cover-logo-wrap {
                width: 88px;
                height: 88px;
                top: -48px;
            }

            .od-cover-socials {
                left: 112px;
                top: -32px;
                gap: 6px;
            }

            .od-cover-social {
                width: 40px;
                height: 40px;
                font-size: 18px;
                border-width: 3px;
            }

            .od-cover-info {
                padding-inline-start: 92px;
            }

            /* .od-food-image {
        height: 100px;
    } */

            .od-offer-carousel-slide,
            .od-offer-carousel-content {
                min-height: 158px;
            }

            .od-offer-carousel-content h3 {
                font-size: 20px;
            }
        }



        /* ///////////////////////////////////////////////////////end compact overlay//////////////////////////////////////////////// */


        /* Hero top row: branch + language */
        .od-cover-top-row {
            position: absolute;
            top: 14px;
            right: 14px;
            left: 14px;
            z-index: 8;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            pointer-events: none;
        }

        .od-cover-top-row>* {
            pointer-events: auto;
        }

        .od-cover-branch-pill {
            min-height: 34px;
            max-width: 58%;
            border-radius: 999px;
            padding: 0 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, .38);
            color: #fff;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, .16);
            font-size: 12.5px;
            font-weight: 850;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .od-cover-top-row .od-lang-single-toggle,
        .od-cover-top-row .od-lang-dropdown {
            position: static;
            top: auto;
            left: auto;
        }

        .od-cover-top-row .od-lang-single-toggle {
            min-width: 48px;
            height: 34px;
            font-size: 12px;
        }

        /* dropdown inside row */
        .od-cover-top-row .od-lang-dropdown-menu {
            left: 0;
        }

        /* adjust title since branch moved up */
        .od-cover-title {
            bottom: 58px;
        }

        .od-cover-title h1 {
            font-size: 27px;
        }

        /* remove old mini badge spacing if still exists */
        .od-cover-mini {
            display: none;
        }

        @media (max-width: 420px) {
            .od-cover-top-row {
                top: 12px;
                right: 12px;
                left: 12px;
            }

            .od-cover-branch-pill {
                min-height: 32px;
                max-width: 62%;
                padding: 0 12px;
                font-size: 12px;
            }

            .od-cover-top-row .od-lang-single-toggle {
                min-width: 46px;
                height: 32px;
                font-size: 11.5px;
            }
        }















        html[dir="rtl"],
        html[lang="ar"],
        html[lang^="ar"] body {
            font-family: var(--font-ar);
        }

        html[dir="ltr"],
        html:not([lang^="ar"]) body {
            font-family: var(--font-en);
        }

        body {
            font-size: 14px;
            line-height: 1.55;
            letter-spacing: 0;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }















        .od-account-btn {
    min-width: 36px;
    height: 34px;
    border-radius: 999px;
    display: inline-grid;
    place-items: center;
    background: rgba(0, 0, 0, .38);
    color: #fff;
    text-decoration: none;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, .16);
}

.od-account-btn:hover {
    color: #fff;
    background: rgba(0, 0, 0, .5);
}









.od-ios-install-hint {
    position: fixed;
    right: 14px;
    left: 14px;
    bottom: 86px;
    z-index: 999;
    border-radius: 20px;
    padding: 12px 42px 12px 14px;
    background: rgba(17, 24, 39, .94);
    color: #fff;
    box-shadow: 0 16px 34px rgba(15, 23, 42, .24);
    font-size: 13px;
}

.od-ios-install-hint strong {
    display: block;
    font-weight: 900;
    margin-bottom: 3px;
}

.od-ios-install-hint span {
    opacity: .85;
}

.od-ios-install-close {
    position: absolute;
    top: 8px;
    right: 10px;
    width: 24px;
    height: 24px;
    border: 0;
    border-radius: 999px;
    background: rgba(255, 255, 255, .16);
    color: #fff;
}






.od-delivery-fields {
    margin-top: 12px;
    padding: 12px;
    border-radius: 18px;
    background: rgba(245, 241, 234, .72);
    border: 1px solid rgba(31, 23, 19, .08);
}

.od-field {
    margin-bottom: 10px;
}

.od-label {
    display: block;
    font-size: 12px;
    font-weight: 900;
    margin-bottom: 6px;
    color: #3b2f2a;
}

.od-input {
    width: 100%;
    min-height: 42px;
    border-radius: 14px;
    border: 1px solid rgba(31, 23, 19, .12);
    background: #fff;
    padding: 8px 12px;
    font-size: 14px;
}

.od-address-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 8px;
}

.od-help {
    margin-top: 5px;
    font-size: 12px;
    color: #6b7280;
}

.od-error {
    margin-top: 5px;
    font-size: 12px;
    color: #dc2626;
}

@media (max-width: 480px) {
    .od-address-grid {
        grid-template-columns: 1fr;
    }
}
    </style>
    @if (!empty($menuTheme['custom_css']))
        {!! $menuTheme['custom_css'] !!}
    @endif
</head>

<body>

    <div class="menu-wrapper">
    
        @if (request()->filled('preview_template') ||
                request()->boolean('theme_preview') ||
                request()->boolean('content_section_preview'))
            <div class="alert alert-warning rounded-4">
                أنت تشاهد معاينة مؤقتة، ولم يتم حفظ هذه التغييرات بعد.
            </div>
        @endif

        @include($menuTheme['views']['hero'])

        @include($menuTheme['views']['branch_switch'])

        @include($menuTheme['views']['invoice'])

        @include('public.restaurant-menu.templates.sections.content-sections.index')

        <div id="iosInstallHint" class="od-ios-install-hint" style="display:none;">
    <button type="button" class="od-ios-install-close" onclick="this.parentElement.style.display='none'">
        ×
    </button>

    <strong>تثبيت المنيو</strong>
    <span>
        من زر المشاركة في Safari اختر Add to Home Screen.
    </span>
</div>
        @include($menuTheme['views']['category_tabs'])

        @include($menuTheme['views']['items'])

        @include($menuTheme['views']['footer'])
    </div>

 

    @include($menuTheme['views']['cart'])
    @include($menuTheme['views']['item_modal'])



    <button type="button" id="installPwaBtn" class="od-install-pwa-btn" style="display:none;">
    <i class="bi bi-phone"></i>
    تثبيت المنيو
</button>



    <div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h3 class="h5 fw-bold mb-1">مراجعة الطلب</h3>
                            <p class="text-muted small mb-0">راجع الأصناف ثم املأ بياناتك لإرسال الطلب.</p>
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>راجع البيانات:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div id="cartItemsWrap" class="mb-3"></div>

                    {{-- <div class="border rounded-4 p-3 mb-3">
                        <div class="d-flex justify-content-between">
                            <span>الإجمالي</span>
                            <strong id="cartModalTotal">0.00</strong>
                        </div>
                    </div> --}}
<div class="border rounded-4 p-3 mb-3">
    <div id="deliverySummary" class="small text-muted mb-2" style="display:none;"></div>

    <div class="d-flex justify-content-between">
        <span>الإجمالي</span>
        <strong id="cartModalTotal">0.00</strong>
    </div>
</div>

                    <form method="POST"
                        action="{{ route('public.restaurant-menu.orders.store', [$workspace, $branch]) }}"
                        id="checkoutForm">
                        @csrf

                        @if (!empty($currentInvoice) && !empty($currentInvoiceGuest))
                            <input type="hidden" name="invoice_id" value="{{ $currentInvoice->id }}">
                        @endif

                        @if (!empty($selectedTable))
                            <input type="hidden" name="table_code" value="{{ $selectedTable->code }}">
                        @endif

                        <div id="checkoutItemsInputs"></div>

                        <div class="mb-3">
                            <label class="form-label">الاسم <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                                class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                            <input type="text" name="customer_phone" value="{{ old('customer_phone') }}"
                                class="form-control" required placeholder="2010xxxxxxxx">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                                class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">نوع الطلب <span class="text-danger">*</span></label>

                            <select name="order_type" id="orderType" class="form-select" required>
                                <option value="takeaway" @selected(old('order_type') === 'takeaway' && empty($selectedTable))>
                                    تيك أواي
                                </option>

                                <option value="dine_in" @selected(old('order_type', !empty($selectedTable) || !empty($currentInvoice) ? 'dine_in' : null) === 'dine_in')>
                                    داخل المكان
                                </option>

                                <option value="delivery" @selected(old('order_type') === 'delivery')>
                                    دليفري
                                </option>
                            </select>


                        
                        </div>

                        <div class="mb-3" id="tableNumberWrap" style="display:none;">
                            <label class="form-label">رقم الطاولة</label>
                            @if (!empty($selectedTable))
                                <input type="text" value="{{ $selectedTable->number }}" class="form-control"
                                    readonly>
                                <input type="hidden" name="table_number" value="{{ $selectedTable->number }}">
                            @else
                                <input type="text" name="table_number" value="{{ old('table_number') }}"
                                    class="form-control" placeholder="مثال: 5">
                            @endif
                        </div>

                        <div class="mb-3" id="deliveryAddressWrap" style="display:none;">
                            <label class="form-label">عنوان التوصيل</label>
                            <textarea name="delivery_address" rows="3" class="form-control" placeholder="اكتب العنوان بالتفصيل">{{ old('delivery_address') }}</textarea>
                        </div>

                        <div id="deliveryFields" class="od-delivery-fields" style="display:none;">
    <div class="od-field">
        <label class="od-label">
            منطقة التوصيل
        </label>

        <select name="delivery_zone_id" id="deliveryZoneSelect" class="od-input">
            <option value="">اختر منطقة التوصيل</option>

            @foreach($deliveryZones as $zone)
                <option
                    value="{{ $zone->id }}"
                    data-fee="{{ (float) $zone->delivery_fee }}"
                    data-min-order="{{ $zone->min_order_amount !== null ? (float) $zone->min_order_amount : '' }}"
                    data-estimated="{{ $zone->estimated_minutes }}"
                    @selected(old('delivery_zone_id') == $zone->id)
                >
                    {{ $zone->name }}
                    -
                    {{ number_format((float) $zone->delivery_fee, 2) }}
                </option>
            @endforeach
        </select>

        @error('delivery_zone_id')
            <div class="od-error">{{ $message }}</div>
        @enderror

        <div id="deliveryZoneHint" class="od-help"></div>
    </div>

    <div class="od-field">
        <label class="od-label">
            العنوان بالتفصيل
        </label>

        <textarea
            name="delivery_address_details"
            id="deliveryAddressDetails"
            rows="3"
            class="od-input"
            placeholder="اكتب العنوان بالتفصيل"
        >{{ old('delivery_address_details') }}</textarea>

        @error('delivery_address_details')
            <div class="od-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="od-address-grid">
        <div class="od-field">
            <label class="od-label">المنطقة / الحي</label>
            <input
                type="text"
                name="delivery_area"
                value="{{ old('delivery_area') }}"
                class="od-input"
            >
        </div>

        <div class="od-field">
            <label class="od-label">العمارة</label>
            <input
                type="text"
                name="delivery_building"
                value="{{ old('delivery_building') }}"
                class="od-input"
            >
        </div>

        <div class="od-field">
            <label class="od-label">الدور</label>
            <input
                type="text"
                name="delivery_floor"
                value="{{ old('delivery_floor') }}"
                class="od-input"
            >
        </div>

        <div class="od-field">
            <label class="od-label">الشقة</label>
            <input
                type="text"
                name="delivery_apartment"
                value="{{ old('delivery_apartment') }}"
                class="od-input"
            >
        </div>
    </div>

    <div class="od-field">
        <label class="od-label">علامة مميزة</label>
        <input
            type="text"
            name="delivery_landmark"
            value="{{ old('delivery_landmark') }}"
            class="od-input"
            placeholder="مثال: بجوار الصيدلية"
        >
    </div>
</div>

                        <div class="mb-3">
                            <label class="form-label">ملاحظات عامة</label>
                            <textarea name="notes" rows="3" class="form-control" placeholder="أي ملاحظات على الطلب">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-main w-100" id="submitOrderBtn">
                            إرسال الطلب
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    @if (!empty($openInvoiceEnabled) && !empty($selectedTable))
        <div class="modal fade" id="openInvoiceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h3 class="h5 fw-bold mb-1">فتح فاتورة للطاولة</h3>
                                <p class="text-muted small mb-0">
                                    سيتم إنشاء فاتورة مفتوحة لمدة محددة، وسيظهر لك PIN للمشاركة.
                                </p>
                            </div>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <form method="POST"
                            action="{{ route('public.restaurant-menu.invoices.open', [$workspace, $branch]) }}">
                            @csrf

                            <input type="hidden" name="table_code" value="{{ $selectedTable->code }}">
                            <input type="hidden" name="table_number" value="{{ $selectedTable->number }}">

                            <div class="mb-3">
                                <label class="form-label">اسمك <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                                    class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                <input type="text" name="customer_phone" value="{{ old('customer_phone') }}"
                                    class="form-control" required placeholder="2010xxxxxxxx">
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                فتح الفاتورة
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif




    @if (!empty($openInvoiceEnabled) && !empty($openInvoice) && $invoiceJoinPolicy === 'allow_with_pin')
        <div class="modal fade" id="joinInvoiceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h3 class="h5 fw-bold mb-1">الانضمام للفاتورة</h3>
                                <p class="text-muted small mb-0">
                                    أدخل PIN الخاص بالفاتورة المفتوحة لهذه الطاولة.
                                </p>
                            </div>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <form method="POST"
                            action="{{ route('public.restaurant-menu.invoices.join', [$workspace, $branch, $openInvoice]) }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">اسمك <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                                    class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                <input type="text" name="customer_phone" value="{{ old('customer_phone') }}"
                                    class="form-control" required placeholder="2010xxxxxxxx">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">PIN <span class="text-danger">*</span></label>
                                <input type="text" name="pin" class="form-control text-center" required
                                    dir="ltr" placeholder="24-81" maxlength="20"
                                    style="font-size: 22px; letter-spacing: 3px;">
                            </div>

                            <button type="submit" class="btn btn-dark w-100">
                                الانضمام
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif


@php
    $deliveryZonesPayload = collect($deliveryZones ?? [])->map(function ($zone) {
        return [
            'id' => $zone->id,
            'name' => $zone->name,
            'delivery_fee' => (float) $zone->delivery_fee,
            'min_order_amount' => $zone->min_order_amount !== null ? (float) $zone->min_order_amount : null,
            'estimated_minutes' => $zone->estimated_minutes,
        ];
    })->values();

    $deliveryConfigPayload = [
        'enabled' => (bool) ($deliverySettings?->is_enabled ?? false),
        'feeCalculationMode' => $deliverySettings?->fee_calculation_mode ?? 'zone',
        'feeIncludedInTotal' => (bool) ($deliverySettings?->delivery_fee_included_in_total ?? true),
        'showFeeOnReceipt' => (bool) ($deliverySettings?->show_delivery_fee_on_receipt ?? true),
        'requireZone' => (bool) ($deliverySettings?->require_zone_for_delivery ?? true),
        'zones' => $deliveryZonesPayload,
    ];
@endphp

    <script>
        window.restaurantMenuItems = @json($itemsPayload);
        window.restaurantMenuOffers = @json($offersPayload);
        console.log('OFFERS PAYLOAD', window.restaurantMenuOffers);
        
            window.deliveryConfig = @json($deliveryConfigPayload);

    console.log('OFFERS PAYLOAD', window.restaurantMenuOffers);
    console.log('DELIVERY CONFIG', window.deliveryConfig);

    //      window.deliveryConfig = {
    //     enabled: @json((bool) ($deliverySettings?->is_enabled ?? false)),
    //     feeCalculationMode: @json($deliverySettings?->fee_calculation_mode ?? 'zone'),
    //     feeIncludedInTotal: @json((bool) ($deliverySettings?->delivery_fee_included_in_total ?? true)),
    //     showFeeOnReceipt: @json((bool) ($deliverySettings?->show_delivery_fee_on_receipt ?? true)),
    //     requireZone: @json((bool) ($deliverySettings?->require_zone_for_delivery ?? true)),
    // };
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        window.hasCurrentInvoice = @json(!empty($currentInvoice) && !empty($currentInvoiceGuest));
        window.openInvoiceEnabled = @json(!empty($openInvoiceEnabled));
        window.hasSelectedTable = @json(!empty($selectedTable));
    </script>

    <script>
        (function() {
            const modalElement = document.getElementById('itemModal');

            if (!modalElement) {
                console.error('itemModal element not found');
                return;
            }

            const modal = bootstrap.Modal.getOrCreateInstance(modalElement);



            const imageWrap = document.getElementById('modalImageWrap');
            const title = document.getElementById('modalTitle');
            const description = document.getElementById('modalDescription');
            const meta = document.getElementById('modalMeta');
            const price = document.getElementById('modalPrice');
            const oldPrice = document.getElementById('modalOldPrice');
            const variantsWrap = document.getElementById('modalVariants');
            const groupsWrap = document.getElementById('modalOptionGroups');

            const qtyInput = document.getElementById('modalQty');
            const qtyMinus = document.getElementById('qtyMinus');
            const qtyPlus = document.getElementById('qtyPlus');
            const notesInput = document.getElementById('modalItemNotes');
            const addToCartBtn = document.getElementById('addToCartBtn');

            const cartFloat = document.getElementById('cartFloat');
            const cartFloatTotal = document.getElementById('cartFloatTotal');
            const cartItemsWrap = document.getElementById('cartItemsWrap');
            const cartModalTotal = document.getElementById('cartModalTotal');
            const checkoutItemsInputs = document.getElementById('checkoutItemsInputs');
            const checkoutForm = document.getElementById('checkoutForm');

            const orderType = document.getElementById('orderType');
            const tableNumberWrap = document.getElementById('tableNumberWrap');
            const deliveryAddressWrap = document.getElementById('deliveryAddressWrap');

        

            let currentItem = null;

const CART_STORAGE_KEY = 'restaurant_menu_cart_{{ $workspace->id }}_{{ $branch->id ?? "main" }}';

let cart = loadCart();

function loadCart() {
    try {
        const stored = localStorage.getItem(CART_STORAGE_KEY);

        if (!stored) {
            return [];
        }

        const parsed = JSON.parse(stored);

        return Array.isArray(parsed) ? parsed : [];
    } catch (error) {
        console.error('Failed to load cart', error);
        return [];
    }
}

function saveCart() {
    try {
        localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
    } catch (error) {
        console.error('Failed to save cart', error);
    }
}

function clearCart() {
    localStorage.removeItem(CART_STORAGE_KEY);
    cart = [];
    renderCart();
}


const menuUrlParams = new URLSearchParams(window.location.search);

if (menuUrlParams.get('clear_cart') === '1') {
    localStorage.removeItem(CART_STORAGE_KEY);
    cart = [];

    menuUrlParams.delete('clear_cart');

    const cleanUrl = window.location.pathname
        + (menuUrlParams.toString() ? '?' + menuUrlParams.toString() : '');

    window.history.replaceState({}, '', cleanUrl);
}






            function money(value, currency) {
                const number = Number(value || 0).toFixed(2);
                return `${number} ${currency || ''}`;
            }

            function itemBasePrice(item) {
                return item.sale_price !== null ? Number(item.sale_price) : Number(item.price);
            }

            function variantPrice(variant) {
                return variant.sale_price !== null ? Number(variant.sale_price) : Number(variant.price);
            }

            function getSelectedVariant(item) {
                const checked = document.querySelector('input[name="variant_id"]:checked');

                if (!checked || !checked.value) {
                    return null;
                }

                return (item.variants || []).find(v => String(v.id) === String(checked.value)) || null;
            }

            function getSelectedOptions(item) {
                const selected = [];

                (item.option_groups || []).forEach(function(group) {
                    const inputs = document.querySelectorAll(`[data-option-group="${group.id}"]:checked`);

                    inputs.forEach(function(input) {
                        const option = (group.options || []).find(o => String(o.id) === String(input
                            .value));

                        if (option) {
                            selected.push({
                                ...option,
                                group_id: group.id,
                                group_name: group.name
                            });
                        }
                    });
                });

                return selected;
            }

            function calculateLine(item, variant, options, quantity) {
                const unit = variant ? variantPrice(variant) : itemBasePrice(item);
                const optionsTotal = options.reduce((sum, option) => sum + Number(option.price || 0), 0);

                return {
                    unit,
                    optionsTotal,
                    lineTotal: (unit + optionsTotal) * quantity
                };
            }

            function validateSelections(item) {
                for (const group of (item.option_groups || [])) {
                    const checked = document.querySelectorAll(`[data-option-group="${group.id}"]:checked`);
                    const count = checked.length;

                    if (group.is_required && count < Math.max(1, Number(group.min_choices || 0))) {
                        alert(`يجب اختيار ${group.name}`);
                        return false;
                    }

                    if (group.max_choices && count > Number(group.max_choices)) {
                        alert(`تجاوزت الحد الأقصى في ${group.name}`);
                        return false;
                    }

                    if (group.type === 'single' && count > 1) {
                        alert(`يمكن اختيار خيار واحد فقط من ${group.name}`);
                        return false;
                    }
                }

                return true;
            }

            function renderItem(item) {
                currentItem = item;

                qtyInput.value = 1;
                notesInput.value = '';

                imageWrap.innerHTML = item.image ?
                    `<img src="${item.image}" alt="${item.name}" class="modal-image">` :
                    `<div class="modal-image d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image fs-1"></i></div>`;

                title.textContent = item.name;
                description.textContent = item.description || '';

                meta.innerHTML = '';

                if (item.calories) {
                    meta.innerHTML += `<span class="badge bg-light text-dark border">${item.calories} كالوري</span>`;
                }

                if (item.preparation_time_minutes) {
                    meta.innerHTML +=
                        `<span class="badge bg-light text-dark border">${item.preparation_time_minutes} دقيقة</span>`;
                }

                const finalPrice = item.sale_price !== null ? item.sale_price : item.price;
                price.textContent = money(finalPrice, item.currency);

                oldPrice.textContent = item.sale_price !== null ?
                    money(item.price, item.currency) :
                    '';

                renderVariants(item);
                renderOptionGroups(item);
            }

            function renderVariants(item) {
                variantsWrap.innerHTML = '';

                if (!item.variants || item.variants.length === 0) {
                    return;
                }

                let html = `
                <div class="option-box">
                    <div class="fw-bold mb-2">الأحجام / الاختيارات السعرية</div>
            `;

                item.variants.forEach(function(variant) {
                    const finalPrice = variant.sale_price !== null ? variant.sale_price : variant.price;

                    html += `
                    <label class="choice-row">
                        <span>
                            <input type="radio" name="variant_id" value="${variant.id}" ${variant.is_default ? 'checked' : ''}>
                            <span class="ms-1">${variant.name}</span>
                            ${variant.is_default ? '<span class="badge bg-primary ms-1">افتراضي</span>' : ''}
                        </span>

                        <strong>${money(finalPrice, variant.currency)}</strong>
                    </label>
                `;
                });

                html += `</div>`;

                variantsWrap.innerHTML = html;
            }

            function renderOptionGroups(item) {
                groupsWrap.innerHTML = '';

                if (!item.option_groups || item.option_groups.length === 0) {
                    return;
                }

                let html = '';

                item.option_groups.forEach(function(group) {
                    html += `
                    <div class="option-box">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <div>
                                <div class="fw-bold">${group.name}</div>
                                <div class="small text-muted">
                                    ${group.type === 'single' ? 'اختيار واحد' : 'اختيارات متعددة'}
                                    ${group.is_required ? ' — إجباري' : ' — اختياري'}
                                </div>
                            </div>
                        </div>
                `;

                    if (!group.options || group.options.length === 0) {
                        html += `<div class="text-muted small">لا توجد خيارات متاحة.</div>`;
                    } else {
                        group.options.forEach(function(option) {
                            const inputType = group.type === 'single' ? 'radio' : 'checkbox';
                            const inputName = group.type === 'single' ?
                                `option_group_${group.id}` :
                                `option_group_${group.id}[]`;

                            html += `
                            <label class="choice-row">
                                <span>
                                    <input
                                        type="${inputType}"
                                        name="${inputName}"
                                        value="${option.id}"
                                        data-option-group="${group.id}"
                                    >
                                    <span class="ms-1">${option.name}</span>
                                </span>

                                <span class="small">
                                    ${Number(option.price) > 0 ? '+' + money(option.price, option.currency) : 'بدون تكلفة'}
                                </span>
                            </label>
                        `;
                        });
                    }

                    html += `</div>`;
                });

                groupsWrap.innerHTML = html;
            }

            function addCurrentItemToCart() {
                if (!currentItem) {
                    return;
                }

                if (!validateSelections(currentItem)) {
                    return;
                }

                const quantity = Math.max(1, parseInt(qtyInput.value || '1', 10));
                const variant = getSelectedVariant(currentItem);
                const options = getSelectedOptions(currentItem);
                const pricing = calculateLine(currentItem, variant, options, quantity);



                cart.push({
                    key: Date.now() + '_' + Math.random().toString(16).slice(2),
                    line_type: 'item',
                    item: currentItem,
                    variant,
                    options,
                    quantity,
                    notes: notesInput.value || '',
                    unit_price: pricing.unit,
                    options_total: pricing.optionsTotal,
                    line_total: pricing.lineTotal,
                    currency: currentItem.currency
                });
     
                renderCart();
                modal.hide();
            }




            function addOfferToCart(offer) {
                if (!offer) {
                    return;
                }

                const price = Number(offer.price || 0);

                if (price <= 0) {
                    alert('هذا العرض غير قابل للطلب حاليًا.');
                    return;
                }

                const existing = cart.find(function(line) {
                    return line.line_type === 'offer' &&
                        Number(line.offer_id) === Number(offer.offer_id);
                });

                if (existing) {
                    existing.quantity = Number(existing.quantity || 1) + 1;
                    existing.line_total = Number(existing.quantity) * Number(existing.unit_price);
                } else {
                    cart.push({
                        key: Date.now() + '_' + Math.random().toString(16).slice(2),
                        line_type: 'offer',
                        offer_id: offer.offer_id,
                        offer: offer,
                        item: null,
                        variant: null,
                        options: [],
                        quantity: 1,
                        notes: '',
                        unit_price: price,
                        options_total: 0,
                        line_total: price,
                        currency: offer.currency || 'EGP'
                    });
                }

                renderCart();
            }



            function cartTotal() {
                return cart.reduce((sum, line) => sum + Number(line.line_total || 0), 0);
            }


            



            // ///////////////////////////////////////////////////////////
            function getSelectedDeliveryZone() {
    const select = document.getElementById('deliveryZoneSelect');

    if (!select || !select.value) {
        return null;
    }

    const option = select.options[select.selectedIndex];

    return {
        id: select.value,
        fee: Number(option.dataset.fee || 0),
        minOrder: option.dataset.minOrder ? Number(option.dataset.minOrder) : null,
        estimated: option.dataset.estimated || null,
        name: option.textContent.trim(),
    };
}

function getDeliveryFee() {
    const orderTypeInput = document.querySelector('[name="order_type"]:checked')
        || document.querySelector('[name="order_type"]');

    const orderType = orderTypeInput ? orderTypeInput.value : 'takeaway';

    if (orderType !== 'delivery') {
        return 0;
    }

    const config = window.deliveryConfig || {};

    if (!config.enabled) {
        return 0;
    }

    if (config.feeCalculationMode === 'free') {
        return 0;
    }

    const zone = getSelectedDeliveryZone();

    if (config.feeCalculationMode === 'zone' && zone) {
        return zone.fee;
    }

    return 0;
}
function getCartSubtotal() {
    return cart.reduce(function (sum, line) {
        return sum + Number(line.line_total || 0);
    }, 0);
}
function getCartSubtotalX() {
    return cart.reduce(function (sum, line) {
        return sum + Number(line.total || 0);
    }, 0);
}

function getFinalTotal() {
    const subtotal = getCartSubtotal();
    const deliveryFee = getDeliveryFee();
    const config = window.deliveryConfig || {};

    if (config.feeIncludedInTotal) {
        return subtotal + deliveryFee;
    }

    return subtotal;
}
            // ///////////////////////////////////////////////////////////
function syncDeliveryFields() {
    const deliveryFields = document.getElementById('deliveryFields');
    const zoneHint = document.getElementById('deliveryZoneHint');

    const orderTypeInput = document.querySelector('[name="order_type"]:checked')
        || document.querySelector('[name="order_type"]');

    const orderType = orderTypeInput ? orderTypeInput.value : 'takeaway';

    if (!deliveryFields) {
        return;
    }

    const config = window.deliveryConfig || {};

    const show = orderType === 'delivery' && config.enabled;

    deliveryFields.style.display = show ? '' : 'none';

    if (show && zoneHint) {
        const zone = getSelectedDeliveryZone();

        if (zone) {
    const currency = cart[0]?.currency || 'EGP';

    let text = 'رسوم التوصيل: ' + money(zone.fee, currency);

    if (zone.estimated) {
        text += ' · الوقت المتوقع: ' + zone.estimated + ' دقيقة';
    }

    if (zone.minOrder) {
        text += ' · الحد الأدنى: ' + money(zone.minOrder, currency);
    }

    zoneHint.textContent = text;
} else {
    zoneHint.textContent = '';
}
    }

    renderCart();
}








            function renderCart() {

    saveCart();
        if (!cartFloat || !cartItemsWrap || !cartModalTotal || !checkoutItemsInputs) {
        console.error('Cart elements missing', {
            cartFloat,
            cartItemsWrap,
            cartModalTotal,
            checkoutItemsInputs
        });
        return;
    }

                if (cart.length === 0) {
                    cartFloat.style.display = 'none';
                    cartItemsWrap.innerHTML = `<div class="text-center text-muted py-3">الطلب فارغ.</div>`;
                    cartModalTotal.textContent = money(0, 'EGP');
                    checkoutItemsInputs.innerHTML = '';
                    return;
                }

                // const currency = cart[0]?.currency || 'EGP';
                // const total = cartTotal();

                // cartFloat.style.display = 'block';
                // cartFloatTotal.textContent = money(total, currency);
                // cartModalTotal.textContent = money(total, currency);
                const currency = cart[0]?.currency || 'EGP';
const subtotal = getCartSubtotal();
const deliveryFee = getDeliveryFee();
const total = getFinalTotal();

cartFloat.style.display = 'block';
cartFloatTotal.textContent = money(total, currency);
cartModalTotal.textContent = money(total, currency);

const deliverySummary = document.getElementById('deliverySummary');

if (deliverySummary) {
    if (deliveryFee > 0) {
        deliverySummary.style.display = '';
        deliverySummary.textContent = 'رسوم التوصيل: ' + money(deliveryFee, currency);
    } else {
        deliverySummary.style.display = 'none';
        deliverySummary.textContent = '';
    }
}

       
                cartItemsWrap.innerHTML = cart.map(function(line) {
                    const isOffer = line.line_type === 'offer';

                    const titleText = isOffer ?
                        (line.offer?.title || 'Offer') :
                        (line.item?.name || 'Item');

                    const variantText = !isOffer && line.variant ?
                        ' - ' + line.variant.name :
                        '';

                    const optionsText = !isOffer && line.options && line.options.length ?
                        line.options.map(o => `${o.group_name}: ${o.name}`).join('، ') :
                        '';

                    const notesText = line.notes ?
                        `<div class="small mt-1">ملاحظة: ${escapeHtml(line.notes)}</div>` :
                        '';

                    return `
        <div class="border rounded-4 p-3 mb-2">
            <div class="d-flex justify-content-between gap-2">
                <div>
                    <div class="fw-bold">
                        ${escapeHtml(titleText)}${escapeHtml(variantText)}
                        ${isOffer ? '<span class="badge bg-success ms-1">عرض</span>' : ''}
                    </div>

                    <div class="small text-muted">
                        الكمية: ${line.quantity}
                    </div>

                    ${optionsText ? `<div class="small text-muted mt-1">${escapeHtml(optionsText)}</div>` : ''}
                    ${notesText}
                </div>

                <div class="text-end">
                    <strong>${money(line.line_total, line.currency)}</strong>

                    <button type="button" class="btn btn-sm btn-outline-danger d-block mt-2 remove-cart-line" data-key="${line.key}">
                        حذف
                    </button>
                </div>
            </div>
        </div>
    `;
                }).join('');




                renderCheckoutInputs();
            }

      
            function renderCheckoutInputs() {
                let html = '';

                cart.forEach(function(line, index) {
                    const lineType = line.line_type || 'item';

                    html += `
            <input type="hidden" name="items[${index}][line_type]" value="${lineType}">
            <input type="hidden" name="items[${index}][quantity]" value="${line.quantity}">
            <input type="hidden" name="items[${index}][notes]" value="${escapeHtml(line.notes || '')}">
        `;

                    if (lineType === 'offer') {
                        html += `
                <input type="hidden" name="items[${index}][offer_id]" value="${line.offer_id}">
            `;

                        return;
                    }

                    html += `
            <input type="hidden" name="items[${index}][item_id]" value="${line.item.id}">
        `;

                    if (line.variant) {
                        html +=
                            `<input type="hidden" name="items[${index}][variant_id]" value="${line.variant.id}">`;
                    }

                    line.options.forEach(function(option, optionIndex) {
                        html +=
                            `<input type="hidden" name="items[${index}][options][${optionIndex}]" value="${option.id}">`;
                    });
                });


                checkoutItemsInputs.innerHTML = html;
            }

            function escapeHtml(value) {
                return String(value || '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;');
            }

            function syncOrderTypeFields() {
    const type = orderType.value;

    tableNumberWrap.style.display = type === 'dine_in' ? 'block' : 'none';

    if (deliveryAddressWrap) {
        deliveryAddressWrap.style.display = 'none';
    }

    syncDeliveryFields();
}
            function syncOrderTypeFieldsX() {
                const type = orderType.value;

                tableNumberWrap.style.display = type === 'dine_in' ? 'block' : 'none';
                deliveryAddressWrap.style.display = type === 'delivery' ? 'block' : 'none';
            }

            qtyMinus?.addEventListener('click', function() {
                qtyInput.value = Math.max(1, parseInt(qtyInput.value || '1', 10) - 1);
            });

            qtyPlus?.addEventListener('click', function() {
                qtyInput.value = Math.min(100, parseInt(qtyInput.value || '1', 10) + 1);
            });

            addToCartBtn?.addEventListener('click', addCurrentItemToCart);

            cartItemsWrap?.addEventListener('click', function(event) {
                const button = event.target.closest('.remove-cart-line');

                if (!button) {
                    return;
                }

                const key = button.getAttribute('data-key');
                cart = cart.filter(line => line.key !== key);
                renderCart();
            });

       

            checkoutForm?.addEventListener('submit', function(event) {
    // cart = loadCart();

    if (cart.length === 0) {
        event.preventDefault();
        alert('يجب إضافة صنف واحد على الأقل.');
        return;
    }




    const currentOrderType = orderType ? orderType.value : 'takeaway';

if (currentOrderType === 'delivery') {
    const config = window.deliveryConfig || {};

    if (!config.enabled) {
        event.preventDefault();
        alert('الدليفري غير متاح حاليًا.');
        return;
    }

    const zoneSelect = document.getElementById('deliveryZoneSelect');

    if (config.requireZone && !zoneSelect?.value) {
        event.preventDefault();
        alert('اختر منطقة التوصيل.');
        return;
    }

    const address = document.getElementById('deliveryAddressDetails')?.value?.trim();

    if (!address) {
        event.preventDefault();
        alert('اكتب عنوان التوصيل بالتفصيل.');
        return;
    }

    const subtotal = getCartSubtotal();
    const zone = getSelectedDeliveryZone();

    if (zone && zone.minOrder && subtotal < zone.minOrder) {
        event.preventDefault();
        alert('الحد الأدنى للتوصيل لهذه المنطقة هو ' + money(zone.minOrder, cart[0]?.currency || 'EGP'));
        return;
    }
}





    renderCheckoutInputs();

    if (!checkoutItemsInputs.innerHTML.trim()) {
        event.preventDefault();
        alert('حدث خطأ في تجهيز الطلب. أعد فتح السلة وحاول مرة أخرى.');
        return;
    }

    if (window.openInvoiceEnabled && window.hasSelectedTable && !window.hasCurrentInvoice) {
        event.preventDefault();
        alert('يجب فتح فاتورة أو الانضمام لفاتورة موجودة قبل إرسال الطلب.');
        return;
    }

    saveCart();

    console.log('CHECKOUT INPUTS', checkoutItemsInputs.innerHTML);
});


            orderType?.addEventListener('change', syncOrderTypeFields);

 


            document.querySelectorAll(
                '.item-card, .item-card-large, .elegant-item, .featured-item-card, .collection-item, .offer-slide, .od-food-card, .od-featured-card, .od-collection-item, .od-offer-card, .od-offer-carousel-slide'
            ).forEach(function(card) {
                card.addEventListener('click', function(event) {
                    const offerId = card.getAttribute('data-offer-id');
                    const itemId = card.getAttribute('data-item-id');

                    if (offerId) {
                        event.preventDefault();
                        event.stopPropagation();

                        const offer = window.restaurantMenuOffers?.[offerId];

                        console.log('clicked offer', offerId, offer);

                        if (!offer) {
                            console.error('Offer not found in payload:', offerId);
                            return;
                        }

                        if (offer.order_mode === 'single_item' && itemId) {
                            const item = window.restaurantMenuItems[itemId];

                            if (item) {
                                renderItem(item);
                                modal.show();
                            }

                            return;
                        }

                        addOfferToCart(offer);
                        return;
                    }

                    if (!itemId) {
                        return;
                    }

                    const item = window.restaurantMenuItems[itemId];

                    if (!item) {
                        console.error('Item not found:', itemId);
                        return;
                    }

                    renderItem(item);
                    modal.show();
                });
            });


            document.querySelectorAll('.od-add-mini').forEach(function(button) {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation();

                    const card = button.closest('.od-food-card');

                    if (!card) {
                        return;
                    }

                    const itemId = card.getAttribute('data-item-id');

                    if (!itemId) {
                        return;
                    }

                    const item = window.restaurantMenuItems[itemId];

                    if (!item) {
                        console.error('Item not found in window.restaurantMenuItems:', itemId);
                        return;
                    }

                    renderItem(item);
                    modal.show();
                });
            });


// document.querySelectorAll('[name="order_type"]').forEach(function (input) {
//     input.addEventListener('change', syncDeliveryFields);
// });

// document.getElementById('deliveryZoneSelect')?.addEventListener('change', syncDeliveryFields);

// syncDeliveryFields();

document.getElementById('deliveryZoneSelect')?.addEventListener('change', syncDeliveryFields);


           cart = loadCart();
renderCart();
syncOrderTypeFields();

@if ($errors->any() || session('error'))
    renderCheckoutInputs();

    const cartModalElement = document.getElementById('cartModal');

    if (cartModalElement) {
        const cartModal = bootstrap.Modal.getOrCreateInstance(cartModalElement);
        cartModal.show();
    }
@endif
       
        })();
        (function() {
            const categoryLinks = document.querySelectorAll('.od-category-pill');
            const sections = Array.from(document.querySelectorAll('.od-menu-section[id^="category-"]'));

            if (!categoryLinks.length || !sections.length) {
                return;
            }

            function setActiveById(id) {
                categoryLinks.forEach(function(link) {
                    link.classList.toggle(
                        'active',
                        link.getAttribute('href') === '#' + id
                    );
                });
            }

            categoryLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    const id = link.getAttribute('href')?.replace('#', '');

                    if (id) {
                        setActiveById(id);
                    }
                });
            });

            const observer = new IntersectionObserver(function(entries) {
                const visible = entries
                    .filter(entry => entry.isIntersecting)
                    .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];

                if (visible?.target?.id) {
                    setActiveById(visible.target.id);
                }
            }, {
                root: null,
                threshold: [0.25, 0.4, 0.6],
                rootMargin: '-120px 0px -50% 0px'
            });

            sections.forEach(function(section) {
                observer.observe(section);
            });
        })();




    </script>





{{-- <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/restaurant-menu-sw.js')
                .catch(function (error) {
                    console.warn('Service worker registration failed:', error);
                });
        });
    }
</script> --}}
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/restaurant-menu-sw.js', {
                scope: '/'
            }).catch(function (error) {
                console.warn('Service worker registration failed:', error);
            });
        });
    }
</script>
<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.addEventListener('controllerchange', function () {
            if (window.__swRefreshing) {
                return;
            }

            window.__swRefreshing = true;
            window.location.reload();
        });
    }
</script>


<script>
    (function () {
        const isIos = /iphone|ipad|ipod/i.test(window.navigator.userAgent);
        const isStandalone = window.navigator.standalone === true
            || window.matchMedia('(display-mode: standalone)').matches;

        if (isIos && !isStandalone) {
            const hint = document.getElementById('iosInstallHint');

            if (hint && !localStorage.getItem('ios_pwa_hint_closed')) {
                hint.style.display = 'block';

                hint.querySelector('.od-ios-install-close')?.addEventListener('click', function () {
                    localStorage.setItem('ios_pwa_hint_closed', '1');
                });
            }
        }
    })();
</script>


{{-- @php
    $deliveryZonesPayload = collect($deliveryZones ?? [])->map(function ($zone) {
        return [
            'id' => $zone->id,
            'name' => $zone->name,
            'delivery_fee' => (float) $zone->delivery_fee,
            'min_order_amount' => $zone->min_order_amount !== null ? (float) $zone->min_order_amount : null,
            'estimated_minutes' => $zone->estimated_minutes,
        ];
    })->values();
@endphp

<script>
    window.deliveryConfig = {
        enabled: @json((bool) ($deliverySettings?->is_enabled ?? false)),
        feeCalculationMode: @json($deliverySettings?->fee_calculation_mode ?? 'zone'),
        feeIncludedInTotal: @json((bool) ($deliverySettings?->delivery_fee_included_in_total ?? true)),
        showFeeOnReceipt: @json((bool) ($deliverySettings?->show_delivery_fee_on_receipt ?? true)),
        requireZone: @json((bool) ($deliverySettings?->require_zone_for_delivery ?? true)),
        zones: @json($deliveryZonesPayload),
    };
</script> --}}

</body>

</html>
