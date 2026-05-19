{{-- resources/views/app/business-customers/index.blade.php --}}
@extends('app.layouts.app')

@section('title', 'العملاء')
@section('page_title', 'العملاء')
@section('page_description', 'إدارة بيانات العملاء وربطهم بالمواعيد والمتابعات.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <strong>
            عدد العملاء:
            {{ $customers->total() }}
            /
            {{ $isUnlimited ? 'غير محدود' : $customersLimit }}
        </strong>
    </div>

    <a href="{{ route('app.customers.create', $workspace) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        إضافة عميل
    </a>
</div>

<div class="card content-card mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('app.customers.index', $workspace) }}" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">بحث</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="اسم العميل، الهاتف، البريد"
                >
            </div>

            <div class="col-md-3">
                <label class="form-label">الحالة</label>
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    <option value="active" @selected(request('status') === 'active')>نشط</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>غير نشط</option>
                    <option value="blocked" @selected(request('status') === 'blocked')>محظور</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">المصدر</label>
                <input
                    type="text"
                    name="source"
                    value="{{ request('source') }}"
                    class="form-control"
                    placeholder="manual"
                >
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark">
                    بحث
                </button>

                <a href="{{ route('app.customers.index', $workspace) }}" class="btn btn-outline-secondary">
                    Reset
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
                        <th>#</th>
                        <th>العميل</th>
                        <th>التواصل</th>
                        <th>المصدر</th>
                        <th>المواعيد</th>
                        <th>الحالة</th>
                        <th>أُضيف في</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>{{ $customer->id }}</td>

                            <td>
                                <div class="fw-semibold">{{ $customer->name }}</div>

                                @if($customer->gender)
                                    <small class="text-muted">
                                        {{ $customer->gender }}
                                    </small>
                                @endif
                            </td>

                            <td>
                                @if($customer->phone)
                                    <div>
                                        <i class="bi bi-telephone text-muted"></i>
                                        <a href="tel:{{ $customer->phone }}" class="text-decoration-none">
                                            {{ $customer->phone }}
                                        </a>
                                    </div>
                                @endif

                                @if($customer->email)
                                    <div>
                                        <i class="bi bi-envelope text-muted"></i>
                                        <a href="mailto:{{ $customer->email }}" class="text-decoration-none">
                                            {{ $customer->email }}
                                        </a>
                                    </div>
                                @endif

                                @if(! $customer->phone && ! $customer->email)
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-secondary">
                                    {{ $customer->source }}
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-info">
                                    {{ $customer->appointments_count }}
                                </span>
                            </td>

                            <td>
                                @if($customer->status === 'active')
                                    <span class="badge bg-success">نشط</span>
                                @elseif($customer->status === 'inactive')
                                    <span class="badge bg-warning text-dark">غير نشط</span>
                                @else
                                    <span class="badge bg-danger">محظور</span>
                                @endif
                            </td>

                            <td>
                                {{ $customer->created_at?->format('Y-m-d') }}
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a
                                        href="{{ route('app.customers.edit', [$workspace, $customer]) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        تعديل
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('app.customers.destroy', [$workspace, $customer]) }}"
                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا العميل؟')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                لا يوجد عملاء بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $customers->links() }}
        </div>
    </div>
</div>
@endsection