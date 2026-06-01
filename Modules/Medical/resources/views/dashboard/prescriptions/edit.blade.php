@extends('app.layouts.app')

@section('title', 'تعديل روشتة')
@section('page_title', 'تعديل روشتة')
@section('page_description', $prescription->prescription_number)

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.medical.prescriptions.update', [$workspace, $prescription]) }}">
            @csrf
            @method('PUT')

            @include('medical::dashboard.prescriptions.partials.form', [
                'prescription' => $prescription,
                'visit' => null,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.medical.prescriptions.show', [$workspace, $prescription]) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    تحديث الروشتة
                </button>
            </div>
        </form>
    </div>
</div>
@endsection