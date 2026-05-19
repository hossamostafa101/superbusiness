{{-- resources/views/app/business-customers/edit.blade.php --}}
@extends('app.layouts.app')

@section('title', 'تعديل عميل')
@section('page_title', 'تعديل عميل')
@section('page_description', $businessCustomer->name)

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('app.customers.update', [$workspace, $businessCustomer]) }}">
            @csrf
            @method('PUT')

            @include('app.business-customers.partials.form', [
                'workspace' => $workspace,
                'businessCustomer' => $businessCustomer,
                'isEdit' => true,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.customers.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button type="submit" class="btn btn-primary">
                    تحديث
                </button>
            </div>
        </form>
    </div>
</div>
@endsection