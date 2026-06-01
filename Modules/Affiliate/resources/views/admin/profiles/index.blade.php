@extends('admin.layouts.app')

@section('title', 'المسوقين')

@section('content')
<div class="card">
    <div class="card-body">
        <h1 class="h4 fw-bold mb-4">
            المسوقين
        </h1>

        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-5">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="بحث بالاسم أو البريد أو الكود"
                >
            </div>

            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    <option value="pending" @selected(request('status') === 'pending')>قيد المراجعة</option>
                    <option value="active" @selected(request('status') === 'active')>نشط</option>
                    <option value="suspended" @selected(request('status') === 'suspended')>موقوف</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>مرفوض</option>
                </select>
            </div>

            <div class="col-md-2 d-grid">
                <button class="btn btn-dark">
                    بحث
                </button>
            </div>

            <div class="col-md-2 d-grid">
                <a href="{{ route('admin.affiliate.profiles.index') }}" class="btn btn-light">
                    إعادة
                </a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>المسوق</th>
                        <th>الكود</th>
                        <th>الرصيد المتاح</th>
                        <th>قيد الانتظار</th>
                        <th>إحالات</th>
                        <th>عمولات</th>
                        <th>الحالة</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($profiles as $profile)
                        <tr>
                            <td>
                                <div class="fw-bold">
                                    {{ $profile->name }}
                                </div>

                                <div class="small text-muted">
                                    {{ $profile->email }}
                                </div>
                            </td>

                            <td dir="ltr">
                                <strong>{{ $profile->code }}</strong>
                            </td>

                            <td>
                                {{ number_format((float) $profile->available_balance, 2) }}
                            </td>

                            <td>
                                {{ number_format((float) $profile->pending_balance, 2) }}
                            </td>

                            <td>
                                {{ $profile->referrals_count }}
                            </td>

                            <td>
                                {{ $profile->commissions_count }}
                            </td>

                            <td>
                                <span class="badge {{ $profile->statusBadgeClass() }}">
                                    {{ $profile->statusLabel() }}
                                </span>
                            </td>

                            <td>
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('admin.affiliate.profiles.show', $profile) }}" class="btn btn-sm btn-outline-dark">
                                        عرض
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                لا يوجد مسوقين.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $profiles->links() }}
    </div>
</div>
@endsection