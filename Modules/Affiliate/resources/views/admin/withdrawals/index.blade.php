@extends('admin.layout.admin_app')

@section('title', 'سحوبات المسوقين')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 fw-bold mb-0">
                سحوبات المسوقين
            </h1>
        </div>

        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    <option value="requested" @selected(request('status') === 'requested')>مطلوب</option>
                    <option value="approved" @selected(request('status') === 'approved')>مقبول</option>
                    <option value="paid" @selected(request('status') === 'paid')>مدفوع</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>مرفوض</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>ملغي</option>
                </select>
            </div>

            <div class="col-md-2">
                <button class="btn btn-dark w-100">
                    عرض
                </button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>المسوق</th>
                        <th>المبلغ</th>
                        <th>الطريقة</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($withdrawals as $withdrawal)
                        <tr>
                            <td dir="ltr">{{ $withdrawal->withdrawal_number }}</td>

                            <td>
                                <div class="fw-bold">
                                    {{ $withdrawal->affiliateProfile?->name }}
                                </div>

                                <div class="small text-muted">
                                    {{ $withdrawal->affiliateProfile?->email }}
                                </div>
                            </td>

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

                            <td>
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('admin.affiliate.withdrawals.show', $withdrawal) }}" class="btn btn-sm btn-outline-dark">
                                        عرض
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
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