{{-- resources/views/app/business-products/edit.blade.php --}}
@extends('app.layouts.app')

@section('title', 'تعديل منتج')
@section('page_title', 'تعديل منتج')
@section('page_description', $businessProduct->name)

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.products.update', [$workspace, $businessProduct]) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @include('app.business-products.partials.form', [
                'workspace' => $workspace,
                'businessProduct' => $businessProduct,
                'categories' => $categories,
                'isEdit' => true,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.products.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button type="submit" class="btn btn-primary">
                    تحديث
                </button>
            </div>
        </form>
    </div>
</div>
@endsection