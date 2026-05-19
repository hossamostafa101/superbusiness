@forelse($branch->categories as $category)
    @if($category->items->count())
        <section class="category-card" id="category-{{ $category->id }}">
            <div class="mb-3">
                <h2 class="h5 fw-bold mb-1">{{ $category->name }}</h2>

                @if($category->description)
                    <p class="text-muted small mb-0">{{ $category->description }}</p>
                @endif
            </div>

            @foreach($category->items as $item)
                <div class="item-card d-flex gap-3" data-item-id="{{ $item->id }}">
                    @if($item->image)
                        <img src="{{ asset('storage/' . $item->image) }}" class="item-image" alt="{{ $item->name }}">
                    @else
                        <div class="item-image d-flex align-items-center justify-content-center text-muted">
                            <i class="bi bi-image"></i>
                        </div>
                    @endif

                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between gap-2 align-items-start">
                            <div>
                                <div class="item-title">
                                    {{ $item->name }}
                                </div>

                                <div class="d-flex gap-1 flex-wrap mt-1">
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
                            <div class="item-desc mt-2">
                                {{ \Illuminate\Support\Str::limit($item->description, 115) }}
                            </div>
                        @endif

                        <div class="small text-muted mt-2">
                            @if($item->calories)
                                {{ $item->calories }} كالوري
                            @endif

                            @if($item->preparation_time_minutes)
                                @if($item->calories) — @endif
                                {{ $item->preparation_time_minutes }} دقيقة
                            @endif
                        </div>
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