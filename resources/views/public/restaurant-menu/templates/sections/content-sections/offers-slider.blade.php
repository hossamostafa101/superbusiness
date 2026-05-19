@php
    $offers = $contentSection->activeOffers;
@endphp

@if($offers->count())
    <section class="od-offers-section">
        <div class="od-section-head">
            <div>
                <h2>{{ $contentSection->title }}</h2>

                @if($contentSection->subtitle)
                    <p>{{ $contentSection->subtitle }}</p>
                @endif
            </div>

            <span class="od-swipe-hint">
                عروض
            </span>
        </div>

        <div class="od-offers-scroll">
            @foreach($offers as $offer)
                <article
                    class="od-offer-card"
                    style="
                        background: {{ $offer->background_color }};
                        color: {{ $offer->text_color }};
                    "
                    @if($offer->item_id)
                        data-item-id="{{ $offer->item_id }}"
                    @endif
                >
                    <div class="od-offer-content">
                        @if($offer->badge_text)
                            <span class="od-offer-badge">
                                {{ $offer->badge_text }}
                            </span>
                        @endif

                        <h3>{{ $offer->title }}</h3>

                        @if($offer->subtitle)
                            <p>{{ $offer->subtitle }}</p>
                        @endif

                        @if($offer->new_price)
                            <div class="od-offer-price">
                                <strong>
                                    {{ number_format((float) $offer->new_price, 2) }}
                                    {{ $offer->currency }}
                                </strong>

                                @if($offer->old_price)
                                    <span>
                                        {{ number_format((float) $offer->old_price, 2) }}
                                    </span>
                                @endif
                            </div>
                        @endif

                        @if($offer->button_text && $offer->button_url)
                            <a
                                href="{{ $offer->button_url }}"
                                target="_blank"
                                class="od-offer-btn"
                                onclick="event.stopPropagation();"
                            >
                                {{ $offer->button_text }}
                            </a>
                        @endif
                    </div>

                    @if($offer->imageUrl())
                        <img src="{{ $offer->imageUrl() }}" class="od-offer-image" alt="{{ $offer->title }}">
                    @elseif($offer->item?->image)
                        <img src="{{ asset('storage/' . $offer->item->image) }}" class="od-offer-image" alt="{{ $offer->title }}">
                    @endif
                </article>
            @endforeach
        </div>
    </section>
@endif