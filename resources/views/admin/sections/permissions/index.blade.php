@extends('admin.layout.admin_app') {{-- أو استبدلها بـ layout لوحة التحكم --}}
@section('title','الصلاحيات')

@section('content')
<div class="card">
  <div class="card-body">

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="card-title mb-0">الصلاحيات</h5>
      <a href="{{ route('admin.permissions.create') }}" class="btn btn-sm btn-info text-white">
        <i class="bi bi-plus-lg"></i> صلاحية جديدة
      </a>
    </div>

    {{-- فلاش رسائل --}}
    @if(session('ok')) <div class="alert alert-success small">{{ session('ok') }}</div> @endif
    @if(session('err'))<div class="alert alert-danger  small">{{ session('err') }}</div> @endif

    {{-- بحث بسيط (اختياري) --}}
    <form method="get" class="mb-3">
      <div class="row g-2">
        <div class="col-md-6">
          <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm"
                 placeholder="ابحث بالاسم مثل: articles.create">
        </div>
        <div class="col-md-6 text-end">
          <button class="btn btn-sm btn-outline-secondary">بحث</button>
          <a href="{{ route('admin.permissions.index') }}" class="btn btn-sm btn-outline-light border">مسح</a>
        </div>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead>
          <tr>
            <th>الاسم</th>
            <th class="text-muted small">المجموعة</th>
            <th class="text-end">تحكم</th>
          </tr>
        </thead>
        <tbody>
          @forelse($permissions as $p)
            @php
              $parts = explode('.',$p->name);
              $group = $parts[0] ?? '—';
            @endphp
            <tr>
              <td class="fw-semibold">{{ $p->name }}</td>
              <td class="text-muted small">{{ strtoupper($group) }}</td>
              <td class="text-end">
                {{-- لا يوجد edit في الكنترولر الحالي، فقط حذف --}}
                <form method="post" action="{{ route('admin.permissions.destroy', $p) }}" class="d-inline"
                      onsubmit="return confirm('حذف الصلاحية «{{ $p->name }}»؟ هذا قد يؤثر على الأدوار المرتبطة.')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash"></i> حذف
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="3" class="text-center text-muted small">لا توجد صلاحيات حالياً.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- ترقيم صفحات --}}
    <div class="mt-2">
      {{ $permissions->withQueryString()->links() }}
    </div>

  </div>
</div>
@endsection
