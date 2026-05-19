{{-- resources/views/app/restaurant-menu/option-groups/edit.blade.php --}}
@extends('app.layouts.app')

@section('title', 'تعديل مجموعة إضافات')
@section('page_title', 'تعديل مجموعة إضافات')
@section('page_description', $restaurantMenuItem->name . ' - ' . $restaurantItemOptionGroup->name)

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.restaurant-menu.items.option-groups.update', [$workspace, $restaurantMenuItem, $restaurantItemOptionGroup]) }}">
            @csrf
            @method('PUT')

            @include('app.restaurant-menu.option-groups.partials.form', [
                'workspace' => $workspace,
                'restaurantMenuItem' => $restaurantMenuItem,
                'restaurantItemOptionGroup' => $restaurantItemOptionGroup,
                'isEdit' => true,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.restaurant-menu.items.option-groups.index', [$workspace, $restaurantMenuItem]) }}" class="btn btn-light">
                    إلغاء
                </a>

                <a
                    href="{{ route('app.restaurant-menu.items.option-groups.options.index', [$workspace, $restaurantMenuItem, $restaurantItemOptionGroup]) }}"
                    class="btn btn-outline-success"
                >
                    إدارة الخيارات
                </a>

                <button type="submit" class="btn btn-primary">
                    تحديث المجموعة
                </button>
            </div>
        </form>
    </div>
</div>
@endsection