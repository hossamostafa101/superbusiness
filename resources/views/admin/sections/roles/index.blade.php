@extends('admin.layout.admin_app')

@section('title','الأدوار')

@section('content')
<div class="card">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="card-title mb-0">الأدوار</h5>
      <a href="{{ route('admin.roles.create') }}" class="btn btn-sm btn-info text-white">دور جديد</a>
    </div>

    <table class="table table-sm">
      <thead><tr><th>الاسم</th><th>عدد الصلاحيات</th><th class="text-end">تحكم</th></tr></thead>
      <tbody>
        @foreach($roles as $r)
          <tr>
            <td>{{ $r->name }}</td>
            <td>{{ $r->permissions_count }}</td>
            <td class="text-end">
              <a href="{{ route('admin.roles.edit',$r) }}" class="btn btn-sm btn-outline-secondary">تعديل</a>
              <form class="d-inline" method="post" action="{{ route('admin.roles.destroy',$r) }}" onsubmit="return confirm('حذف الدور؟')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">حذف</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

    {{ $roles->links() }}
  </div>
</div>
@endsection