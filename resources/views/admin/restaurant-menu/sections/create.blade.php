@extends('admin.layout.admin_app')

@section('title', 'إضافة قسم منيو')

@section('content')
<div class="card">
    <div class="card-body p-4">
        <h1 class="h4 fw-bold mb-4">إضافة قسم منيو</h1>

        <form method="POST" action="{{ route('admin.restaurant-menu-template-sections.store') }}" enctype="multipart/form-data">
            @csrf

            @include('admin.restaurant-menu.sections.partials.form', [
                'restaurantMenuTemplateSection' => null,
                'sectionTypes' => $sectionTypes,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.restaurant-menu-template-sections.index') }}" class="btn btn-light">إلغاء</a>
                <button class="btn btn-primary">حفظ</button>
            </div>
        </form>
    </div>
</div>
@endsection