{{-- resources/views/admin/restaurant-menu/templates/edit.blade.php --}}
@extends('admin.layout.admin_app')

@section('title', 'تعديل قالب منيو')

@section('content')
<div class="card">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h1 class="h4 fw-bold mb-1">تعديل قالب منيو</h1>
                <div class="text-muted">
                    {{ $restaurantMenuTemplate->name }}
                </div>
            </div>

            <a href="{{ route('admin.restaurant-menu-templates.index') }}" class="btn btn-light">
                رجوع
            </a>
        </div>

        <form
            method="POST"
            action="{{ route('admin.restaurant-menu-templates.update', $restaurantMenuTemplate) }}"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            @include('admin.restaurant-menu.templates.partials.form', [
                'restaurantMenuTemplate' => $restaurantMenuTemplate,
                'sectionOptions' => $sectionOptions,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.restaurant-menu-templates.index') }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    تحديث القالب
                </button>
            </div>
        </form>
    </div>
</div>
@endsection