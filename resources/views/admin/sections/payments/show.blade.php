{{-- resources/views/admin/sections/payments/show.blade.php --}}
@extends('admin.layout.admin_app')

@section('title', 'تفاصيل الدفع')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">تفاصيل الدفع #{{ $payment->id }}</h1>
        <p class="text-body-secondary mb-0">
            مراجعة بيانات الدفع والاشتراك المرتبط.
        </p>
    </div>

    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">
        رجوع
    </a>
</div>

@include('admin.sections.payments.partials.alerts')

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <strong>بيانات الدفع</strong>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="text-body-secondary small">المبلغ</div>
                        <div class="fw-semibold">
                            {{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="text-body-secondary small">مزود الدفع</div>
                        <div class="fw-semibold">{{ $payment->provider }}</div>
                    </div>

                    <div class="col-md-4">
                        <div class="text-body-secondary small">طريقة الدفع</div>
                        <div class="fw-semibold">{{ $payment->method }}</div>
                    </div>

                    <div class="col-md-4">
                        <div class="text-body-secondary small">الحالة</div>
                        <div>
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
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="text-body-secondary small">الدورة</div>
                        <div class="fw-semibold">{{ $payment->billing_cycle ?: '-' }}</div>
                    </div>

                    <div class="col-md-4">
                        <div class="text-body-secondary small">المرجع</div>
                        <div class="fw-semibold">{{ $payment->reference ?: '-' }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-body-secondary small">Provider Payment ID</div>
                        <div class="fw-semibold">{{ $payment->provider_payment_id ?: '-' }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-body-secondary small">Provider Reference</div>
                        <div class="fw-semibold">{{ $payment->provider_reference ?: '-' }}</div>
                    </div>

                    <div class="col-md-4">
                        <div class="text-body-secondary small">تاريخ الإنشاء</div>
                        <div>{{ $payment->created_at?->format('Y-m-d H:i') }}</div>
                    </div>

                    <div class="col-md-4">
                        <div class="text-body-secondary small">تاريخ الدفع</div>
                        <div>{{ $payment->paid_at?->format('Y-m-d H:i') ?: '-' }}</div>
                    </div>

                    <div class="col-md-4">
                        <div class="text-body-secondary small">تم الاعتماد بواسطة</div>
                        <div>{{ $payment->approvedBy?->name ?: '-' }}</div>
                    </div>

                    @if($payment->notes)
                        <div class="col-12">
                            <div class="text-body-secondary small">ملاحظات</div>
                            <div class="border rounded p-3">
                                {{ $payment->notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <strong>مساحة العمل والباقة</strong>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-body-secondary small">مساحة العمل</div>
                        <div class="fw-semibold">{{ $payment->workspace?->name ?: '-' }}</div>
                        <small class="text-body-secondary">{{ $payment->workspace?->slug }}</small>
                    </div>

                    <div class="col-md-6">
                        <div class="text-body-secondary small">المالك</div>
                        <div class="fw-semibold">{{ $payment->workspace?->owner?->name ?: '-' }}</div>
                        <small class="text-body-secondary">{{ $payment->workspace?->owner?->email }}</small>
                    </div>

                    <div class="col-md-6">
                        <div class="text-body-secondary small">الباقة</div>
                        <div class="fw-semibold">{{ $payment->plan?->name ?: '-' }}</div>
                        <small class="text-body-secondary">{{ $payment->plan?->slug }}</small>
                    </div>

                    <div class="col-md-6">
                        <div class="text-body-secondary small">الاشتراك المرتبط</div>
                        @if($payment->subscription)
                            <div class="fw-semibold">
                                #{{ $payment->subscription->id }}
                                —
                                {{ $payment->subscription->status }}
                            </div>
                            <small class="text-body-secondary">
                                ينتهي في:
                                {{ $payment->subscription->ends_at?->format('Y-m-d') ?: '-' }}
                            </small>
                        @else
                            <span class="text-body-secondary">لا يوجد اشتراك مرتبط بعد</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <strong>إثبات الدفع</strong>
            </div>

            <div class="card-body">
                @if($payment->receipt_image)
                    <a href="{{ asset('storage/' . $payment->receipt_image) }}" target="_blank">
                        <img
                            src="{{ asset('storage/' . $payment->receipt_image) }}"
                            alt="إثبات الدفع"
                            class="img-fluid rounded border"
                        >
                    </a>

                    <div class="mt-3">
                        <a
                            href="{{ asset('storage/' . $payment->receipt_image) }}"
                            target="_blank"
                            class="btn btn-outline-secondary w-100"
                        >
                            فتح الصورة في تبويب جديد
                        </a>
                    </div>
                @else
                    <div class="text-body-secondary">
                        لا توجد صورة إثبات.
                    </div>
                @endif
            </div>
        </div>

        @if($payment->provider === 'manual' && $payment->status === 'pending')
            <div class="card">
                <div class="card-header">
                    <strong>مراجعة الدفع اليدوي</strong>
                </div>

                <div class="card-body">
                    @can('payments.approve')
                        <form
                            method="POST"
                            action="{{ route('admin.payments.approve', $payment) }}"
                            class="mb-3"
                            onsubmit="return confirm('هل تريد اعتماد الدفع وتفعيل الاشتراك؟')"
                        >
                            @csrf

                            <button type="submit" class="btn btn-success w-100">
                                اعتماد وتفعيل الاشتراك
                            </button>
                        </form>
                    @endcan

                    @can('payments.reject')
                        <form
                            method="POST"
                            action="{{ route('admin.payments.reject', $payment) }}"
                            onsubmit="return confirm('هل تريد رفض هذا الدفع؟')"
                        >
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">سبب الرفض</label>
                                <textarea
                                    name="reason"
                                    class="form-control"
                                    rows="3"
                                    placeholder="اختياري"
                                ></textarea>
                            </div>

                            <button type="submit" class="btn btn-outline-danger w-100">
                                رفض الدفع
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        @endif
    </div>
</div>
@endsection