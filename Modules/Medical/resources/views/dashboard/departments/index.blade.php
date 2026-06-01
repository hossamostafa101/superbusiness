@extends('app.layouts.app')

@section('title', 'الأقسام الطبية')
@section('page_title', 'الأقسام الطبية')
@section('page_description', 'إدارة أقسام المنشأة الطبية.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <strong>عدد الأقسام: {{ $departments->total() }}</strong>

    <a href="{{ route('app.medical.departments.create', $workspace) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        إضافة قسم
    </a>
</div>

<div class="card content-card">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>القسم</th>
                        <th>الفرع</th>
                        <th>الحالة</th>
                        <th>الترتيب</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($departments as $department)
                        <tr>
                            <td>
                                <div class="fw-bold">
                                    {{ $department->name }}
                                </div>

                                @if($department->description)
                                    <div class="small text-muted">
                                        {{ \Illuminate\Support\Str::limit($department->description, 80) }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                {{ $department->branch?->name ?: 'كل الفروع' }}
                            </td>

                            <td>
                                @if($department->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">متوقف</span>
                                @endif
                            </td>

                            <td>{{ $department->sort_order }}</td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('app.medical.departments.edit', [$workspace, $department]) }}" class="btn btn-sm btn-outline-primary">
                                        تعديل
                                    </a>

                                    <form method="POST" action="{{ route('app.medical.departments.destroy', [$workspace, $department]) }}" onsubmit="return confirm('حذف القسم؟')">
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
                            <td colspan="5" class="text-center text-muted py-4">
                                لا توجد أقسام بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $departments->links() }}
    </div>
</div>
@endsection