{{-- resources/views/app/restaurant-menu/options/create.blade.php --}}
@extends('app.layouts.app')

@section('title', 'إضافة خيار')
@section('page_title', 'إضافة خيار')
@section('page_description', 'إضافة خيار داخل مجموعة: ' . $restaurantItemOptionGroup->name)

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST"
              action="{{ route('app.restaurant-menu.items.option-groups.options.store', [$workspace, $restaurantMenuItem, $restaurantItemOptionGroup]) }}">
            @csrf

            @include('app.restaurant-menu.options.partials.form', [
                'workspace' => $workspace,
                'restaurantMenuItem' => $restaurantMenuItem,
                'restaurantItemOptionGroup' => $restaurantItemOptionGroup,
                'restaurantItemOption' => null,
                'isEdit' => false,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.restaurant-menu.items.option-groups.options.index', [$workspace, $restaurantMenuItem, $restaurantItemOptionGroup]) }}"
                   class="btn btn-light">
                    إلغاء
                </a>

                <button type="submit" class="btn btn-primary">
                    حفظ الخيار
                </button>
            </div>
        </form>
    </div>
</div>
@endsection