@extends('app.layouts.app')

@section('title', 'تعديل فرع طبي')
@section('page_title', 'تعديل فرع طبي')
@section('page_description', $branch->name)

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.medical.branches.update', [$workspace, $branch]) }}">
            @csrf
            @method('PUT')

            @include('medical::dashboard.branches.partials.form', [
                'branch' => $branch,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.medical.branches.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    تحديث الفرع
                </button>
            </div>
        </form>
    </div>
</div>
@endsection