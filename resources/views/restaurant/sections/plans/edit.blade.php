{{-- resources/views/admin/plans/edit.blade.php --}}
@extends('admin.layout.admin_app')

@section('title', 'تعديل خطة اشتراك')

@section('content')
<div class="container-fluid py-4">

    <div class="mb-3 d-flex justify-content-between align-items-center">
        <a href="{{ route('admin.plans.index') }}" class="btn btn-link p-0">
            &larr; الرجوع لقائمة الخطط
        </a>

        <span class="text-muted">
            تعديل الخطة: <strong>{{ $plan->name }}</strong>
        </span>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h1 class="h5 mb-0">تعديل خطة</h1>
        </div>

        <div class="card-body">
            @include('admin.sections.plans._form', [
                'action' => route('admin.plans.update', $plan),
                'method' => 'PUT',
                'plan'   => $plan,
            ])
        </div>
    </div>

</div>
@endsection
