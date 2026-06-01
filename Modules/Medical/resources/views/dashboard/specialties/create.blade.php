@extends('app.layouts.app')

@section('title', 'إضافة تخصص')
@section('page_title', 'إضافة تخصص')
@section('page_description', 'إضافة تخصص طبي جديد.')

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.medical.specialties.store', $workspace) }}">
            @csrf

            @include('medical::dashboard.specialties.partials.form', [
                'specialty' => null,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.medical.specialties.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    حفظ التخصص
                </button>
            </div>
        </form>
    </div>
</div>
@endsection