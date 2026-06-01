@extends('admin.layouts.app')

@section('title', 'إدارة برنامج الشركاء')

@section('content')
<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="{{ route('admin.affiliate.settings.edit') }}" class="btn btn-primary">
        الإعدادات
    </a>

    <a href="{{ route('admin.affiliate.profiles.index') }}" class="btn btn-outline-primary">
        المسوقين
    </a>

    <a href="{{ route('admin.affiliate.withdrawals.index') }}" class="btn btn-outline-dark">
        السحوبات
    </a>

    <a href="{{ route('admin.affiliate.resources.index') }}" class="btn btn-outline-success">
    الموارد التسويقية
</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small mb-1">المسوقين</div>
                <div class="fs-3 fw-bold">{{ $stats['profiles'] }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small mb-1">النشطين</div>
                <div class="fs-3 fw-bold">{{ $stats['active_profiles'] }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small mb-1">الإحالات</div>
                <div class="fs-3 fw-bold">{{ $stats['referrals'] }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small mb-1">طلبات سحب جديدة</div>
                <div class="fs-3 fw-bold">{{ $stats['withdrawals_requested'] }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h5 fw-bold mb-3">
                    أحدث المسوقين
                </h2>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>الاسم</th>
                                <th>الكود</th>
                                <th>الحالة</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($latestProfiles as $profile)
                                <tr>
                                    <td>{{ $profile->name }}</td>
                                    <td dir="ltr">{{ $profile->code }}</td>
                                    <td>
                                        <span class="badge {{ $profile->statusBadgeClass() }}">
                                            {{ $profile->statusLabel() }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.affiliate.profiles.show', $profile) }}" class="btn btn-sm btn-outline-dark">
                                            عرض
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        لا توجد بيانات.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h5 fw-bold mb-3">
                    أحدث السحوبات
                </h2>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>المسوق</th>
                                <th>المبلغ</th>
                                <th>الحالة</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($latestWithdrawals as $withdrawal)
                                <tr>
                                    <td>{{ $withdrawal->affiliateProfile?->name }}</td>
                                    <td>
                                        {{ number_format((float) $withdrawal->amount, 2) }}
                                        {{ $withdrawal->currency }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $withdrawal->statusBadgeClass() }}">
                                            {{ $withdrawal->statusLabel() }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.affiliate.withdrawals.show', $withdrawal) }}" class="btn btn-sm btn-outline-dark">
                                            عرض
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        لا توجد بيانات.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <hr>

                <div class="small text-muted">
                    إجمالي العمولات:
                    <strong>{{ number_format((float) $stats['commissions_total'], 2) }}</strong>
                    |
                    قيد الانتظار:
                    <strong>{{ number_format((float) $stats['commissions_pending'], 2) }}</strong>
                    |
                    متاحة:
                    <strong>{{ number_format((float) $stats['commissions_available'], 2) }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection