@if(request()->boolean('debug_sections'))
    <pre style="direction:ltr;text-align:left;background:#111;color:#0f0;padding:12px;border-radius:12px;font-size:12px;white-space:pre-wrap;">
CONTENT SECTIONS DEBUG

contentSections count: {{ isset($contentSections) ? $contentSections->count() : 'contentSections not set' }}

@if(isset($contentSections))
@foreach($contentSections as $section)
---
id: {{ $section->id }}
type: {{ $section->type }}
title: {{ $section->title }}
branch_id: {{ $section->branch_id ?? 'NULL' }}
is_active: {{ $section->is_active ? 'YES' : 'NO' }}
starts_at: {{ $section->starts_at ?? 'NULL' }}
ends_at: {{ $section->ends_at ?? 'NULL' }}
activeSectionItems count: {{ $section->relationLoaded('activeSectionItems') ? $section->activeSectionItems->count() : 'NOT LOADED' }}
activeOffers count: {{ $section->relationLoaded('activeOffers') ? $section->activeOffers->count() : 'NOT LOADED' }}
@endforeach
@endif
    </pre>
@endif
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