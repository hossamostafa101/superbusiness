{{-- resources/views/app/restaurant-menu/categories/create.blade.php --}}
@extends('app.layouts.app')

@section('title', 'إضافة تصنيف منيو')
@section('page_title', 'إضافة تصنيف منيو')
@section('page_description', 'أضف قسمًا جديدًا داخل منيو أحد الفروع.')

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.restaurant-menu.categories.store', $workspace) }}" enctype="multipart/form-data">
            @csrf

            @include('app.restaurant-menu.categories.partials.form', [
                'workspace' => $workspace,
                'restaurantMenuCategory' => null,
                'branches' => $branches,
                'isEdit' => false,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.restaurant-menu.categories.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button type="submit" class="btn btn-primary">
                    حفظ التصنيف
                </button>
            </div>
        </form>
    </div>
</div>
@endsection