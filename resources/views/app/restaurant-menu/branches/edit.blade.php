{{-- resources/views/app/business-links/edit.blade.php --}}
@extends('app.layouts.app')

@section('title', 'تعديل رابط')
@section('page_title', 'تعديل رابط')
@section('page_description', $businessLink->title)

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.links.update', [$workspace, $businessLink]) }}">
            @csrf
            @method('PUT')

            @include('app.business-links.partials.form', [
                'workspace' => $workspace,
                'businessLink' => $businessLink,
                'isEdit' => true,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.links.index', $workspace) }}" class="btn btn-light">
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