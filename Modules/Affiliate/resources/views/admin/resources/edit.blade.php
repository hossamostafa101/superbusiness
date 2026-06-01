@extends('admin.layouts.app')

@section('title', 'تعديل مورد تسويقي')

@section('content')
<div class="card">
    <div class="card-body">
        <h1 class="h4 fw-bold mb-4">
            تعديل مورد تسويقي
        </h1>

        <form method="POST" action="{{ route('admin.affiliate.resources.update', $resource) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @include('affiliate::admin.resources.partials.form', [
                'resource' => $resource,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.affiliate.resources.index') }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    تحديث
                </button>
            </div>
        </form>
    </div>
</div>
@endsection