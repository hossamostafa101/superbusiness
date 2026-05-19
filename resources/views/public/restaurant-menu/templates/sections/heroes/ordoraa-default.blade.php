{{-- resources/views/public/restaurant-menu/templates/sections/heroes/ordoraa-default.blade.php --}}
@php
    $profile = $workspace->businessProfile;

    $displayName = $profile?->display_name ?: $workspace->name;

    $coverImage = $profile?->cover_image ?? null;
    $logoImage = $profile?->logo ?? null;

    $coverUrl = $coverImage
        ? (str_starts_with($coverImage, 'http') ? $coverImage : asset('storage/' . $coverImage))
        : null;

    $logoUrl = $logoImage
        ? (str_starts_with($logoImage, 'http') ? $logoImage : asset('storage/' . $logoImage))
        : null;

    $whatsapp = $branch->whatsapp_number ?: ($profile?->whatsapp_number ?? null);
    $wa = $whatsapp ? preg_replace('/\D+/', '', $whatsapp) : null;

    $phone = $branch->phone ?: ($profile?->phone ?? null);

    $mapsUrl = $branch->google_maps_url ?? null;



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


@endphp

<section class="od-hero">
    {{-- <div
        class="od-hero-cover"
        @if($coverUrl)
            style="background-image: url('{{ $coverUrl }}');"
        @endif
    >
        @unless($coverUrl)
            <div class="od-hero-cover-fallback"></div>
        @endunless
    </div> --}}

     <div class="cover">
            @if($coverUrl)
                <img src="{{ $coverUrl }}" alt="{{ $displayName }}">
            @endif
        </div>

    <div class="od-hero-body">
        <div class="od-logo-wrap">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" class="od-logo" alt="{{ $displayName }}">
            @else
                <div class="od-logo od-logo-placeholder">
                    {{ mb_substr($displayName, 0, 1) }}
                </div>
            @endif
        </div>

        <h1 class="od-title">
            {{ $displayName }}
        </h1>

        <div class="od-subtitle">
            {{ $branch->name }}
            @if($branch->address)
                <span>•</span>
                {{ \Illuminate\Support\Str::limit($branch->address, 42) }}
            @endif
        </div>

        


        @if(!empty($links) && $links->count())
    <div class="od-social-row" aria-label="روابط مهمة">
        @foreach($links as $link)
            <a
                href="{{ route('public.business-page.track.link', [$workspace, $link]) }}"
                target="_blank"
                rel="noopener"
                class="od-social-icon"
                aria-label="{{ $link->title }}"
                title="{{ $link->title }}"
            >
                <i class="bi {{ business_link_icon($link->icon) }}"></i>
            </a>
        @endforeach
    </div>
@endif


@if(!empty($selectedTable))
    <div class="od-table-actions">
        <form method="POST" action="{{ route('public.restaurant-menu.service-request.store', [$workspace, $branch]) }}">
            @csrf

            <input type="hidden" name="type" value="waiter">
            <input type="hidden" name="table_id" value="{{ $selectedTable->id }}">

            <button type="submit" class="od-table-action-btn">
                <i class="bi bi-bell"></i>
                طلب الجرسون
            </button>
        </form>

        <form method="POST" action="{{ route('public.restaurant-menu.service-request.store', [$workspace, $branch]) }}">
            @csrf

            <input type="hidden" name="type" value="cash">
            <input type="hidden" name="table_id" value="{{ $selectedTable->id }}">

            <button type="submit" class="od-table-action-btn">
                <i class="bi bi-receipt"></i>
                طلب الحساب
            </button>
        </form>
    </div>
@endif

        {{-- <div class="od-actions-main">
            @if($wa)
                <a href="https://wa.me/{{ $wa }}" target="_blank" class="od-main-btn">
                    <i class="bi bi-whatsapp"></i>
                    تواصل عبر واتساب
                </a>
            @endif
        </div> --}}

        {{-- <div class="od-actions-secondary">
            @if($mapsUrl)
                <a href="{{ $mapsUrl }}" target="_blank" class="od-secondary-btn">
                    <i class="bi bi-geo-alt"></i>
                    الموقع
                </a>
            @endif

            @if($phone)
                <a href="tel:{{ $phone }}" class="od-secondary-btn">
                    <i class="bi bi-telephone"></i>
                    اتصال
                </a>
            @endif
        </div> --}}
    </div>
</section>