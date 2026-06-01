@extends('app.layouts.app')

@section('title', 'إضافة روشتة')
@section('page_title', 'إضافة روشتة')
@section('page_description', 'إنشاء روشتة مرتبطة بزيارة.')

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        @if(empty($visit))
            <div class="alert alert-warning rounded-4">
                يجب إنشاء الروشتة من داخل صفحة الزيارة حتى ترتبط بالمريض والطبيب.
            </div>

            <a href="{{ route('app.medical.visits.index', $workspace) }}" class="btn btn-light">
                رجوع للزيارات
            </a>
        @else
            <form method="POST" action="{{ route('app.medical.prescriptions.store', $workspace) }}">
                @csrf

                @include('medical::dashboard.prescriptions.partials.form', [
                    'prescription' => null,
                    'visit' => $visit,
                ])

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('app.medical.visits.show', [$workspace, $visit]) }}" class="btn btn-light">
                        إلغاء
                    </a>

                    <button class="btn btn-primary">
                        حفظ الروشتة
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection