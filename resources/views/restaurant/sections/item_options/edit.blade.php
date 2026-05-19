{{-- resources/views/restaurant/item_options/edit.blade.php --}}
@extends('restaurant.layouts.app')

@section('title', 'تعديل خيار - ' . $item->name)

@section('content')
<div class="container py-4">

    <div class="mb-3">
        <h1 class="h4 mb-1">تعديل خيار</h1>
        <p class="text-muted small mb-0">
            المطعم: <strong>{{ $restaurant->name }}</strong> —
            الفرع الحالي: <strong>{{ $branch->name }}</strong> —
            الصنف: <strong>{{ $item->name }}</strong> —
            المجموعة: <strong>{{ $group->name }}</strong> —
            الخيار: <strong>{{ $option->name }}</strong>
        </p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST"
                  action="{{ route('restaurant.items.option-groups.options.update', [$item, $group, $option]) }}">
                @csrf
                @method('PUT')

                @include('restaurant.sections.item_options._form', [
                    'option' => $option,
                    'item'   => $item,
                    'group'  => $group,
                ])
            </form>
        </div>
    </div>

</div>
@endsection
