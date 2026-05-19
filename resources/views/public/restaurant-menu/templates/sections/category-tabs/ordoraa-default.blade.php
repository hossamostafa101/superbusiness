{{-- resources/views/public/restaurant-menu/templates/sections/category-tabs/ordoraa-default.blade.php --}}
@if($branch->categories->count())
    <section class="od-categories-wrap">
        <div class="od-section-head od-section-head-inline">
            <h2>التصنيفات</h2>
        </div>

        <div class="od-category-tabs">
            @foreach($branch->categories as $category)
                @if($category->items->count())
                    <a
                        href="#category-{{ $category->id }}"
                        class="od-category-pill {{ $loop->first ? 'active' : '' }}"
                    >
                        {{ $category->name }}
                    </a>
                @endif
            @endforeach
        </div>
    </section>
@endif