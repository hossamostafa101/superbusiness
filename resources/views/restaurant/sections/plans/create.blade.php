{{-- resources/views/admin/plans/create.blade.php --}}
@extends('admin.layout.admin_app')

@section('title', 'إضافة خطة اشتراك')

@section('content')
<div class="container-fluid py-4">

    <div class="mb-3">
        <a href="{{ route('admin.plans.index') }}" class="btn btn-link p-0">
            &larr; الرجوع لقائمة الخطط
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h1 class="h5 mb-0">إضافة خطة جديدة</h1>
        </div>

        <div class="card-body">
            @include('admin.sections.plans._form', [
                'action' => route('admin.plans.store'),
                'method' => 'POST',
                'plan'   => null,
            ])
        </div>
    </div>

</div>
@endsection
