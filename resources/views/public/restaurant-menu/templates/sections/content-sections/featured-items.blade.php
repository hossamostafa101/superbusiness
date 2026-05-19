@php
    $sectionItems = $contentSection->activeSectionItems
        ->filter(fn ($row) => $row->item);
@endphp

@if($sectionItems->count())
    <section class="od-featured-section">
        <div class="od-section-head">
            <div>
                <h2>{{ $contentSection->title }}</h2>

                @if($contentSection->subtitle)
                    <p>{{ $contentSection->subtitle }}</p>
                @endif
            </div>

            <span class="od-swipe-hint">
                اسحب ←
            </span>
        </div>

        <div class="od-featured-scroll">
            @foreach($sectionItems as $row)
                @php
                    $item = $row->item;
                @endphp

                <article class="od-featured-card" data-item-id="{{ $item->id }}">
                    <div class="od-featured-image-wrap">
                        @if($item->image)
                            <img
                                src="{{ asset('storage/' . $item->image) }}"
                                class="od-featured-image"
                                alt="{{ $item->name }}"
                            >
                        @else
                            <div class="od-featured-image od-food-placeholder">
                                <i class="bi bi-image"></i>
                            </div>
                        @endif

                        <span class="od-featured-badge">
                            مميز
                        </span>
                    </div>

                    <div class="od-featured-body">
                        <h3>{{ $item->name }}</h3>

                        <div class="od-featured-price">
                            {{ number_format((float) ($item->sale_price ?: $item->price), 2) }}
                            {{ $item->currency }}
                        </div>

                        @if($item->sale_price)
                            <div class="od-old-price">
                                {{ number_format((float) $item->price, 2) }}
                                {{ $item->currency }}
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endif