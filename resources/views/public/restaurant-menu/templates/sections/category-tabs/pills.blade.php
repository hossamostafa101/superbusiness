@if($branch->categories->count())
    <div class="category-tabs">
        @foreach($branch->categories as $category)
            @if($category->items->count())
                <a href="#category-{{ $category->id }}" class="category-tab">
                    {{ $category->name }}
                </a>
            @endif
        @endforeach
    </div>
@endif