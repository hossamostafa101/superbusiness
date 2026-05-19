{{-- resources/views/admin/sections/payments/index.blade.php --}}
@extends('admin.layout.admin_app')

@section('title', 'المدفوعات')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">المدفوعات</h1>
        <p class="text-body-secondary mb-0">مراجعة المدفوعات اليدوية ومدفوعات بوابات الدفع.</p>
    </div>
</div>

<div class="col-md-3">
    <label class="form-label">بحث</label>
    <input
        type="text"
        name="search"
        value="{{ request('search') }}"
        class="form-control"
        placeholder="Workspace / Ref / Provider ID"
    >
</div>

@include('admin.sections.payments.partials.alerts')

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">الحالة</label>
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    <option value="pending" @selected(request('status') === 'pending')>قيد المراجعة</option>
                    <option value="approved" @selected(request('status') === 'approved')>معتمد</option>
                    <option value="paid" @selected(request('status') === 'paid')>مدفوع</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>مرفوض</option>
                    <option value="failed" @selected(request('status') === 'failed')>فشل</option>
                    <option value="refunded" @selected(request('status') === 'refunded')>مسترد</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>ملغي</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">مزود الدفع</label>
                <select name="provider" class="form-select">
                    <option value="">كل المزودين</option>
                    <option value="manual" @selected(request('provider') === 'manual')>Manual</option>
                    <option value="kashier" @selected(request('provider') === 'kashier')>Kashier</option>
                    <option value="paddle" @selected(request('provider') === 'paddle')>Paddle</option>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-dark">
                    بحث
                </button>

                <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">
                    إعادة ضبط
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <strong>قائمة المدفوعات</strong>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>مساحة العمل</th>
                        <th>الباقة</th>
                        <th>المبلغ</th>
                        <th>الدورة</th>
                        <th>المزود</th>
                        <th>الحالة</th>
                        <th>الإثبات</th>
                        <th>التاريخ</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->id }}</td>

                            <td>
                                @if($payment->workspace)
                                    <div class="fw-semibold">{{ $payment->workspace->name }}</div>
                                    <small class="text-body-secondary">{{ $payment->workspace->slug }}</small>
                                @else
                                    <span class="text-danger">غير موجودة</span>
                                @endif
                            </td>

                            <td>
                                @if($payment->plan)
                                    <div>{{ $payment->plan->name }}</div>
                                    <small class="text-body-secondary">{{ $payment->plan->slug }}</small>
                                @else
                                    <span class="text-body-secondary">-</span>
                                @endif
                            </td>

                            <td>
                                <strong>{{ number_format((float) $payment->amount, 2) }}</strong>
                                <span>{{ $payment->currency }}</span>
                            </td>

                            <td>
                                @if($payment->billing_cycle === 'yearly')
                                    <span class="badge bg-info">سنوي</span>
                                @elseif($payment->billing_cycle === 'monthly')
                                    <span class="badge bg-secondary">شهري</span>
                                @else
                                    <span class="text-body-secondary">-</span>
                                @endif
                            </td>

                            <td>
                                @if($payment->provider === 'manual')
                                    <span class="badge bg-dark">Manual</span>
                                @elseif($payment->provider === 'kashier')
                                    <span class="badge bg-primary">Kashier</span>
                                @elseif($payment->provider === 'paddle')
                                    <span class="badge bg-info">Paddle</span>
                                @else
                                    <span class="badge bg-secondary">{{ $payment->provider }}</span>
                                @endif
                            </td>

                            <td>
                                @switch($payment->status)
                                    @case('pending')
                                        <span class="badge bg-warning text-dark">قيد المراجعة</span>
                                        @break

                                    @case('approved')
                                        <span class="badge bg-success">معتمد</span>
                                        @break

                                    @case('paid')
                                        <span class="badge bg-success">مدفوع</span>
                                        @break

                                    @case('rejected')
                                        <span class="badge bg-danger">مرفوض</span>
                                        @break

                                    @case('failed')
                                        <span class="badge bg-danger">فشل</span>
                                        @break

                                    @case('refunded')
                                        <span class="badge bg-secondary">مسترد</span>
                                        @break

                                    @default
                                        <span class="badge bg-dark">ملغي</span>
                                @endswitch
                            </td>

                            <td>
                                @if($payment->receipt_image)
                                    <a
                                        href="{{ asset('storage/' . $payment->receipt_image) }}"
                                        target="_blank"
                                        class="btn btn-sm btn-outline-secondary"
                                    >
                                        عرض
                                    </a>
                                @else
                                    <span class="text-body-secondary">-</span>
                                @endif
                            </td>

                            <td>{{ $payment->created_at?->format('Y-m-d H:i') }}</td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    @can('payments.view')
                                        <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-outline-primary">
                                            التفاصيل
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-body-secondary py-4">
                                لا توجد مدفوعات.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $payments->links() }}
        </div>
    </div>
</div>
@endsection