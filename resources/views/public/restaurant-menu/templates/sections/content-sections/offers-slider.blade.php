@php
    $offers = $contentSection->activeOffers;

    $sectionTitle = isset($translate)
        ? $translate($contentSection, 'title', $contentSection->title)
        : $contentSection->title;

    $sectionSubtitle = isset($translate)
        ? $translate($contentSection, 'subtitle', $contentSection->subtitle)
        : $contentSection->subtitle;

    $carouselId = 'offersCarousel_' . $contentSection->id;
@endphp

@if($offers->count())
    <section class="od-offers-carousel-section">
        <div class="od-section-head">
            <div>
                <h2>{{ $sectionTitle }}</h2>

                @if($sectionSubtitle)
                    <p>{{ $sectionSubtitle }}</p>
                @endif
            </div>
        </div>

        <div
            id="{{ $carouselId }}"
            class="carousel slide od-offers-carousel"
            data-bs-ride="carousel"
            data-bs-interval="4200"
            data-bs-touch="true"
        >
            @if($offers->count() > 1)
                <div class="carousel-indicators od-offers-indicators">
                    @foreach($offers as $offer)
                        <button
                            type="button"
                            data-bs-target="#{{ $carouselId }}"
                            data-bs-slide-to="{{ $loop->index }}"
                            class="{{ $loop->first ? 'active' : '' }}"
                            aria-current="{{ $loop->first ? 'true' : 'false' }}"
                            aria-label="Slide {{ $loop->iteration }}"
                        ></button>
                    @endforeach
                </div>
            @endif

            <div class="carousel-inner">
                @foreach($offers as $offer)
                    @php
                        $title = isset($translate)
                            ? $translate($offer, 'title', $offer->title)
                            : $offer->title;

                        $subtitle = isset($translate)
                            ? $translate($offer, 'subtitle', $offer->subtitle)
                            : $offer->subtitle;

                        $description = isset($translate)
                            ? $translate($offer, 'description', $offer->description)
                            : $offer->description;

                        $badgeText = isset($translate)
                            ? $translate($offer, 'badge_text', $offer->badge_text)
                            : $offer->badge_text;

                        $buttonText = isset($translate)
                            ? $translate($offer, 'button_text', $offer->button_text)
                            : $offer->button_text;

                        $imageUrl = $offer->imageUrl();

                        if (! $imageUrl && $offer->item?->image) {
                            $imageUrl = asset('storage/' . $offer->item->image);
                        }

                        $hasPrice = filled($offer->new_price);
                    @endphp

                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                        {{-- <article
                            class="od-offer-carousel-slide"
                            @if($offer->item_id)
                                data-item-id="{{ $offer->item_id }}"
                            @endif
                        > --}}
                        <article
    class="od-offer-carousel-slide"
    data-offer-id="{{ $offer->id }}"
    @if($offer->order_mode === 'single_item' && $offer->item_id)
        data-item-id="{{ $offer->item_id }}"
    @endif
>

                            @if($imageUrl)
                                <img
                                    src="{{ $imageUrl }}"
                                    class="od-offer-bg-image"
                                    alt="{{ $title }}"
                                >
                            @endif

                            <div
                                class="od-offer-bg-fallback"
                                style="background: {{ $offer->background_color ?: '#2a120d' }};"
                            ></div>

                            <div class="od-offer-bg-overlay"></div>

                            <div class="od-offer-carousel-content">
                                @if($badgeText)
                                    <span class="od-offer-carousel-badge">
                                        {{ $badgeText }}
                                    </span>
                                @endif

                                <h3>
                                    {{ $title }}
                                </h3>

                                @if($subtitle)
                                    <div class="od-offer-carousel-subtitle">
                                        {{ $subtitle }}
                                    </div>
                                @endif

                                @if($description)
                                    <p>
                                        {{ \Illuminate\Support\Str::limit($description, 105) }}
                                    </p>
                                @endif

                                @if($hasPrice)
                                    <div class="od-offer-carousel-price">
                                        <strong>
                                            {{ number_format((float) $offer->new_price, 2) }}
                                            {{ $offer->currency }}
                                        </strong>

                                        @if($offer->old_price)
                                            <span>
                                                {{ number_format((float) $offer->old_price, 2) }}
                                                {{ $offer->currency }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                @if($buttonText && $offer->button_url)
                                    <a
                                        href="{{ $offer->button_url }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="od-offer-carousel-btn"
                                        onclick="event.stopPropagation();"
                                    >
                                        {{ $buttonText }}
                                    </a>
                                @endif
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif