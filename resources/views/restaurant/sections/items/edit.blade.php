{{-- resources/views/restaurant/items/edit.blade.php --}}
@extends('restaurant.layouts.app')

@section('title', 'تعديل الصنف')

@section('content')
<div class="container py-4">

    <div class="mb-3">
        <h1 class="h4 mb-1">تعديل الصنف</h1>
        <p class="text-muted small mb-0">
            المطعم: <strong>{{ $restaurant->name }}</strong> —
            الفرع الحالي: <strong>{{ $branch->name }}</strong> —
            الصنف: <strong>{{ $item->name }}</strong>
        </p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST"
                  action="{{ route('restaurant.items.update', $item) }}"
                  enctype="multipart/form-data"> {{-- 👈 برضه هنا --}}
                @csrf
                @method('PUT')

                @include('restaurant.sections.items._form', [
                    'item'       => $item,
                    'categories' => $categories,
                ])
            </form>
        </div>
    </div>

</div>
@endsection
