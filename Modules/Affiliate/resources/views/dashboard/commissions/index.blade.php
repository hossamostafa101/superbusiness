@extends('affiliate::dashboard.layout')

@section('title', 'العمولات')
@section('page_title', 'العمولات')
@section('page_description', 'متابعة العمولات المتاحة وقيد الانتظار والمدفوعة.')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card content-card h-100">
            <div class="card-body p-4">
                <div class="text-muted small mb-2">متاح</div>
                <div class="fs-3 fw-bold">
                    {{ number_format((float) $profile->available_balance, 2) }}
                    {{ $settings->currency }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card content-card h-100">
            <div class="card-body p-4">
                <div class="text-muted small mb-2">قيد الانتظار</div>
                <div class="fs-3 fw-bold">
                    {{ number_format((float) $profile->pending_balance, 2) }}
                    {{ $settings->currency }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card content-card h-100">
            <div class="card-body p-4">
                <div class="text-muted small mb-2">مدفوع</div>
                <div class="fs-3 fw-bold">
                    {{ number_format((float) $profile->paid_balance, 2) }}
                    {{ $settings->currency }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card content-card h-100">
            <div class="card-body p-4">
                <div class="text-muted small mb-2">ملغي</div>
                <div class="fs-3 fw-bold">
                    {{ number_format((float) $profile->cancelled_balance, 2) }}
                    {{ $settings->currency }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card content-card mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">الحالة</label>
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    <option value="pending" @selected(request('status') === 'pending')>قيد الانتظار</option>
                    <option value="available" @selected(request('status') === 'available')>متاحة</option>
                    <option value="paid" @selected(request('status') === 'paid')>مدفوعة</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>ملغية</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">النوع</label>
                <select name="type" class="form-select">
                    <option value="">كل الأنواع</option>
                    <option value="signup_bonus" @selected(request('type') === 'signup_bonus')>بونص تسجيل</option>
                    <option value="subscription_payment" @selected(request('type') === 'subscription_payment')>اشتراك مدفوع</option>
                    <option value="subscription_renewal" @selected(request('type') === 'subscription_renewal')>تجديد اشتراك</option>
                    <option value="upgrade" @selected(request('type') === 'upgrade')>ترقية</option>
                    <option value="manual_bonus" @selected(request('type') === 'manual_bonus')>بونص يدوي</option>
                    <option value="adjustment" @selected(request('type') === 'adjustment')>تسوية</option>
                </select>
            </div>

            <div class="col-md-2 d-grid">
                <button class="btn btn-dark">
                    عرض
                </button>
            </div>

            <div class="col-md-2 d-grid">
                <a href="{{ route('affiliate.commissions.index') }}" class="btn btn-light">
                    إعادة
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card content-card">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>النوع</th>
                        <th>قيمة العملية</th>
                        <th>العمولة</th>
                        <th>الحالة</th>
                        <th>تاريخ الكسب</th>
                        <th>متاحة في</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($commissions as $commission)
                        <tr>
                            <td>{{ $commission->typeLabel() }}</td>

                            <td>
                                {{ number_format((float) $commission->base_amount, 2) }}
                                {{ $commission->currency }}
                            </td>

                            <td>
                                <strong>
                                    {{ number_format((float) $commission->amount, 2) }}
                                    {{ $commission->currency }}
                                </strong>
                            </td>

                            <td>
                                <span class="badge {{ $commission->statusBadgeClass() }}">
                                    {{ $commission->statusLabel() }}
                                </span>
                            </td>

                            <td dir="ltr">
                                {{ $commission->earned_at?->format('Y-m-d H:i') ?: '-' }}
                            </td>

                            <td dir="ltr">
                                {{ $commission->available_at?->format('Y-m-d H:i') ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                لا توجد عمولات.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $commissions->links() }}
    </div>
</div>
@endsection