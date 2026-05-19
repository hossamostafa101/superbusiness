{{-- resources/views/public/menu.blade.php --}}
@extends('layouts.app')

@section('title', $restaurant['name'].' - المنيو')

@section('content')

    {{-- Header Section --}}
    @includeIf(
        'public.menu.sections.header.' . ($styles['header'] ?? 'classic'),
        [
            'restaurant' => $restaurant,
            'preview'    => $preview ?? false,
        ]
    )

    {{-- Categories Section --}}
    @includeIf(
        'public.menu.sections.categories.' . ($styles['categories'] ?? 'pills'),
        [
            'categories' => $categories,
        ]
    )

    {{-- Items Section --}}
    @includeIf(
        'public.menu.sections.items.' . ($styles['items'] ?? 'card_right_image'),
        [
            'items' => $items,
        ]
    )

    {{-- Cart + Bottom Bar Section --}}
    @includeIf(
        'public.menu.sections.cart.' . ($styles['cart_bar'] ?? 'floating_glass'),
        [
            'restaurant' => $restaurant,
            'preview'    => $preview ?? false,
        ]
    )

    {{-- Modal تفاصيل الصنف --}}
    @include('public.menu.sections.partials.item_modal')

@endsection

@push('scripts')
    {{-- في وضع المعاينة (preview) مش هنحمّل السكربت عشان مايبقاش فيه functionality --}}
    @includeWhen(empty($preview), 'public.menu.sections.partials.scripts')
@endpush
