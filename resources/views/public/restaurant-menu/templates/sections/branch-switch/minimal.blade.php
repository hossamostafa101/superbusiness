{{-- resources/views/public/restaurant-menu/templates/sections/branch-switch/minimal.blade.php --}}
@if($branches->count() > 1)
    <div class="branch-minimal">
        <select class="form-select" onchange="if(this.value) window.location.href=this.value;">
            @foreach($branches as $branchItem)
                <option
                    value="{{ route('public.restaurant-menu.branch', [$workspace, $branchItem]) }}"
                    @selected($branchItem->id === $branch->id)
                >
                    {{ $branchItem->name }}
                </option>
            @endforeach
        </select>
    </div>
@endif