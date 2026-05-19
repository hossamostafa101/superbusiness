@extends('app.layouts.app')

@section('title', 'تعديل عرض')
@section('page_title', 'تعديل عرض')
@section('page_description', $offer->title)

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form
            method="POST"
            action="{{ route('app.restaurant-menu.content-sections.offers.update', [$workspace, $contentSection, $offer]) }}"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            @include('app.restaurant-menu.content-sections.offers.partials.form', [
                'workspace' => $workspace,
                'contentSection' => $contentSection,
                'offer' => $offer,
                'items' => $items,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.restaurant-menu.content-sections.offers.index', [$workspace, $contentSection]) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    تحديث العرض
                </button>
            </div>
        </form>
    </div>
</div>
@endsection