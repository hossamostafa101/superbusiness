{{-- resources/views/public/restaurant-menu/templates/sections/items/elegant.blade.php --}}
@forelse($branch->categories as $category)
    @if($category->items->count())
        <section class="category-card category-elegant" id="category-{{ $category->id }}">
            <div class="text-center mb-4">
                <div class="luxury-label text-dark mb-2">
                    Menu Section
                </div>

                <h2 class="h4 fw-bold mb-2">{{ $category->name }}</h2>

                @if($category->description)
                    <p class="text-muted small mb-0">{{ $category->description }}</p>
                @endif
            </div>

            @foreach($category->items as $item)
                <div class="elegant-item" data-item-id="{{ $item->id }}">
                    <div class="elegant-item-main">
                        <div>
                            <div class="elegant-item-title">
                                {{ $item->name }}
                            </div>

                            @if($item->description)
                                <div class="elegant-item-desc">
                                    {{ \Illuminate\Support\Str::limit($item->description, 130) }}
                                </div>
                            @endif
                        </div>

                        <div class="elegant-dots"></div>

                        <div class="elegant-price">
                            @if($item->sale_price)
                                <div class="text-success">
                                    {{ number_format((float) $item->sale_price, 2) }}
                                    {{ $item->currency }}
                                </div>

                                <div class="old-price">
                                    {{ number_format((float) $item->price, 2) }}
                                    {{ $item->currency }}
                                </div>
                            @else
                                {{ number_format((float) $item->price, 2) }}
                                {{ $item->currency }}
                            @endif
                        </div>
                    </div>

                    <div class="d-flex gap-1 flex-wrap mt-2">
                        @if($item->is_featured)
                            <span class="tag">مميز</span>
                        @endif

                        @if($item->activeVariants->count())
                            <span class="tag">أحجام</span>
                        @endif

                        @if($item->activeOptionGroups->count())
                            <span class="tag">إضافات</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </section>
    @endif
@empty
    <div class="category-card text-center text-muted">
        لا توجد تصنيفات في هذا الفرع بعد.
    </div>
@endforelse