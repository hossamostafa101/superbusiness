<div class="hero">
    <div class="d-flex justify-content-between align-items-start gap-3">
        <div>
            <h1 class="h3 fw-bold mb-2">{{ $workspace->name }}</h1>

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
        </div>

        @if($branch->whatsapp_number)
            @php
                $wa = preg_replace('/\D+/', '', $branch->whatsapp_number);
            @endphp

            <a href="https://wa.me/{{ $wa }}" target="_blank" class="btn btn-light rounded-pill">
                <i class="bi bi-whatsapp text-success"></i>
            </a>
        @endif
    </div>
</div>