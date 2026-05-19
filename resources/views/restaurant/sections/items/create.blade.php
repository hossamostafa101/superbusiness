{{-- resources/views/restaurant/items/create.blade.php --}}
@extends('restaurant.layouts.app')

@section('title', 'إضافة صنف جديد')

@section('content')
<div class="container py-4">

    <div class="mb-3">
        <h1 class="h4 mb-1">إضافة صنف جديد</h1>
        <p class="text-muted small mb-0">
            المطعم: <strong>{{ $restaurant->name }}</strong> —
            الفرع الحالي: <strong>{{ $branch->name }}</strong>
        </p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST"
                  action="{{ route('restaurant.items.store') }}"
                  enctype="multipart/form-data"> {{-- 👈 مهم جداً لرفع الصورة --}}
                @csrf

                @include('restaurant.sections.items._form', [
                    'item'        => $item ?? null,
                    'categories'  => $categories,
                ])
            </form>
        </div>
    </div>

</div>
@endsection
