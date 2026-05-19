{{-- resources/views/app/restaurant-menu/options/edit.blade.php --}}
@extends('app.layouts.app')

@section('title', 'تعديل خيار')
@section('page_title', 'تعديل خيار')
@section('page_description', $restaurantItemOptionGroup->name . ' - ' . $restaurantItemOption->name)

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST"
              action="{{ route('app.restaurant-menu.items.option-groups.options.update', [$workspace, $restaurantMenuItem, $restaurantItemOptionGroup, $restaurantItemOption]) }}">
            @csrf
            @method('PUT')

            @include('app.restaurant-menu.options.partials.form', [
                'workspace' => $workspace,
                'restaurantMenuItem' => $restaurantMenuItem,
                'restaurantItemOptionGroup' => $restaurantItemOptionGroup,
                'restaurantItemOption' => $restaurantItemOption,
                'isEdit' => true,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.restaurant-menu.items.option-groups.options.index', [$workspace, $restaurantMenuItem, $restaurantItemOptionGroup]) }}"
                   class="btn btn-light">
                    إلغاء
                </a>

                <button type="submit" class="btn btn-primary">
                    تحديث الخيار
                </button>
            </div>
        </form>
    </div>
</div>
@endsection