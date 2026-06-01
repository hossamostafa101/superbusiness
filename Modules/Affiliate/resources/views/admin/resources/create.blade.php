@extends('admin.layouts.app')

@section('title', 'إضافة مورد تسويقي')

@section('content')
<div class="card">
    <div class="card-body">
        <h1 class="h4 fw-bold mb-4">
            إضافة مورد تسويقي
        </h1>

        <form method="POST" action="{{ route('admin.affiliate.resources.store') }}" enctype="multipart/form-data">
            @csrf

            @include('affiliate::admin.resources.partials.form', [
                'resource' => null,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.affiliate.resources.index') }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    حفظ
                </button>
            </div>
        </form>
    </div>
</div>
@endsection