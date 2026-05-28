{{-- resources/views/public/restaurant-menu/templates/sections/category-tabs/ordoraa-default.blade.php --}}
@if($branch->categories->count())
    <section class="od-categories-wrap">
        <div class="od-section-head od-section-head-inline">
            <h2>{{ $tUi('categories') }}</h2>
        </div>

        <div class="od-category-tabs">
            @foreach($branch->categories as $category)
                @if($category->items->count())
                    <a
                        href="#category-{{ $category->id }}"
                        class="od-category-pill {{ $loop->first ? 'active' : '' }}"
                    >
                    @if(request()->boolean('debug_lang'))
    @php
        $debug = $debugTranslate($category, 'name', $category->name);
    @endphp

    <pre style="direction:ltr;text-align:left;background:#111;color:#0f0;font-size:11px;padding:8px;border-radius:8px;white-space:pre-wrap;">
CATEGORY TAB
class: {{ $debug['class'] }}
id: {{ $debug['id'] }}
field: {{ $debug['field'] }}
lang: {{ $debug['lang'] }}
key: {{ $debug['key'] }}
found: {{ $debug['found'] ? 'YES' : 'NO' }}
value: {{ $debug['value'] ?? 'NULL' }}
fallback: {{ $debug['fallback'] }}
final: {{ $debug['final'] }}
view: category-tabs/ordoraa-default
    </pre>
@endif
                        {{ $translate($category, 'name', $category->name) }}
                    </a>
                @endif
            @endforeach
        </div>
    </section>
@endif