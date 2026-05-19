{{-- resources/views/app/business-services/create.blade.php --}}
@extends('app.layouts.app')

@section('title', 'إضافة خدمة')
@section('page_title', 'إضافة خدمة')
@section('page_description', 'أضف خدمة يمكن استخدامها في نظام المواعيد.')

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.services.store', $workspace) }}">
            @csrf

            @include('app.business-services.partials.form', [
                'workspace' => $workspace,
                'businessService' => null,
                'isEdit' => false,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.services.index', $workspace) }}" class="btn btn-light">
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