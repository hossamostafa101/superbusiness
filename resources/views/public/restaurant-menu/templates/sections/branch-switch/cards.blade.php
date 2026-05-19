{{-- resources/views/public/restaurant-menu/templates/sections/branch-switch/cards.blade.php --}}
@if($branches->count() > 1)
    <div class="branch-cards">
        @foreach($branches as $branchItem)
            <a
                href="{{ route('public.restaurant-menu.branch', [$workspace, $branchItem]) }}"
                class="branch-card {{ $branchItem->id === $branch->id ? 'active' : '' }}"
            >
                <div class="branch-card-icon">
                    <i class="bi bi-shop"></i>
                </div>

                <div>
                    <div class="fw-bold">
                        {{ $branchItem->name }}
                    </div>

                    @if($branchItem->is_default)
                        <div class="small opacity-75">
                            الفرع الرئيسي
                        </div>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
@endif