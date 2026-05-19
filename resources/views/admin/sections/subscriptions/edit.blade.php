@extends('admin.layout.admin_app')

@section('title', 'تعديل اشتراك')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">تعديل اشتراك</h1>
        <p class="text-body-secondary mb-0">
            {{ $subscription->workspace?->name }} — {{ $subscription->plan?->name }}
        </p>
    </div>

    <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-secondary">
        رجوع
    </a>
</div>

@include('admin.sections.subscriptions.partials.alerts')

<div class="card">
    <div class="card-header">
        <strong>بيانات الاشتراك</strong>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.subscriptions.update', $subscription) }}">
            @csrf
            @method('PUT')

            @include('admin.sections.subscriptions.partials.form', [
                'subscription' => $subscription,
                'workspaces' => collect(),
                'plans' => $plans,
                'isEdit' => true,
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-light">
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