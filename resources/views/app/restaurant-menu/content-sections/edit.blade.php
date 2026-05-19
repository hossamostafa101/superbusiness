{{-- resources/views/app/restaurant-menu/content-sections/edit.blade.php --}}
@extends('app.layouts.app')

@section('title', 'تعديل قسم')
@section('page_title', 'تعديل قسم')
@section('page_description', $contentSection->title)

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card content-card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('app.restaurant-menu.content-sections.update', [$workspace, $contentSection]) }}">
                    @csrf
                    @method('PUT')

                    @include('app.restaurant-menu.content-sections.partials.form', [
                        'workspace' => $workspace,
                        'contentSection' => $contentSection,
                        'branches' => $branches,
                        'items' => $items,
                        'selectedItemIds' => old('item_ids', $selectedItemIds),
                        'customBgEnabled' => $customBgEnabled,
                        'isEdit' => true,
                    ])

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        @if($contentSection->type === 'offers_slider')
                            <a
                                href="{{ route('app.restaurant-menu.content-sections.offers.index', [$workspace, $contentSection]) }}"
                                class="btn btn-outline-warning"
                            >
                                إدارة العروض
                            </a>
                        @endif

                        <a href="{{ route('app.restaurant-menu.content-sections.index', $workspace) }}" class="btn btn-light">
                            إلغاء
                        </a>

                        <button type="submit" class="btn btn-primary">
                            تحديث القسم
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        @include('app.restaurant-menu.content-sections.partials.live-preview', [
            'previewUrl' => $previewUrl,
        ])
    </div>
</div>
@endsection