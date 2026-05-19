{{-- resources/views/admin/restaurant-menu/templates/create.blade.php --}}
@extends('admin.layout.admin_app')

@section('title', 'إضافة قالب منيو')

@section('content')
<div class="card">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h1 class="h4 fw-bold mb-1">إضافة قالب منيو</h1>
                <div class="text-muted">
                    اختر الأقسام التي يتكون منها القالب الكامل.
                </div>
            </div>

            <a href="{{ route('admin.restaurant-menu-templates.index') }}" class="btn btn-light">
                رجوع
            </a>
        </div>

        <form
            method="POST"
            action="{{ route('admin.restaurant-menu-templates.store') }}"
            enctype="multipart/form-data"
        >
            @csrf

            @include('admin.restaurant-menu.templates.partials.form', [
                'restaurantMenuTemplate' => null,
                'sectionOptions' => $sectionOptions,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.restaurant-menu-templates.index') }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    حفظ القالب
                </button>
            </div>
        </form>
    </div>
</div>
@endsection