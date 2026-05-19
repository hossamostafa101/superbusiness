{{-- resources/views/public/restaurant-menu/templates/sections/heroes/luxury.blade.php --}}
<div class="hero hero-luxury">
    <div class="text-center">
        <div class="luxury-label mb-2">
            Fine Dining Menu
        </div>

        <h1 class="luxury-title mb-2">
            {{ $workspace->name }}
        </h1>

        <div class="luxury-line mx-auto mb-3"></div>

        <div class="opacity-75">
            <i class="bi bi-shop"></i>
            {{ $branch->name }}
        </div>

        @if($branch->address)
            <div class="mt-2 small opacity-75">
                <i class="bi bi-geo-alt"></i>
                {{ $branch->address }}
            </div>
        @endif

        @if($branch->whatsapp_number)
            @php
                $wa = preg_replace('/\D+/', '', $branch->whatsapp_number);
            @endphp

            <a href="https://wa.me/{{ $wa }}" target="_blank" class="btn btn-light rounded-pill mt-3">
                <i class="bi bi-whatsapp text-success"></i>
                واتساب
            </a>
        @endif
    </div>
</div>