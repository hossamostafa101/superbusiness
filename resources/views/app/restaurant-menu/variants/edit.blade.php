{{-- resources/views/app/restaurant-menu/variants/edit.blade.php --}}
@extends('app.layouts.app')

@section('title', 'تعديل Variant')
@section('page_title', 'تعديل Variant')
@section('page_description', $restaurantMenuItem->name . ' - ' . $restaurantItemVariant->name)

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.restaurant-menu.items.variants.update', [$workspace, $restaurantMenuItem, $restaurantItemVariant]) }}">
            @csrf
            @method('PUT')

            @include('app.restaurant-menu.variants.partials.form', [
                'workspace' => $workspace,
                'restaurantMenuItem' => $restaurantMenuItem,
                'restaurantItemVariant' => $restaurantItemVariant,
                'isEdit' => true,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.restaurant-menu.items.variants.index', [$workspace, $restaurantMenuItem]) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button type="submit" class="btn btn-primary">
                    تحديث
                </button>
            </div>
        </form>
    </div>
</div>
@endsection