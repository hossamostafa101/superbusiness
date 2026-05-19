{{-- resources/views/app/restaurant-menu/tables/create.blade.php --}}
@extends('app.layouts.app')

@section('title', 'إضافة طاولة')
@section('page_title', 'إضافة طاولة')
@section('page_description', 'أضف طاولة جديدة داخل أحد الفروع وسيتم إنشاء QR تلقائي لها.')

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.restaurant-menu.tables.store', $workspace) }}">
            @csrf

            @include('app.restaurant-menu.tables.partials.form', [
                'workspace' => $workspace,
                'restaurantTable' => null,
                'branches' => $branches,
                'isEdit' => false,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.restaurant-menu.tables.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button type="submit" class="btn btn-primary">
                    حفظ الطاولة
                </button>
            </div>
        </form>
    </div>
</div>
@endsection