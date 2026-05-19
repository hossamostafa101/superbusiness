{{-- resources/views/restaurant/item_options/create.blade.php --}}
@extends('restaurant.layouts.app')

@section('title', 'إضافة خيار جديد - ' . $item->name)

@section('content')
<div class="container py-4">

    <div class="mb-3">
        <h1 class="h4 mb-1">إضافة خيار جديد</h1>
        <p class="text-muted small mb-0">
            المطعم: <strong>{{ $restaurant->name }}</strong> —
            الفرع الحالي: <strong>{{ $branch->name }}</strong> —
            الصنف: <strong>{{ $item->name }}</strong> —
            المجموعة: <strong>{{ $group->name }}</strong>
        </p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST"
                  action="{{ route('restaurant.items.option-groups.options.store', [$item, $group]) }}">
                @csrf

                @include('restaurant.sections.item_options._form', [
                    'option' => $option ?? null,
                    'item'   => $item,
                    'group'  => $group,
                ])
            </form>
        </div>
    </div>

</div>
@endsection
