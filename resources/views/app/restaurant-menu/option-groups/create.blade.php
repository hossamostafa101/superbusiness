{{-- resources/views/app/restaurant-menu/option-groups/create.blade.php --}}
@extends('app.layouts.app')

@section('title', 'إضافة مجموعة إضافات')
@section('page_title', 'إضافة مجموعة إضافات')
@section('page_description', 'إضافة مجموعة اختيارات للصنف: ' . $restaurantMenuItem->name)

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.restaurant-menu.items.option-groups.store', [$workspace, $restaurantMenuItem]) }}">
            @csrf

            @include('app.restaurant-menu.option-groups.partials.form', [
                'workspace' => $workspace,
                'restaurantMenuItem' => $restaurantMenuItem,
                'restaurantItemOptionGroup' => null,
                'isEdit' => false,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.restaurant-menu.items.option-groups.index', [$workspace, $restaurantMenuItem]) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button type="submit" class="btn btn-primary">
                    حفظ المجموعة
                </button>
            </div>
        </form>
    </div>
</div>
@endsection