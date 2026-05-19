{{-- resources/views/app/restaurant-menu/categories/edit.blade.php --}}
@extends('app.layouts.app')

@section('title', 'تعديل تصنيف منيو')
@section('page_title', 'تعديل تصنيف منيو')
@section('page_description', $restaurantMenuCategory->name)

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.restaurant-menu.categories.update', [$workspace, $restaurantMenuCategory]) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @include('app.restaurant-menu.categories.partials.form', [
                'workspace' => $workspace,
                'restaurantMenuCategory' => $restaurantMenuCategory,
                'branches' => $branches,
                'isEdit' => true,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.restaurant-menu.categories.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                @if($restaurantMenuCategory->branch)
                    <a
                        href="{{ route('public.restaurant-menu.branch', [$workspace, $restaurantMenuCategory->branch]) }}"
                        target="_blank"
                        class="btn btn-outline-secondary"
                    >
                        معاينة المنيو
                    </a>
                @endif

                <button type="submit" class="btn btn-primary">
                    تحديث التصنيف
                </button>
            </div>
        </form>
    </div>
</div>
@endsection