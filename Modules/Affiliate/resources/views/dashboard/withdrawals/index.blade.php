@extends('affiliate::dashboard.layout')

@section('title', 'السحوبات')
@section('page_title', 'السحوبات')
@section('page_description', 'طلب ومتابعة سحب أرباحك.')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card content-card h-100">
            <div class="card-body p-4">
                <div class="text-muted small mb-2">الرصيد المتاح</div>
                <div class="fs-3 fw-bold">
                    {{ number_format((float) $profile->available_balance, 2) }}
                    {{ $settings->currency }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card content-card h-100">
            <div class="card-body p-4">
                <div class="text-muted small mb-2">الحد الأدنى للسحب</div>
                <div class="fs-3 fw-bold">
                    {{ number_format((float) $settings->minimum_withdrawal_amount, 2) }}
                    {{ $settings->currency }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card content-card h-100">
            <div class="card-body p-4">
                <div class="text-muted small mb-2">حالة الحساب</div>
                <span class="badge {{ $profile->statusBadgeClass() }}">
                    {{ $profile->statusLabel() }}
                </span>
            </div>
        </div>
    </div>
</div>

<div class="card content-card mb-4">
    <div class="card-body p-4">
        <h2 class="h5 fw-bold mb-3">
            طلب سحب
        </h2>

        @if($profile->canRequestWithdrawal($settings))
            <form method="POST" action="{{ route('affiliate.withdrawals.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">طريقة الدفع</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="wallet" @selected(old('payment_method', $profile->payment_method) === 'wallet')>محفظة</option>
                            <option value="bank_transfer" @selected(old('payment_method', $profile->payment_method) === 'bank_transfer')>تحويل بنكي</option>
                            <option value="cash" @selected(old('payment_method', $profile->payment_method) === 'cash')>كاش</option>
                            <option value="other" @selected(old('payment_method', $profile->payment_method) === 'other')>أخرى</option>
                        </select>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">بيانات الدفع</label>
                        <textarea
                            name="payment_details"
                            rows="3"
                            class="form-control"
                            required
                            placeholder="رقم المحفظة / بيانات الحساب البنكي"
                        >{{ old('payment_details', $profile->payment_details) }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="affiliate_notes" rows="2" class="form-control">{{ old('affiliate_notes') }}</textarea>
                    </div>

                    <div class="col-12 d-flex justify-content-end">
                        <button class="btn btn-primary">
                            إرسال طلب السحب
                        </button>
                    </div>
                </div>
            </form>
        @else
            <div class="alert alert-warning rounded-4 mb-0">
                لا يمكنك طلب السحب الآن. يجب أن يكون الحساب نشطًا وأن يصل الرصيد المتاح إلى الحد الأدنى للسحب.
            </div>
        @endif
    </div>
</div>

<div class="card content-card">
    <div class="card-body p-4">
        <h2 class="h5 fw-bold mb-3">
            طلبات السحب
        </h2>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>المبلغ</th>
                        <th>الطريقة</th>
                        <th>الحالة</th>
                        <th>تاريخ الطلب</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($withdrawals as $withdrawal)
                        <tr>
                            <td dir="ltr">{{ $withdrawal->withdrawal_number }}</td>

                            <td>
                                <strong>
                                    {{ number_format((float) $withdrawal->amount, 2) }}
                                    {{ $withdrawal->currency }}
                                </strong>
                            </td>

                            <td>{{ $withdrawal->payment_method ?: '-' }}</td>

                            <td>
                                <span class="badge {{ $withdrawal->statusBadgeClass() }}">
                                    {{ $withdrawal->statusLabel() }}
                                </span>
                            </td>

                            <td dir="ltr">
                                {{ $withdrawal->requested_at?->format('Y-m-d H:i') ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                لا توجد طلبات سحب.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $withdrawals->links() }}
    </div>
</div>
@endsection