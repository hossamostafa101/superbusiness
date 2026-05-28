{{-- resources/views/public/restaurant-menu/templates/sections/items/ordoraa-grid.blade.php --}}
@forelse($branch->categories as $category)
    @if($category->items->count())
        <section class="od-menu-section" id="category-{{ $category->id }}">
            <div class="od-section-head">
                <div>
                    <h2>{{ $category->name }}</h2>

                    @if($category->description)
                        <p>{{ $category->description }}</p>
                    @endif
                </div>

                <span class="od-items-count">
                    {{ $category->items->count() }} أصناف
                </span>
            </div>

            <div class="od-items-grid">
                @foreach($category->items as $item)
                    <article class="od-food-card" data-item-id="{{ $item->id }}">
                        <div class="od-food-image-wrap">
                            @if($item->image)
                                <img
                                    src="{{ asset('storage/' . $item->image) }}"
                                    class="od-food-image"
                                    alt="{{ $item->name }}"
                                >
                            @else
                                <div class="od-food-image od-food-placeholder">
                                    <i class="bi bi-image"></i>
                                </div>
                            @endif

                            @if($item->is_featured)
                                <span class="od-food-badge">
                                    مميز
                                </span>
                            @endif
                        </div>

                        <div class="od-food-body">
                            {{-- <h3>{{ $item->name }}</h3> --}}
                            <h3>{{ $item->translated('name', $currentLanguageCode ?? app()->getLocale()) }}</h3>

                            @if($item->description)
                                <p>
                                    {{ \Illuminate\Support\Str::limit($item->description, 55) }}
                                </p>
                            @endif

                            <div class="od-food-bottom">
                                <div>
                                    @if($item->sale_price)
                                        <div class="od-price od-price-sale">
                                            {{ number_format((float) $item->sale_price, 2) }}
                                            {{ $item->currency }}
                                        </div>

                                        <div class="od-old-price">
                                            {{ number_format((float) $item->price, 2) }}
                                            {{ $item->currency }}
                                        </div>
                                    @else
                                        <div class="od-price">
                                            {{ number_format((float) $item->price, 2) }}
                                            {{ $item->currency }}
                                        </div>
                                    @endif
                                </div>

                                <button type="button" class="od-add-mini" aria-label="إضافة">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
@empty
    <section class="od-empty-section">
        لا توجد أصناف في هذا الفرع بعد.
    </section>
@endforelse