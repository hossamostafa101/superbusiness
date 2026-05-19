{{-- resources/views/public/business-page/partials/product-card.blade.php --}}
@php
    $price = $product->sale_price ?: $product->price;

    // $productWhatsappUrl = null;

    // if ($whatsappNumber) {
    //     $message = "مرحبًا، أريد الاستفسار عن المنتج: {$product->name}";

    //     if ($price) {
    //         $message .= " - السعر: {$price} {$product->currency}";
    //     }

    //     $productWhatsappUrl = "https://wa.me/{$whatsappNumber}?text=" . urlencode($message);
    // }

       $productWhatsappUrl = $whatsappNumber
        ? route('public.business-page.track.product-whatsapp', [$product->workspace, $product])
        : null;
@endphp

<div class="product-card">
    @if($product->image)
        <img
            src="{{ asset('storage/' . $product->image) }}"
            alt="{{ $product->name }}"
            class="product-image"
        >
    @else
        <div class="product-placeholder">
            <i class="bi bi-image"></i>
        </div>
    @endif

    <div class="p-3">
        <div class="product-title">{{ $product->name }}</div>

        @if($product->description)
            <div class="product-description mb-3">
                {{ $product->description }}
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center gap-2">
            <div>
                @if($product->sale_price)
                    <div class="price">
                        {{ number_format((float) $product->sale_price, 2) }}
                        {{ $product->currency }}
                    </div>

                    @if($product->price)
                        <div class="old-price">
                            {{ number_format((float) $product->price, 2) }}
                            {{ $product->currency }}
                        </div>
                    @endif
                @elseif($product->price)
                    <div class="price">
                        {{ number_format((float) $product->price, 2) }}
                        {{ $product->currency }}
                    </div>
                @else
                    <div class="text-muted small">
                        السعر عند التواصل
                    </div>
                @endif
            </div>

            @if($productWhatsappUrl)
                <a href="{{ $productWhatsappUrl }}" target="_blank" class="main-button py-2 px-3">
                    <i class="bi bi-whatsapp"></i>
                    اطلب
                </a>
            @endif
        </div>
    </div>
</div>