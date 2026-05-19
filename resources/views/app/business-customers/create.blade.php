{{-- resources/views/app/business-customers/create.blade.php --}}
@extends('app.layouts.app')

@section('title', 'إضافة عميل')
@section('page_title', 'إضافة عميل')
@section('page_description', 'أضف بيانات عميل جديد إلى قاعدة العملاء.')

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.customers.store', $workspace) }}">
            @csrf

            @include('app.business-customers.partials.form', [
                'workspace' => $workspace,
                'businessCustomer' => null,
                'isEdit' => false,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.customers.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button type="submit" class="btn btn-primary">
                    حفظ
                </button>
            </div>
        </form>
    </div>
</div>
@endsection