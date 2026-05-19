{{-- resources/views/public/restaurant-menu/templates/sections/branch-switch/ordoraa-default.blade.php --}}
@if($branches->count() > 1)
    <section class="od-branch-switch">
        <select class="od-branch-select" onchange="if(this.value) window.location.href=this.value;">
            @foreach($branches as $branchItem)
                <option
                    value="{{ route('public.restaurant-menu.branch', [$workspace, $branchItem]) }}"
                    @selected($branchItem->id === $branch->id)
                >
                    {{ $branchItem->name }}
                </option>
            @endforeach
        </select>
    </section>
@endif