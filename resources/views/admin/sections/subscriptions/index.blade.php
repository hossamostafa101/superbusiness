@extends('admin.layout.admin_app')

@section('title', 'الاشتراكات')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">الاشتراكات</h1>
        <p class="text-body-secondary mb-0">إدارة اشتراكات مساحات العمل وربطها بالباقات.</p>
    </div>

    @can('subscriptions.create')
        <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            إضافة اشتراك
        </a>
    @endcan
</div>

@include('admin.sections.subscriptions.partials.alerts')

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.subscriptions.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">بحث</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="اسم مساحة العمل أو المالك"
                >
            </div>

            <div class="col-md-3">
                <label class="form-label">الباقة</label>
                <select name="plan_id" class="form-select">
                    <option value="">كل الباقات</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" @selected((string) request('plan_id') === (string) $plan->id)>
                            {{ $plan->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">الحالة</label>
                <select name="status" class="form-select">
                    <option value="">الكل</option>
                    <option value="trialing" @selected(request('status') === 'trialing')>تجربة</option>
                    <option value="active" @selected(request('status') === 'active')>نشط</option>
                    <option value="past_due" @selected(request('status') === 'past_due')>متأخر</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>ملغي</option>
                    <option value="expired" @selected(request('status') === 'expired')>منتهي</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">الدورة</label>
                <select name="billing_cycle" class="form-select">
                    <option value="">الكل</option>
                    <option value="monthly" @selected(request('billing_cycle') === 'monthly')>شهري</option>
                    <option value="yearly" @selected(request('billing_cycle') === 'yearly')>سنوي</option>
                </select>
            </div>

            <div class="col-md-1 d-flex gap-2">
                <button type="submit" class="btn btn-dark">
                    بحث
                </button>
            </div>
        </form>

        <div class="mt-3">
            <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-sm btn-outline-secondary">
                إعادة ضبط
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <strong>قائمة الاشتراكات</strong>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>مساحة العمل</th>
                        <th>المالك</th>
                        <th>الباقة</th>
                        <th>الدورة</th>
                        <th>الحالة</th>
                        <th>يبدأ في</th>
                        <th>ينتهي في</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($subscriptions as $subscription)
                        <tr>
                            <td>{{ $subscription->id }}</td>

                            <td>
                                @if($subscription->workspace)
                                    <div class="fw-semibold">{{ $subscription->workspace->name }}</div>
                                    <small class="text-body-secondary">
                                        {{ $subscription->workspace->slug }}
                                    </small>
                                @else
                                    <span class="text-danger">غير موجودة</span>
                                @endif
                            </td>

                            <td>
                                @if($subscription->workspace?->owner)
                                    <div>{{ $subscription->workspace->owner->name }}</div>
                                    <small class="text-body-secondary">
                                        {{ $subscription->workspace->owner->email }}
                                    </small>
                                @else
                                    <span class="text-body-secondary">-</span>
                                @endif
                            </td>

                            <td>
                                @if($subscription->plan)
                                    <div class="fw-semibold">{{ $subscription->plan->name }}</div>
                                    <small class="text-body-secondary">{{ $subscription->plan->slug }}</small>
                                @else
                                    <span class="text-danger">بدون باقة</span>
                                @endif
                            </td>

                            <td>
                                @if($subscription->billing_cycle === 'yearly')
                                    <span class="badge bg-info">سنوي</span>
                                @else
                                    <span class="badge bg-secondary">شهري</span>
                                @endif
                            </td>

                            <td>
                                @switch($subscription->status)
                                    @case('trialing')
                                        <span class="badge bg-info">تجربة</span>
                                        @break

                                    @case('active')
                                        <span class="badge bg-success">نشط</span>
                                        @break

                                    @case('past_due')
                                        <span class="badge bg-warning text-dark">متأخر</span>
                                        @break

                                    @case('cancelled')
                                        <span class="badge bg-danger">ملغي</span>
                                        @break

                                    @default
                                        <span class="badge bg-dark">منتهي</span>
                                @endswitch
                            </td>

                            <td>{{ $subscription->starts_at?->format('Y-m-d') ?: '-' }}</td>

                            <td>
                                @if($subscription->ends_at)
                                    {{ $subscription->ends_at->format('Y-m-d') }}
                                @else
                                    <span class="text-body-secondary">-</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    @can('subscriptions.edit')
                                        <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="btn btn-sm btn-outline-primary">
                                            تعديل
                                        </a>

                                        @if($subscription->status !== 'active')
                                            <form method="POST" action="{{ route('admin.subscriptions.mark-active', $subscription) }}">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit" class="btn btn-sm btn-outline-success">
                                                    تفعيل
                                                </button>
                                            </form>
                                        @endif
                                    @endcan

                                    @can('subscriptions.cancel')
                                        @if(! in_array($subscription->status, ['cancelled', 'expired'], true))
                                            <form
                                                method="POST"
                                                action="{{ route('admin.subscriptions.cancel', $subscription) }}"
                                                onsubmit="return confirm('هل تريد إلغاء هذا الاشتراك؟')"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit" class="btn btn-sm btn-outline-warning">
                                                    إلغاء
                                                </button>
                                            </form>
                                        @endif

                                        <form
                                            method="POST"
                                            action="{{ route('admin.subscriptions.destroy', $subscription) }}"
                                            onsubmit="return confirm('هل أنت متأكد من حذف الاشتراك؟')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                حذف
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-body-secondary py-4">
                                لا توجد اشتراكات.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $subscriptions->links() }}
        </div>
    </div>
</div>
@endsection