{{-- resources/views/public/restaurant-menu/templates/sections/heroes/modern-gradient.blade.php --}}
<div class="hero hero-modern">
    <div class="hero-modern-bg"></div>

    <div class="position-relative">
        <div class="d-flex justify-content-between align-items-start gap-3">
            <div>
                <div class="hero-eyebrow mb-2">
                    Digital Menu
                </div>

                <h1 class="hero-modern-title mb-2">
                    {{ $workspace->name }}
                </h1>

                <div class="hero-modern-branch">
                    <i class="bi bi-shop"></i>
                    {{ $branch->name }}
                </div>

                @if($branch->address)
                    <div class="mt-2 small opacity-75">
                        <i class="bi bi-geo-alt"></i>
                        {{ $branch->address }}
                    </div>
                @endif
            </div>

            @if($branch->whatsapp_number)
                @php
                    $wa = preg_replace('/\D+/', '', $branch->whatsapp_number);
                @endphp

                <a href="https://wa.me/{{ $wa }}" target="_blank" class="hero-whatsapp">
                    <i class="bi bi-whatsapp"></i>
                </a>
            @endif
        </div>
    </div>
</div>