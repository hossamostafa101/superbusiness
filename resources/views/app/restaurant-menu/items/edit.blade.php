{{-- resources/views/app/restaurant-menu/items/edit.blade.php --}}
@extends('app.layouts.app')

@section('title', 'تعديل صنف منيو')
@section('page_title', 'تعديل صنف منيو')
@section('page_description', $restaurantMenuItem->name)

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.restaurant-menu.items.update', [$workspace, $restaurantMenuItem]) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @include('app.restaurant-menu.items.partials.form', [
                'workspace' => $workspace,
                'restaurantMenuItem' => $restaurantMenuItem,
                'branches' => $branches,
                'categories' => $categories,
                'isEdit' => true,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.restaurant-menu.items.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                @if($restaurantMenuItem->branch)
                    <a
                        href="{{ route('public.restaurant-menu.branch', [$workspace, $restaurantMenuItem->branch]) }}"
                        target="_blank"
                        class="btn btn-outline-secondary"
                    >
                        معاينة المنيو
                    </a>
                @endif

                <button type="submit" class="btn btn-primary">
                    تحديث الصنف
                </button>
            </div>
        </form>
    </div>
</div>
@endsection