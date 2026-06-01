@extends('app.layouts.app')

@section('title', 'الفريق الطبي')
@section('page_title', 'الفريق الطبي')
@section('page_description', 'إدارة الأطباء والتمريض والفنيين وموظفي المنشأة.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <strong>
        عدد أعضاء الفريق:
        {{ $staff->total() }}
    </strong>

    <a href="{{ route('app.medical.staff.create', $workspace) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        إضافة عضو
    </a>
</div>

<div class="card content-card mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">بحث</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="الاسم، الهاتف، البريد، اللقب"
                >
            </div>

            <div class="col-md-3">
                <label class="form-label">الدور</label>
                <select name="role" class="form-select">
                    <option value="">كل الأدوار</option>
                    <option value="doctor" @selected(request('role') === 'doctor')>طبيب</option>
                    <option value="nurse" @selected(request('role') === 'nurse')>تمريض</option>
                    <option value="lab_technician" @selected(request('role') === 'lab_technician')>فني معمل</option>
                    <option value="radiology_technician" @selected(request('role') === 'radiology_technician')>فني أشعة</option>
                    <option value="receptionist" @selected(request('role') === 'receptionist')>استقبال</option>
                    <option value="accountant" @selected(request('role') === 'accountant')>محاسب</option>
                    <option value="admin" @selected(request('role') === 'admin')>مدير</option>
                    <option value="other" @selected(request('role') === 'other')>أخرى</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">القسم</label>
                <select name="department_id" class="form-select">
                    <option value="">كل الأقسام</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" @selected((string) request('department_id') === (string) $department->id)>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-1 d-grid">
                <button class="btn btn-dark">
                    فلترة
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
                        <th>الاسم</th>
                        <th>الدور</th>
                        <th>الفرع</th>
                        <th>القسم</th>
                        <th>التخصص</th>
                        <th>الحجز</th>
                        <th>الحالة</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($staff as $member)
                        <tr>
                            <td>
                                <div class="fw-bold">
                                    {{ $member->name }}
                                </div>

                                @if($member->title)
                                    <div class="small text-muted">
                                        {{ $member->title }}
                                    </div>
                                @endif

                                @if($member->phone)
                                    <div class="small text-muted" dir="ltr">
                                        {{ $member->phone }}
                                    </div>
                                @endif

                                @if($member->is_featured)
                                    <span class="badge bg-warning text-dark mt-1">
                                        مميز
                                    </span>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $member->roleLabel() }}
                                </span>
                            </td>

                            <td>
                                {{ $member->branch?->name ?: 'كل الفروع' }}
                            </td>

                            <td>
                                {{ $member->department?->name ?: '-' }}
                            </td>

                            <td>
                                {{ $member->specialty?->name ?: '-' }}
                            </td>

                            <td>
                                @if($member->accepts_online_booking)
                                    <span class="badge bg-primary">متاح</span>
                                @else
                                    <span class="badge bg-secondary">غير متاح</span>
                                @endif
                            </td>

                            <td>
                                @if($member->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">متوقف</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a
    href="{{ route('app.medical.staff.working-hours.edit', [$workspace, $member]) }}"
    class="btn btn-sm btn-outline-secondary"
>
    المواعيد
</a>

                                    <a
    href="{{ route('app.medical.staff.services.edit', [$workspace, $member]) }}"
    class="btn btn-sm btn-outline-dark"
>
    الخدمات
</a>

                                    <a href="{{ route('app.medical.staff.edit', [$workspace, $member]) }}" class="btn btn-sm btn-outline-primary">
                                        تعديل
                                    </a>

                                    <form method="POST" action="{{ route('app.medical.staff.destroy', [$workspace, $member]) }}" onsubmit="return confirm('حذف عضو الفريق؟')">
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
                            <td colspan="8" class="text-center text-muted py-4">
                                لا يوجد أعضاء فريق بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $staff->links() }}
    </div>
</div>
@endsection