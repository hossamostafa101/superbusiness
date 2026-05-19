{{-- resources/views/app/restaurant-menu/branches/create.blade.php --}}
@extends('app.layouts.app')

@section('title', 'إضافة فرع')
@section('page_title', 'إضافة فرع')
@section('page_description', 'أضف فرعًا جديدًا، ويمكنك نسخ بيانات المنيو من فرع موجود حسب الباقة.')

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.restaurant-menu.branches.store', $workspace) }}">
            @csrf

            @include('app.restaurant-menu.branches.partials.form', [
                'workspace' => $workspace,
                'restaurantBranch' => null,
                'isEdit' => false,
                'cloneEnabled' => $cloneEnabled,
                'cloneBranches' => $cloneBranches,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.restaurant-menu.branches.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button type="submit" class="btn btn-primary">
                    حفظ الفرع
                </button>
            </div>
        </form>
    </div>
</div>
@endsection