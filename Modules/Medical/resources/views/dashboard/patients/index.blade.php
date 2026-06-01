@extends('app.layouts.app')

@section('title', 'المرضى')
@section('page_title', 'المرضى')
@section('page_description', 'إدارة ملفات المرضى وبيانات التواصل.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <strong>
        عدد المرضى:
        {{ $patients->total() }}
    </strong>

    <a href="{{ route('app.medical.patients.create', $workspace) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        إضافة مريض
    </a>
</div>

<div class="card content-card mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label">بحث</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="الاسم، الكود، الهاتف، الرقم القومي"
                >
            </div>

            <div class="col-md-3">
                <label class="form-label">النوع</label>
                <select name="gender" class="form-select">
                    <option value="">الكل</option>
                    <option value="male" @selected(request('gender') === 'male')>ذكر</option>
                    <option value="female" @selected(request('gender') === 'female')>أنثى</option>
                    <option value="unknown" @selected(request('gender') === 'unknown')>غير محدد</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">الحالة</label>
                <select name="status" class="form-select">
                    <option value="">الكل</option>
                    <option value="active" @selected(request('status') === 'active')>نشط</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>غير نشط</option>
                    <option value="blocked" @selected(request('status') === 'blocked')>محظور</option>
                </select>
            </div>

            <div class="col-md-1 d-grid">
                <button class="btn btn-dark">
                    بحث
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card content-card">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>المريض</th>
                        <th>الهاتف</th>
                        <th>النوع</th>
                        <th>تاريخ الميلاد</th>
                        <th>الحالة</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($patients as $patient)
                        <tr>
                            <td>
                                <div class="fw-bold">
                                    {{ $patient->full_name }}
                                </div>

                                <div class="small text-muted" dir="ltr">
                                    {{ $patient->patient_code ?: '-' }}
                                </div>
                            </td>

                            <td dir="ltr">
                                {{ $patient->phone ?: $patient->whatsapp_number ?: '-' }}
                            </td>

                            <td>
                                {{ $patient->genderLabel() }}
                            </td>

                            <td>
                                {{ $patient->birth_date?->format('Y-m-d') ?: '-' }}
                            </td>

                            <td>
                                @if($patient->status === 'active')
                                    <span class="badge bg-success">نشط</span>
                                @elseif($patient->status === 'blocked')
                                    <span class="badge bg-danger">محظور</span>
                                @else
                                    <span class="badge bg-secondary">غير نشط</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('app.medical.patients.show', [$workspace, $patient]) }}" class="btn btn-sm btn-outline-dark">
                                        الملف
                                    </a>

                                    <a href="{{ route('app.medical.patients.edit', [$workspace, $patient]) }}" class="btn btn-sm btn-outline-primary">
                                        تعديل
                                    </a>

                                    <form method="POST" action="{{ route('app.medical.patients.destroy', [$workspace, $patient]) }}" onsubmit="return confirm('حذف المريض؟')">
                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-sm btn-outline-danger">
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                لا يوجد مرضى بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $patients->links() }}
    </div>
</div>
@endsection