{{-- resources/views/public/restaurant-menu/templates/sections/items/cards-large.blade.php --}}
@forelse($branch->categories as $category)
    @if($category->items->count())
        <section class="category-card" id="category-{{ $category->id }}">
            <div class="mb-3">
                <h2 class="h5 fw-bold mb-1">{{ $category->name }}</h2>

                @if($category->description)
                    <p class="text-muted small mb-0">{{ $category->description }}</p>
                @endif
            </div>

            <div class="items-grid-large">
                @foreach($category->items as $item)
                    <div class="item-card-large" data-item-id="{{ $item->id }}">
                        <div class="item-card-large-image-wrap">
                            @if($item->image)
                                <img
                                    src="{{ asset('storage/' . $item->image) }}"
                                    class="item-card-large-image"
                                    alt="{{ $item->name }}"
                                >
                            @else
                                <div class="item-card-large-image item-card-large-placeholder">
                                    <i class="bi bi-image"></i>
                                </div>
                            @endif

                            @if($item->is_featured)
                                <span class="item-card-large-featured">
                                    مميز
                                </span>
                            @endif
                        </div>

                        <div class="p-3">
                            <div class="d-flex justify-content-between gap-2 align-items-start mb-2">
                                <div class="fw-bold">
                                    {{ $item->name }}
                                </div>

                                <div class="text-end">
                                    @if($item->sale_price)
                                        <div class="price text-success">
                                            {{ number_format((float) $item->sale_price, 2) }}
                                            {{ $item->currency }}
                                        </div>

                                        <div class="old-price">
                                            {{ number_format((float) $item->price, 2) }}
                                            {{ $item->currency }}
                                        </div>
                                    @else
                                        <div class="price">
                                            {{ number_format((float) $item->price, 2) }}
                                            {{ $item->currency }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if($item->description)
                                <div class="item-desc">
                                    {{ \Illuminate\Support\Str::limit($item->description, 90) }}
                                </div>
                            @endif

                            <div class="d-flex gap-1 flex-wrap mt-3">
                                @if($item->activeVariants->count())
                                    <span class="tag">أحجام</span>
                                @endif

                                @if($item->activeOptionGroups->count())
                                    <span class="tag">إضافات</span>
                                @endif

                                @if($item->preparation_time_minutes)
                                    <span class="tag">
                                        {{ $item->preparation_time_minutes }} دقيقة
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
@empty
    <div class="category-card text-center text-muted">
        لا توجد تصنيفات في هذا الفرع بعد.
    </div>
@endforelse