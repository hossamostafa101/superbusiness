{{-- resources/views/app/analytics/index.blade.php --}}
@extends('app.layouts.app')

@section('title', 'التحليلات')
@section('page_title', 'التحليلات')
@section('page_description', 'تابع الضغطات والتفاعل على صفحتك ومنتجاتك.')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card content-card">
            <div class="card-body">
                <div class="text-muted mb-2">كل الأحداث</div>
                <div class="fs-3 fw-bold">{{ $stats['total_events'] }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card content-card">
            <div class="card-body">
                <div class="text-muted mb-2">ضغطات واتساب</div>
                <div class="fs-3 fw-bold">{{ $stats['whatsapp_clicks'] }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card content-card">
            <div class="card-body">
                <div class="text-muted mb-2">طلبات منتجات</div>
                <div class="fs-3 fw-bold">{{ $stats['product_whatsapp_clicks'] }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card content-card">
            <div class="card-body">
                <div class="text-muted mb-2">ضغطات روابط</div>
                <div class="fs-3 fw-bold">{{ $stats['link_clicks'] }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card content-card">
            <div class="card-header bg-white">
                <strong>أكثر المنتجات طلبًا</strong>
            </div>

            <div class="card-body">
                @forelse($topProducts as $row)
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span>{{ $row->businessProduct?->name ?: 'منتج محذوف' }}</span>
                        <strong>{{ $row->clicks }}</strong>
                    </div>
                @empty
                    <div class="text-muted text-center py-4">
                        لا توجد بيانات بعد.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card content-card">
            <div class="card-header bg-white">
                <strong>أكثر الروابط ضغطًا</strong>
            </div>

            <div class="card-body">
                @forelse($topLinks as $row)
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span>{{ $row->businessLink?->title ?: 'رابط محذوف' }}</span>
                        <strong>{{ $row->clicks }}</strong>
                    </div>
                @empty
                    <div class="text-muted text-center py-4">
                        لا توجد بيانات بعد.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection