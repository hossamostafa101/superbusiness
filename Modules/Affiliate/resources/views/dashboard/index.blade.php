@extends('affiliate::dashboard.layout')

@section('title', 'لوحة المسوق')
@section('page_title', 'لوحة المسوق')
@section('page_description', 'متابعة الروابط والعمولات والسحوبات.')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-3">
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
                <div class="text-muted small mb-2">تم دفعه</div>
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
                <div class="text-muted small mb-2">كود المسوق</div>
                <div class="fs-5 fw-bold" dir="ltr">
                    {{ $profile->code }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card content-card mb-4">
    <div class="card-body p-4">
        <h2 class="h5 fw-bold mb-3">
            رابطك العام
        </h2>

        <div class="input-group" dir="ltr">
            <input
                type="text"
                class="form-control"
                value="{{ route('public.affiliate.track', $profile->code) }}"
                readonly
            >

            <button
                type="button"
                class="btn btn-dark"
                onclick="navigator.clipboard.writeText('{{ route('public.affiliate.track', $profile->code) }}')"
            >
                Copy
            </button>
        </div>

        <div class="form-text mt-2">
            هذا الرابط يحفظ كودك عند زيارة العميل. لاحقًا سننشئ روابط خاصة لكل تخصص.
        </div>
    </div>
</div>

<div class="card content-card mb-4">
    <div class="card-body p-4">
        <h2 class="h5 fw-bold mb-3">
            روابط جاهزة
        </h2>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">رابط المطاعم</label>

                <div class="input-group" dir="ltr">
                    <input
                        type="text"
                        class="form-control"
                        value="{{ route('public.affiliate.track', $profile->code) }}?target=/restaurant"
                        readonly
                    >

                    <button
                        type="button"
                        class="btn btn-dark"
                        onclick="navigator.clipboard.writeText('{{ route('public.affiliate.track', $profile->code) }}?target=/restaurant')"
                    >
                        Copy
                    </button>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">رابط الطبي</label>

                <div class="input-group" dir="ltr">
                    <input
                        type="text"
                        class="form-control"
                        value="{{ route('public.affiliate.track', $profile->code) }}?target=/medical"
                        readonly
                    >

                    <button
                        type="button"
                        class="btn btn-dark"
                        onclick="navigator.clipboard.writeText('{{ route('public.affiliate.track', $profile->code) }}?target=/medical')"
                    >
                        Copy
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card content-card">
    <div class="card-body p-4">
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
                        <th>تاريخ الاستحقاق</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($latestCommissions as $commission)
                        <tr>
                            <td>{{ $commission->typeLabel() }}</td>

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
                                {{ $commission->available_at?->format('Y-m-d H:i') ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                لا توجد عمولات بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="alert alert-info rounded-4 mb-0">
            الحد الأدنى للسحب:
            <strong>
                {{ number_format((float) $settings->minimum_withdrawal_amount, 2) }}
                {{ $settings->currency }}
            </strong>
        </div>
    </div>
</div>
@endsection