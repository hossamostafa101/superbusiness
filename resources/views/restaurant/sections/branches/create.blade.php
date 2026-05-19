{{-- resources/views/restaurant/branches/create.blade.php --}}
@extends('restaurant.layouts.app')

@section('title', 'إضافة فرع جديد')

@section('content')
<div class="container py-4">

    <div class="mb-3">
        <h1 class="h4 mb-1">إضافة فرع جديد</h1>
        <p class="text-muted small mb-0">
            المطعم: <strong>{{ $restaurant->name }}</strong>
        </p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('restaurant.branches.store') }}">
                @csrf

                @include('restaurant.sections.branches._form', ['branch' => $branch ?? null])
            </form>
        </div>
    </div>

</div>
@endsection
