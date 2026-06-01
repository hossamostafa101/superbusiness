@extends('app.layouts.app')

@section('title', 'تعديل تخصص')
@section('page_title', 'تعديل تخصص')
@section('page_description', $specialty->name)

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.medical.specialties.update', [$workspace, $specialty]) }}">
            @csrf
            @method('PUT')

            @include('medical::dashboard.specialties.partials.form', [
                'specialty' => $specialty,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.medical.specialties.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    تحديث التخصص
                </button>
            </div>
        </form>
    </div>
</div>
@endsection