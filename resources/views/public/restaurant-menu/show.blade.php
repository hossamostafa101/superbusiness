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
                'name' => $item->name,
                'description' => $item->description,
                'image' => $item->image ? asset('storage/' . $item->image) : null,
                'price' => (float) $item->price,
                'sale_price' => $item->sale_price !== null ? (float) $item->sale_price : null,
                'currency' => $item->currency,
                'calories' => $item->calories,
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
@endphp

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منيو {{ $workspace->name }} - {{ $branch->name }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

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
        radial-gradient(circle at 15% 20%, rgba(255,255,255,.28), transparent 28%),
        radial-gradient(circle at 90% 10%, rgba(255,255,255,.18), transparent 22%);
}

.hero-eyebrow {
    display: inline-flex;
    border: 1px solid rgba(255,255,255,.35);
    border-radius: 999px;
    padding: 5px 11px;
    font-size: 12px;
    color: rgba(255,255,255,.9);
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
    background: rgba(255,255,255,.95);
    color: #16a34a;
    text-decoration: none;
    font-size: 22px;
}

.hero-luxury {
    background:
        linear-gradient(135deg, #111827, #27272a);
    border: 1px solid rgba(255,255,255,.12);
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
    background: rgba(255,255,255,.6);
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
    box-shadow: 0 0 0 4px rgba(37,99,235,.10);
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
    box-shadow: 0 10px 28px rgba(15,23,42,.04);
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
    background: rgba(255,255,255,.18);
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
    background: rgba(255,255,255,.92);
    color: #111827;
    border-radius: 22px;
    overflow: hidden;
    cursor: pointer;
    scroll-snap-align: start;
    border: 1px solid rgba(229,231,235,.75);
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
    background: rgba(255,255,255,.92);
    color: #111827;
    border: 1px solid rgba(229,231,235,.75);
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
    background: rgba(255,255,255,.18);
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
    border: 6px solid rgba(255,255,255,.18);
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
        linear-gradient(135deg, rgba(17,24,39,.2), rgba(37,99,235,.12)),
        radial-gradient(circle at 20% 20%, rgba(255,255,255,.5), transparent 25%),
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
    height: 142px;
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
    background: linear-gradient(to top, rgba(246,247,251,.98), rgba(246,247,251,.72), transparent);
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
    background: rgba(255,255,255,.16);
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
    border: 1px solid rgba(229,231,235,.8);
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
    background: rgba(255,255,255,.88);
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
    background: rgba(255,255,255,.18);
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
    background: rgba(255,255,255,.95);
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
    border: 7px solid rgba(255,255,255,.18);
}

@media (max-width: 420px) {
    .od-featured-card {
        min-width: 215px;
        max-width: 215px;
    }

    .od-featured-image {
        height: 165px;
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

.od-hero + .od-branch-switch + .alert,
.menu-wrapper > .alert {
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
    </style>
     @if(!empty($menuTheme['custom_css']))
    {!! $menuTheme['custom_css'] !!}
@endif
</head>

<body>

   <div class="menu-wrapper">
    {{-- @if(request()->filled('preview_template') || request()->boolean('theme_preview'))
    <div class="alert alert-warning rounded-4">
        أنت تشاهد معاينة مؤقتة للتصميم، ولم يتم حفظ هذه التغييرات بعد.
    </div>
@endif --}}

@if(request()->filled('preview_template') || request()->boolean('theme_preview') || request()->boolean('content_section_preview'))
    <div class="alert alert-warning rounded-4">
        أنت تشاهد معاينة مؤقتة، ولم يتم حفظ هذه التغييرات بعد.
    </div>
@endif

    @include($menuTheme['views']['hero'])

    @include($menuTheme['views']['branch_switch'])

    @include($menuTheme['views']['invoice'])

    @include('public.restaurant-menu.templates.sections.content-sections.index')

    @include($menuTheme['views']['category_tabs'])

    @include($menuTheme['views']['items'])

    @include($menuTheme['views']['footer'])
</div>

    {{-- <div class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div id="modalImageWrap"></div>

                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between gap-3 align-items-start mb-2">
                        <div>
                            <h3 class="h5 fw-bold mb-1" id="modalTitle"></h3>
                            <p class="text-muted small mb-0" id="modalDescription"></p>
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="d-flex gap-2 flex-wrap small text-muted mb-3" id="modalMeta"></div>

                    <div class="border rounded-4 p-3 mb-3">
                        <div class="small text-muted">السعر</div>
                        <div class="h5 fw-bold mb-0" id="modalPrice"></div>
                        <div class="old-price" id="modalOldPrice"></div>
                    </div>

                    <div id="modalVariants"></div>
                    <div id="modalOptionGroups"></div>

                    <div class="mb-3">
                        <label class="form-label">الكمية</label>
                        <div class="d-flex gap-2 align-items-center">
                            <button type="button" class="btn btn-light border" id="qtyMinus">-</button>
                            <input type="number" id="modalQty" value="1" min="1" max="100"
                                class="form-control text-center" style="max-width: 90px;">
                            <button type="button" class="btn btn-light border" id="qtyPlus">+</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ملاحظات على الصنف</label>
                        <textarea id="modalItemNotes" class="form-control" rows="2" placeholder="مثال: بدون بصل، زيادة صوص"></textarea>
                    </div>

                    <button type="button" class="btn btn-main w-100 mt-2" id="addToCartBtn">
                        إضافة إلى الطلب
                    </button>
                </div>
            </div>
        </div>
    </div> --}}

    @include($menuTheme['views']['cart'])
@include($menuTheme['views']['item_modal'])

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

                    <div class="border rounded-4 p-3 mb-3">
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


                            {{-- <select name="order_type" id="orderType" class="form-select" required>
                                <option value="takeaway" @selected(old('order_type') === 'takeaway' && empty($selectedTable))>تيك أواي</option>
                                <option value="dine_in" @selected(old('order_type', !empty($selectedTable) ? 'dine_in' : null) === 'dine_in')>داخل المكان</option>
                                <option value="delivery" @selected(old('order_type') === 'delivery')>دليفري</option>
                            </select> --}}
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




    {{-- <div class="cart-float" id="cartFloat" style="display:none;">
        <button type="button" class="cart-button" data-bs-toggle="modal" data-bs-target="#cartModal">
            <span>
                <i class="bi bi-bag"></i>
                الطلب
            </span>

            <strong id="cartFloatTotal">0.00</strong>
        </button>
    </div> --}}


    @include($menuTheme['views']['cart'])
    <script>
        window.restaurantMenuItems = @json($itemsPayload);
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
            let cart = [];

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

            function cartTotal() {
                return cart.reduce((sum, line) => sum + Number(line.line_total || 0), 0);
            }

            function renderCart() {
                if (cart.length === 0) {
                    cartFloat.style.display = 'none';
                    cartItemsWrap.innerHTML = `<div class="text-center text-muted py-3">الطلب فارغ.</div>`;
                    cartModalTotal.textContent = money(0, 'EGP');
                    checkoutItemsInputs.innerHTML = '';
                    return;
                }

                const currency = cart[0]?.currency || 'EGP';
                const total = cartTotal();

                cartFloat.style.display = 'block';
                cartFloatTotal.textContent = money(total, currency);
                cartModalTotal.textContent = money(total, currency);

                cartItemsWrap.innerHTML = cart.map(function(line) {
                    const optionsText = line.options.length ?
                        line.options.map(o => `${o.group_name}: ${o.name}`).join('، ') :
                        '';

                    return `
                    <div class="border rounded-4 p-3 mb-2">
                        <div class="d-flex justify-content-between gap-2">
                            <div>
                                <div class="fw-bold">
                                    ${line.item.name}
                                    ${line.variant ? ' - ' + line.variant.name : ''}
                                </div>

                                <div class="small text-muted">
                                    الكمية: ${line.quantity}
                                </div>

                                ${optionsText ? `<div class="small text-muted mt-1">${optionsText}</div>` : ''}
                                ${line.notes ? `<div class="small mt-1">ملاحظة: ${line.notes}</div>` : ''}
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
                    html += `
                    <input type="hidden" name="items[${index}][item_id]" value="${line.item.id}">
                    <input type="hidden" name="items[${index}][quantity]" value="${line.quantity}">
                    <input type="hidden" name="items[${index}][notes]" value="${escapeHtml(line.notes)}">
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

            // checkoutForm?.addEventListener('submit', function(event) {
            //     if (cart.length === 0) {
            //         event.preventDefault();
            //         alert('يجب إضافة صنف واحد على الأقل.');
            //     }
            // });

            checkoutForm?.addEventListener('submit', function (event) {
    if (cart.length === 0) {
        event.preventDefault();
        alert('يجب إضافة صنف واحد على الأقل.');
        return;
    }

    if (window.openInvoiceEnabled && window.hasSelectedTable && !window.hasCurrentInvoice) {
        event.preventDefault();
        alert('يجب فتح فاتورة أو الانضمام لفاتورة موجودة قبل إرسال الطلب.');
    }
});

            orderType?.addEventListener('change', syncOrderTypeFields);

            // document.querySelectorAll('.item-card, .item-card-large, .elegant-item, .featured-item-card, .collection-item, .offer-slide').forEach(function (card) {
            //     card.addEventListener('click', function() {
            //         const itemId = card.getAttribute('data-item-id');
            //         const item = window.restaurantMenuItems[itemId];

            //         if (!item) {
            //             return;
            //         }

            //         renderItem(item);
            //         modal.show();
            //     });
            // });

//             document.querySelectorAll('.item-card, .item-card-large, .elegant-item, .featured-item-card, .collection-item, .offer-slide, .od-food-card, .od-featured-card, .od-collection-item, .od-offer-card').forEach(function (card) {
//     card.addEventListener('click', function () {
//         const itemId = card.getAttribute('data-item-id');

//         if (!itemId) {
//             return;
//         }

//         openItemModal(itemId);
//     });
// });


document.querySelectorAll(
    '.item-card, .item-card-large, .elegant-item, .featured-item-card, .collection-item, .offer-slide, .od-food-card, .od-featured-card, .od-collection-item, .od-offer-card'
).forEach(function (card) {
    card.addEventListener('click', function () {
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


document.querySelectorAll('.od-add-mini').forEach(function (button) {
    button.addEventListener('click', function (event) {
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



            renderCart();
            syncOrderTypeFields();

            @if ($errors->any() || session('error'))
                const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
                cartModal.show();
            @endif
        })();
        (function () {
    const categoryLinks = document.querySelectorAll('.od-category-pill');
    const sections = Array.from(document.querySelectorAll('.od-menu-section[id^="category-"]'));

    if (!categoryLinks.length || !sections.length) {
        return;
    }

    function setActiveById(id) {
        categoryLinks.forEach(function (link) {
            link.classList.toggle(
                'active',
                link.getAttribute('href') === '#' + id
            );
        });
    }

    categoryLinks.forEach(function (link) {
        link.addEventListener('click', function () {
            const id = link.getAttribute('href')?.replace('#', '');

            if (id) {
                setActiveById(id);
            }
        });
    });

    const observer = new IntersectionObserver(function (entries) {
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

    sections.forEach(function (section) {
        observer.observe(section);
    });
})();
    </script>

</body>

</html>
