{{-- resources/views/restaurant/item_option_groups/create.blade.php --}}
@extends('restaurant.layouts.app')

@section('title', 'إضافة مجموعة خيارات جديدة - ' . $item->name)

@section('content')
<div class="container py-4">

    <div class="mb-3">
        <h1 class="h4 mb-1">إضافة مجموعة خيارات جديدة</h1>
        <p class="text-muted small mb-0">
            المطعم: <strong>{{ $restaurant->name }}</strong> —
            الفرع الحالي: <strong>{{ $branch->name }}</strong> —
            الصنف: <strong>{{ $item->name }}</strong>
        </p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST"
                  action="{{ route('restaurant.items.option-groups.store', $item) }}">
                @csrf

                @include('restaurant.sections.item_option_groups._form', [
                    'group' => $group ?? null,
                    'item'  => $item
                ])
            </form>
        </div>
    </div>

</div>
@endsection
