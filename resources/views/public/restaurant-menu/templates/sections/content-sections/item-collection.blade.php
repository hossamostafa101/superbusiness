@php
    $sectionItems = $contentSection->activeSectionItems
        ->filter(fn ($row) => $row->item);
@endphp

@if($sectionItems->count())
    <section
        class="od-collection-section"
        style="
            --collection-bg: {{ $contentSection->cssBackground() }};
            --collection-text: {{ $contentSection->text_color }};
        "
    >
        <div class="od-section-head">
            <div>
                <h2>{{ $contentSection->title }}</h2>

                @if($contentSection->subtitle)
                    <p>{{ $contentSection->subtitle }}</p>
                @endif
            </div>
        </div>

        <div class="od-collection-list">
            @foreach($sectionItems as $row)
                @php
                    $item = $row->item;
                @endphp

                <article class="od-collection-item" data-item-id="{{ $item->id }}">
                    @if($item->image)
                        <img
                            src="{{ asset('storage/' . $item->image) }}"
                            class="od-collection-image"
                            alt="{{ $item->name }}"
                        >
                    @else
                        <div class="od-collection-image od-food-placeholder">
                            <i class="bi bi-image"></i>
                        </div>
                    @endif

                    <div class="od-collection-info">
                        <h3>{{ $item->name }}</h3>

                        @if($item->description)
                            <p>{{ \Illuminate\Support\Str::limit($item->description, 58) }}</p>
                        @endif

                        <strong>
                            {{ number_format((float) ($item->sale_price ?: $item->price), 2) }}
                            {{ $item->currency }}
                        </strong>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endif