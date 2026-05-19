{{-- resources/views/restaurant/categories/create.blade.php --}}
@extends('restaurant.layouts.app')

@section('title', 'إضافة تصنيف جديد')

@section('content')
<div class="container py-4">

    <div class="mb-3">
        <h1 class="h4 mb-1">إضافة تصنيف جديد</h1>
        <p class="text-muted small mb-0">
            المطعم: <strong>{{ $restaurant->name }}</strong> —
            الفرع الحالي: <strong>{{ $branch->name }}</strong>
        </p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('restaurant.categories.store') }}">
                @csrf

                @include('restaurant.sections.categories._form', ['category' => $category ?? null])
            </form>
        </div>
    </div>

</div>
@endsection
