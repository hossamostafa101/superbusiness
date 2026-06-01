@extends('admin.layouts.app')

@section('title', 'تفاصيل المسوق')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.affiliate.profiles.index') }}" class="btn btn-light">
        رجوع
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body">
                <h1 class="h5 fw-bold mb-3">
                    بيانات المسوق
                </h1>

                <div class="mb-2">
                    <span class="text-muted">الاسم:</span>
                    <strong>{{ $profile->name }}</strong>
                </div>

                <div class="mb-2">
                    <span class="text-muted">البريد:</span>
                    <strong>{{ $profile->email }}</strong>
                </div>

                <div class="mb-2">
                    <span class="text-muted">الهاتف:</span>
                    <strong dir="ltr">{{ $profile->phone ?: '-' }}</strong>
                </div>

                <div class="mb-2">
                    <span class="text-muted">الكود:</span>
                    <strong dir="ltr">{{ $profile->code }}</strong>
                </div>

                <div class="mb-2">
                    <span class="text-muted">الحالة:</span>
                    <span class="badge {{ $profile->statusBadgeClass() }}">
                        {{ $profile->statusLabel() }}
                    </span>
                </div>

                <hr>

                <form method="POST" action="{{ route('admin.affiliate.profiles.update-status', $profile) }}">
                    @csrf
                    @method('PATCH')

                    <label class="form-label">تغيير الحالة</label>

                    <div class="input-group">
                        <select name="status" class="form-select">
                            <option value="pending" @selected($profile->status === 'pending')>قيد المراجعة</option>
                            <option value="active" @selected($profile->status === 'active')>نشط</option>
                            <option value="suspended" @selected($profile->status === 'suspended')>موقوف</option>
                            <option value="rejected" @selected($profile->status === 'rejected')>مرفوض</option>
                        </select>

                        <button class="btn btn-dark">
                            حفظ
                        </button>
                    </div>
                </form>

                <hr>

<form method="POST" action="{{ route('admin.affiliate.profiles.generate-links', $profile) }}">
    @csrf

    <button class="btn btn-outline-primary w-100">
        إنشاء روابط التخصصات
    </button>
</form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h2 class="h5 fw-bold mb-3">
                    الرصيد
                </h2>

                <div class="d-grid gap-2">
                    <div class="d-flex justify-content-between">
                        <span>متاح</span>
                        <strong>{{ number_format((float) $profile->available_balance, 2) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>قيد الانتظار</span>
                        <strong>{{ number_format((float) $profile->pending_balance, 2) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>مدفوع</span>
                        <strong>{{ number_format((float) $profile->paid_balance, 2) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>ملغي</span>
                        <strong>{{ number_format((float) $profile->cancelled_balance, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="h5 fw-bold mb-3">
                    إضافة عمولة يدوية
                </h2>

                <form method="POST" action="{{ route('admin.affiliate.profiles.manual-commission', $profile) }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">النوع</label>

                        <select name="type" class="form-select">
                            <option value="manual_bonus">بونص يدوي</option>
                            <option value="adjustment">تسوية</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">الحالة</label>

                        <select name="status" class="form-select">
                            <option value="available">متاحة</option>
                            <option value="pending">قيد الانتظار</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">المبلغ</label>

                        <input
                            type="number"
                            name="amount"
                            class="form-control"
                            step="0.01"
                            min="0.01"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">العملة</label>

                        <input
                            type="text"
                            name="currency"
                            value="EGP"
                            class="form-control"
                            dir="ltr"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>

                        <textarea name="notes" rows="2" class="form-control"></textarea>
                    </div>

                    <button class="btn btn-primary w-100">
                        إضافة العمولة
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="h5 fw-bold mb-3">
                    آخر الإحالات
                </h2>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>المستخدم</th>
                                <th>المساحة</th>
                                <th>الحالة</th>
                                <th>تاريخ التسجيل</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($latestReferrals as $referral)
                                <tr>
                                    <td>{{ $referral->referredUser?->name ?: '-' }}</td>
                                    <td>{{ $referral->workspace?->name ?: '-' }}</td>
                                    <td>{{ $referral->statusLabel() }}</td>
                                    <td dir="ltr">{{ $referral->registered_at?->format('Y-m-d H:i') ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        لا توجد إحالات.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="h5 fw-bold mb-3">
                    آخر العمولات
                </h2>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>النوع</th>
                                <th>المبلغ</th>
                                <th>الحالة</th>
                                <th>متاحة في</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($latestCommissions as $commission)
                                <tr>
                                    <td>{{ $commission->typeLabel() }}</td>
                                    <td>
                                        {{ number_format((float) $commission->amount, 2) }}
                                        {{ $commission->currency }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $commission->statusBadgeClass() }}">
                                            {{ $commission->statusLabel() }}
                                        </span>
                                    </td>
                                    <td dir="ltr">{{ $commission->available_at?->format('Y-m-d H:i') ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        لا توجد عمولات.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection