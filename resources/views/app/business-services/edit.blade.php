{{-- resources/views/app/business-services/edit.blade.php --}}
@extends('app.layouts.app')

@section('title', 'تعديل خدمة')
@section('page_title', 'تعديل خدمة')
@section('page_description', $businessService->name)

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.services.update', [$workspace, $businessService]) }}">
            @csrf
            @method('PUT')

            @include('app.business-services.partials.form', [
                'workspace' => $workspace,
                'businessService' => $businessService,
                'isEdit' => true,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.services.index', $workspace) }}" class="btn btn-light">
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