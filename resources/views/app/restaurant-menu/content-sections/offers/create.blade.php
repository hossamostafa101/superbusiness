@extends('app.layouts.app')

@section('title', 'إضافة عرض')
@section('page_title', 'إضافة عرض')
@section('page_description', $contentSection->title)

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form
            method="POST"
            action="{{ route('app.restaurant-menu.content-sections.offers.store', [$workspace, $contentSection]) }}"
            enctype="multipart/form-data"
        >
            @csrf

            @include('app.restaurant-menu.content-sections.offers.partials.form', [
                'workspace' => $workspace,
                'contentSection' => $contentSection,
                'offer' => null,
                'items' => $items,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.restaurant-menu.content-sections.offers.index', [$workspace, $contentSection]) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    حفظ العرض
                </button>
            </div>
        </form>
    </div>
</div>
@endsection