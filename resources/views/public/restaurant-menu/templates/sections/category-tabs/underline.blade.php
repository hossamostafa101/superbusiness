{{-- resources/views/public/restaurant-menu/templates/sections/category-tabs/underline.blade.php --}}
@if($branch->categories->count())
    <div class="category-tabs category-tabs-underline">
        @foreach($branch->categories as $category)
            @if($category->items->count())
                <a href="#category-{{ $category->id }}" class="category-tab-underline">
                    {{ $category->name }}
                </a>
            @endif
        @endforeach
    </div>
@endif