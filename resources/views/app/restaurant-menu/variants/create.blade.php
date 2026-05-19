{{-- resources/views/app/restaurant-menu/variants/create.blade.php --}}
@extends('app.layouts.app')

@section('title', 'إضافة Variant')
@section('page_title', 'إضافة Variant')
@section('page_description', 'إضافة حجم أو نسخة سعرية للصنف: ' . $restaurantMenuItem->name)

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.restaurant-menu.items.variants.store', [$workspace, $restaurantMenuItem]) }}">
            @csrf

            @include('app.restaurant-menu.variants.partials.form', [
                'workspace' => $workspace,
                'restaurantMenuItem' => $restaurantMenuItem,
                'restaurantItemVariant' => null,
                'isEdit' => false,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.restaurant-menu.items.variants.index', [$workspace, $restaurantMenuItem]) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button type="submit" class="btn btn-primary">
                    حفظ
                </button>
            </div>
        </form>
    </div>
</div>
@endsection