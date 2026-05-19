@extends('admin.layout.admin_app') {{-- أو layout لوحة التحكم --}}
@section('title','إنشاء دور')

@section('content')
<div class="card">
  <div class="card-body">
    <h5 class="card-title">إنشاء دور جديد</h5>
    <form method="post" action="{{ route('admin.roles.store') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label">اسم الدور</label>
        <input name="name" class="form-control" placeholder="مثال: article-editor" required>
      </div>

      <div class="mb-3">
        <label class="form-label">الصلاحيات</label>
        <div class="row g-2">
          @foreach($permissions as $group => $perms)
            <div class="col-12 col-md-6">
              <div class="border rounded p-2 h-100">
                <div class="fw-semibold mb-2">{{ strtoupper($group) }}</div>
                @foreach($perms as $p)
                  <div class="form-check mb-1">
                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $p->name }}" id="p_{{ $p->id }}">
                    <label class="form-check-label small" for="p_{{ $p->id }}">{{ $p->name }}</label>
                  </div>
                @endforeach
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <button class="btn btn-info text-white">حفظ</button>
      <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">رجوع</a>
    </form>
  </div>
</div>
@endsection
