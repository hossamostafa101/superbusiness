{{-- resources/views/app/business-links/create.blade.php --}}
@extends('app.layouts.app')

@section('title', 'إضافة رابط')
@section('page_title', 'إضافة رابط')
@section('page_description', 'أضف رابط جديد يظهر في الصفحة العامة.')

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.links.store', $workspace) }}">
            @csrf

            @include('app.business-links.partials.form', [
                'workspace' => $workspace,
                'businessLink' => null,
                'isEdit' => false,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.links.index', $workspace) }}" class="btn btn-light">
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