@extends('admin.layout.admin_app') {{-- أو layout لوحة التحكم --}}
@section('title','صلاحية جديدة')

@section('content')
<div class="card">
  <div class="card-body">
    <h5 class="card-title mb-3">إضافة صلاحية</h5>

    @if ($errors->any())
      <div class="alert alert-danger small">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="post" action="{{ route('admin.permissions.store') }}" class="vstack gap-3">
      @csrf
      <div>
        <label class="form-label">اسم الصلاحية</label>
        <input type="text" name="name" class="form-control" required
               placeholder="مثال: articles.create أو articles.publish" value="{{ old('name') }}">
        <div class="form-text">
          استخدم تنسيق <code>module.action</code> مثل
          <code>articles.view</code>, <code>articles.create</code>, <code>articles.edit</code>, <code>articles.delete</code>.
        </div>
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-info text-white"><i class="bi bi-check2"></i> حفظ</button>
        <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">رجوع</a>
      </div>
    </form>

    {{-- اقتراحات سريعة (اختياري) --}}
    <hr>
    <div class="small text-muted">
      اقتراحات: articles.(view/create/edit/delete/publish) — reviews.(view/create/edit/delete) — users.(view/create/edit/delete)
    </div>
  </div>
</div>
@endsection
