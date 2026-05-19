{{-- resources/views/restaurant/branches/edit.blade.php --}}
@extends('restaurant.layouts.app')

@section('title', 'تعديل بيانات الفرع')

@section('content')
<div class="container py-4">

    <div class="mb-3">
        <h1 class="h4 mb-1">تعديل بيانات الفرع</h1>
        <p class="text-muted small mb-0">
            المطعم: <strong>{{ $restaurant->name }}</strong> /
            الفرع: <strong>{{ $branch->name }}</strong>
        </p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('restaurant.branches.update', $branch) }}">
                @csrf
                @method('PUT')

                @include('restaurant.sections.branches._form', ['branch' => $branch])
            </form>
        </div>
    </div>

</div>
@endsection
