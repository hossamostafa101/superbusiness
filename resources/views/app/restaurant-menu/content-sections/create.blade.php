{{-- resources/views/app/restaurant-menu/content-sections/create.blade.php --}}
@extends('app.layouts.app')

@section('title', 'إضافة قسم')
@section('page_title', 'إضافة قسم للمنيو')
@section('page_description', 'أضف قسمًا مثل الأصناف المميزة أو مجموعة أصناف أو سلايدر عروض.')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card content-card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('app.restaurant-menu.content-sections.store', $workspace) }}">
                    @csrf

                    @include('app.restaurant-menu.content-sections.partials.form', [
                        'workspace' => $workspace,
                        'contentSection' => null,
                        'branches' => $branches,
                        'items' => $items,
                        'selectedItemIds' => old('item_ids', []),
                        'customBgEnabled' => $customBgEnabled,
                        'isEdit' => false,
                    ])

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('app.restaurant-menu.content-sections.index', $workspace) }}" class="btn btn-light">
                            إلغاء
                        </a>

                        <button type="submit" class="btn btn-primary">
                            حفظ القسم
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