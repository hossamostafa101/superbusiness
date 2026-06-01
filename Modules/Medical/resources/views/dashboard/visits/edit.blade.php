@extends('app.layouts.app')

@section('title', 'تعديل زيارة')
@section('page_title', 'تعديل زيارة')
@section('page_description', $visit->visit_number)

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.medical.visits.update', [$workspace, $visit]) }}">
            @csrf
            @method('PUT')

            @include('medical::dashboard.visits.partials.form', [
                'visit' => $visit,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.medical.visits.show', [$workspace, $visit]) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    تحديث الزيارة
                </button>
            </div>
        </form>
    </div>
</div>
@endsection