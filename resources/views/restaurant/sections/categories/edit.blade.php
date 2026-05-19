{{-- resources/views/restaurant/categories/edit.blade.php --}}
@extends('restaurant.layouts.app')

@section('title', 'تعديل التصنيف')

@section('content')
<div class="container py-4">

    <div class="mb-3">
        <h1 class="h4 mb-1">تعديل التصنيف</h1>
        <p class="text-muted small mb-0">
            المطعم: <strong>{{ $restaurant->name }}</strong> —
            الفرع الحالي: <strong>{{ $branch->name }}</strong> —
            التصنيف: <strong>{{ $category->name }}</strong>
        </p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('restaurant.categories.update', $category) }}">
                @csrf
                @method('PUT')

                @include('restaurant.sections.categories._form', ['category' => $category])
            </form>
        </div>
    </div>

</div>
@endsection
