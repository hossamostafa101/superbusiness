@if(!empty($contentSections) && $contentSections->count())
    @foreach($contentSections as $contentSection)
        @if($contentSection->type === 'featured_items')
            @include('public.restaurant-menu.templates.sections.content-sections.featured-items', [
                'contentSection' => $contentSection,
            ])
        @elseif($contentSection->type === 'item_collection')
            @include('public.restaurant-menu.templates.sections.content-sections.item-collection', [
                'contentSection' => $contentSection,
            ])
        @elseif($contentSection->type === 'offers_slider')
            @include('public.restaurant-menu.templates.sections.content-sections.offers-slider', [
                'contentSection' => $contentSection,
            ])
        @endif
    @endforeach
@endif