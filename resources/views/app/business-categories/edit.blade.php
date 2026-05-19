{{-- resources/views/app/business-categories/edit.blade.php --}}
@extends('app.layouts.app')

@section('title', 'تعديل تصنيف')
@section('page_title', 'تعديل تصنيف')
@section('page_description', $businessCategory->name)

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.categories.update', [$workspace, $businessCategory]) }}">
            @csrf
            @method('PUT')

            @include('app.business-categories.partials.form', [
                'workspace' => $workspace,
                'businessCategory' => $businessCategory,
                'isEdit' => true,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.categories.index', $workspace) }}" class="btn btn-light">
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