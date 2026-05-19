{{-- resources/views/app/restaurant-menu/items/create.blade.php --}}
@extends('app.layouts.app')

@section('title', 'إضافة صنف منيو')
@section('page_title', 'إضافة صنف منيو')
@section('page_description', 'أضف صنف طعام أو مشروب داخل منيو أحد الفروع.')

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.restaurant-menu.items.store', $workspace) }}" enctype="multipart/form-data">
            @csrf

            @include('app.restaurant-menu.items.partials.form', [
                'workspace' => $workspace,
                'restaurantMenuItem' => null,
                'branches' => $branches,
                'categories' => $categories,
                'isEdit' => false,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.restaurant-menu.items.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button type="submit" class="btn btn-primary">
                    حفظ الصنف
                </button>
            </div>
        </form>
    </div>
</div>
@endsection