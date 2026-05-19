{{-- resources/views/app/business-products/create.blade.php --}}
@extends('app.layouts.app')

@section('title', 'إضافة منتج')
@section('page_title', 'إضافة منتج')
@section('page_description', 'أضف منتجًا أو خدمة جديدة لعرضها في الصفحة العامة.')

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.products.store', $workspace) }}" enctype="multipart/form-data">
            @csrf

            @include('app.business-products.partials.form', [
                'workspace' => $workspace,
                'businessProduct' => null,
                'categories' => $categories,
                'isEdit' => false,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.products.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button type="submit" class="btn btn-primary">
                    حفظ
                </button>
            </div>
        </form>
    </div>
</div>
@endsection