@extends('admin.layouts.app')

@section('title', 'تفاصيل طلب السحب')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.affiliate.withdrawals.index') }}" class="btn btn-light">
        رجوع
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h2 class="h5 fw-bold mb-3">بيانات الطلب</h2>

                <div class="mb-2">
                    <span class="text-muted">رقم الطلب:</span>
                    <strong dir="ltr">{{ $withdrawal->withdrawal_number }}</strong>
                </div>

                <div class="mb-2">
                    <span class="text-muted">المسوق:</span>
                    <strong>{{ $withdrawal->affiliateProfile?->name }}</strong>
                </div>

                <div class="mb-2">
                    <span class="text-muted">المبلغ:</span>
                    <strong>
                        {{ number_format((float) $withdrawal->amount, 2) }}
                        {{ $withdrawal->currency }}
                    </strong>
                </div>

                <div class="mb-2">
                    <span class="text-muted">الحالة:</span>
                    <span class="badge {{ $withdrawal->statusBadgeClass() }}">
                        {{ $withdrawal->statusLabel() }}
                    </span>
                </div>

                <hr>

                <div class="mb-2">
                    <span class="text-muted">طريقة الدفع:</span>
                    <strong>{{ $withdrawal->payment_method ?: '-' }}</strong>
                </div>

                <div class="mb-3">
                    <div class="text-muted small mb-1">بيانات الدفع</div>
                    <div style="white-space: pre-line">{{ $withdrawal->payment_details ?: '-' }}</div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small mb-1">ملاحظات المسوق</div>
                    <div style="white-space: pre-line">{{ $withdrawal->affiliate_notes ?: '-' }}</div>
                </div>

                <div>
                    <div class="text-muted small mb-1">ملاحظات الأدمن</div>
                    <div style="white-space: pre-line">{{ $withdrawal->admin_notes ?: '-' }}</div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h2 class="h5 fw-bold mb-3">إجراءات</h2>

                @if(in_array($withdrawal->status, ['requested', 'approved'], true))
                    <form method="POST" action="{{ route('admin.affiliate.withdrawals.approve', $withdrawal) }}" class="mb-2">
                        @csrf
                        @method('PATCH')

                        <textarea name="admin_notes" rows="2" class="form-control mb-2" placeholder="ملاحظات اختيارية"></textarea>

                        <button class="btn btn-info w-100">
                            قبول الطلب
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.affiliate.withdrawals.paid', $withdrawal) }}" class="mb-2">
                        @csrf
                        @method('PATCH')

                        <textarea name="admin_notes" rows="2" class="form-control mb-2" placeholder="ملاحظات الدفع"></textarea>

                        <button class="btn btn-success w-100">
                            تعليم كمدفوع
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.affiliate.withdrawals.reject', $withdrawal) }}">
                        @csrf
                        @method('PATCH')

                        <textarea name="admin_notes" rows="2" class="form-control mb-2" placeholder="سبب الرفض"></textarea>

                        <button class="btn btn-danger w-100">
                            رفض الطلب
                        </button>
                    </form>
                @else
                    <div class="alert alert-secondary mb-0">
                        لا توجد إجراءات متاحة لهذه الحالة.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h2 class="h5 fw-bold mb-3">العمولات المضمنة</h2>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>النوع</th>
                                <th>المبلغ</th>
                                <th>الحالة</th>
                                <th>تاريخ الاستحقاق</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($withdrawal->commissions as $commission)
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

                                    <td dir="ltr">
                                        {{ $commission->available_at?->format('Y-m-d H:i') ?: '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        لا توجد عمولات مرتبطة.
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