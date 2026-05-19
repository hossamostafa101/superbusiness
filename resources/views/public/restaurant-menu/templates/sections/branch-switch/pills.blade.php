@if($branches->count() > 1)
    <div class="branch-switch">
        @foreach($branches as $branchItem)
            <a
                href="{{ route('public.restaurant-menu.branch', [$workspace, $branchItem]) }}"
                class="branch-pill {{ $branchItem->id === $branch->id ? 'active' : '' }}"
            >
                {{ $branchItem->name }}
            </a>
        @endforeach
    </div>
@endif