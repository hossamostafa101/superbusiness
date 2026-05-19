{{-- resources/views/app/business-categories/create.blade.php --}}
@extends('app.layouts.app')

@section('title', 'إضافة تصنيف')
@section('page_title', 'إضافة تصنيف')
@section('page_description', 'أضف تصنيفًا جديدًا لتنظيم المنتجات.')

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.categories.store', $workspace) }}">
            @csrf

            @include('app.business-categories.partials.form', [
                'workspace' => $workspace,
                'businessCategory' => null,
                'isEdit' => false,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.categories.index', $workspace) }}" class="btn btn-light">
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