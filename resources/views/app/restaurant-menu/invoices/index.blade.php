{{-- resources/views/app/restaurant-menu/invoices/index.blade.php --}}
@extends('app.layouts.app')

@section('title', 'جلسات الطاولات')
@section('page_title', 'جلسات الطاولات')
@section('page_description', 'متابعة جلسات الطاولات المفتوحة والطلبات المرتبطة بها.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <strong>
            عدد الفواتير:
            {{ $invoices->total() }}
        </strong>
    </div>

    <a href="{{ route('app.restaurant-menu.tables.index', $workspace) }}" class="btn btn-outline-primary">
        <i class="bi bi-qr-code"></i>
        الطاولات و QR
    </a>
</div>

<div class="card content-card mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('app.restaurant-menu.invoices.index', $workspace) }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">بحث</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="رقم الفاتورة، الطاولة، العميل"
                >
            </div>

            <div class="col-md-2">
                <label class="form-label">الفرع</label>
                <select name="branch_id" class="form-select">
                    <option value="">كل الفروع</option>

                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected((string) request('branch_id') === (string) $branch->id)>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">الحالة</label>
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    <option value="open" @selected(request('status') === 'open')>مفتوحة</option>
                    <option value="closed" @selected(request('status') === 'closed')>مغلقة</option>
                    <option value="expired" @selected(request('status') === 'expired')>منتهية</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>ملغية</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">من</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
            </div>

            <div class="col-md-2">
                <label class="form-label">إلى</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
            </div>

            <div class="col-md-1">
                <button type="submit" class="btn btn-dark w-100">
                    بحث
                </button>
            </div>

            <div class="col-12">
                <a href="{{ route('app.restaurant-menu.invoices.index', $workspace) }}" class="btn btn-outline-secondary btn-sm">
                    إعادة ضبط
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card content-card">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>الطاولة</th>
                        <th>العميل</th>
                        <th>الفرع</th>
                        <th>المحتوى</th>
                        <th>الإجمالي</th>
                        <th>المدة</th>
                        <th>الحالة</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td>
                                <a href="{{ route('app.restaurant-menu.invoices.show', [$workspace, $invoice]) }}" class="fw-bold text-decoration-none">
                                    #{{ $invoice->invoice_number }}
                                </a>

                                <div class="small text-muted">
                                    {{ $invoice->created_at?->format('Y-m-d H:i') }}
                                </div>
                            </td>

                            <td>
                                @if($invoice->table)
                                    <div class="fw-semibold">
                                        {{ $invoice->table->name }}
                                    </div>

                                    <small class="text-muted">
                                        رقم {{ $invoice->table->number }}
                                    </small>
                                @else
                                    <div class="fw-semibold">
                                        {{ $invoice->table_number ?: '-' }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $invoice->opened_by_name ?: '-' }}
                                </div>

                                @if($invoice->opened_by_phone)
                                    <small class="text-muted">
                                        {{ $invoice->opened_by_phone }}
                                    </small>
                                @endif
                            </td>

                            <td>
                                {{ $invoice->branch?->name ?: '-' }}
                            </td>

                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <span class="badge bg-secondary">
                                        {{ $invoice->items_count }} صنف
                                    </span>

                                    <span class="badge bg-info">
                                        {{ $invoice->guests_count }} ضيف
                                    </span>

                                    <span class="badge bg-primary">
                                        {{ $invoice->orders_count }} طلب
                                    </span>
                                </div>
                            </td>

                            <td>
                                <strong>
                                    {{ number_format((float) $invoice->total, 2) }}
                                    {{ $invoice->currency }}
                                </strong>
                            </td>

                            <td>
                                <div class="small">
                                    فتح:
                                    {{ $invoice->opened_at?->format('H:i') ?: '-' }}
                                </div>

                                <div class="small text-muted">
                                    انتهاء:
                                    {{ $invoice->expires_at?->format('H:i') ?: '-' }}
                                </div>
                            </td>

                            <td>
                                @include('app.restaurant-menu.invoices.partials.status-badge', [
                                    'invoice' => $invoice,
                                ])
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    <a
                                        href="{{ route('app.restaurant-menu.invoices.show', [$workspace, $invoice]) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        التفاصيل
                                    </a>

                                    @if($invoice->status === 'open')
                                        <form method="POST" action="{{ route('app.restaurant-menu.invoices.extend', [$workspace, $invoice]) }}">
                                            @csrf
                                            @method('PATCH')

                                            <input type="hidden" name="minutes" value="{{ $extendMinutesStep }}">

                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                تمديد {{ $extendMinutesStep }}د
                                            </button>
                                        </form>

                                        <form
                                            method="POST"
                                            action="{{ route('app.restaurant-menu.invoices.update-status', [$workspace, $invoice]) }}"
                                            onsubmit="return confirm('هل تريد إغلاق هذه الفاتورة؟')"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <input type="hidden" name="status" value="closed">

                                            <button type="submit" class="btn btn-sm btn-dark">
                                                إغلاق
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                لا توجد فواتير بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $invoices->links() }}
        </div>
    </div>
</div>
@endsection