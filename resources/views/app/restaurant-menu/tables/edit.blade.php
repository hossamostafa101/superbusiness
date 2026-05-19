{{-- resources/views/app/restaurant-menu/tables/edit.blade.php --}}
@extends('app.layouts.app')

@section('title', 'تعديل طاولة')
@section('page_title', 'تعديل طاولة')
@section('page_description', $restaurantTable->name)

@section('content')
@php
    $publicUrl = route('public.restaurant-menu.branch', [
        $workspace,
        $restaurantTable->branch,
        'table_code' => $restaurantTable->code,
    ]);

    $qrImage = 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=' . urlencode($publicUrl);
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card content-card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('app.restaurant-menu.tables.update', [$workspace, $restaurantTable]) }}">
                    @csrf
                    @method('PUT')

                    @include('app.restaurant-menu.tables.partials.form', [
                        'workspace' => $workspace,
                        'restaurantTable' => $restaurantTable,
                        'branches' => $branches,
                        'isEdit' => true,
                    ])

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('app.restaurant-menu.tables.index', $workspace) }}" class="btn btn-light">
                            إلغاء
                        </a>

                        <button type="submit" class="btn btn-primary">
                            تحديث الطاولة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card content-card">
            <div class="card-body p-4 text-center">
                <h6 class="fw-bold mb-3">QR الطاولة</h6>

                <img
                    src="{{ $qrImage }}"
                    alt="QR {{ $restaurantTable->name }}"
                    class="rounded border bg-white mb-3"
                    style="width: 220px; height: 220px;"
                >

                <div class="small text-muted mb-3" style="word-break: break-all;" dir="ltr">
                    {{ $publicUrl }}
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ $publicUrl }}" target="_blank" class="btn btn-outline-primary">
                        فتح رابط الطاولة
                    </a>

                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        onclick="navigator.clipboard.writeText('{{ $publicUrl }}'); alert('تم نسخ الرابط');"
                    >
                        نسخ الرابط
                    </button>

                    <form
                        method="POST"
                        action="{{ route('app.restaurant-menu.tables.regenerate-code', [$workspace, $restaurantTable]) }}"
                        onsubmit="return confirm('سيتم تغيير رابط QR الحالي. هل تريد المتابعة؟')"
                    >
                        @csrf
                        @method('PATCH')

                        <button type="submit" class="btn btn-warning w-100">
                            إنشاء QR جديد
                        </button>
                    </form>
                </div>

                <div class="alert alert-light border mt-3 mb-0 text-start small">
                    عند تغيير QR، الرابط القديم لن يربط الطلب بنفس الطاولة.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection