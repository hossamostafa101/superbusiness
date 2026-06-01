@extends('app.layouts.app')

@section('title', 'الفروع الطبية')
@section('page_title', 'الفروع الطبية')
@section('page_description', 'إدارة فروع المنشأة الطبية.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <strong>
            عدد الفروع:
            {{ $branches->total() }}
        </strong>
    </div>

    <a href="{{ route('app.medical.branches.create', $workspace) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        إضافة فرع
    </a>
</div>

<div class="card content-card">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>الفرع</th>
                        <th>المدينة</th>
                        <th>الهاتف</th>
                        <th>الحالة</th>
                        <th>رئيسي</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($branches as $branch)
                        <tr>
                            <td>
                                <div class="fw-bold">
                                    {{ $branch->name }}
                                </div>

                                @if($branch->address)
                                    <div class="small text-muted">
                                        {{ \Illuminate\Support\Str::limit($branch->address, 70) }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                {{ $branch->city ?: '-' }}
                            </td>

                            <td dir="ltr">
                                {{ $branch->phone ?: '-' }}
                            </td>

                            <td>
                                @if($branch->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">متوقف</span>
                                @endif
                            </td>

                            <td>
                                @if($branch->is_main)
                                    <span class="badge bg-primary">رئيسي</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('app.medical.branches.edit', [$workspace, $branch]) }}" class="btn btn-sm btn-outline-primary">
                                        تعديل
                                    </a>

                                    <form method="POST" action="{{ route('app.medical.branches.destroy', [$workspace, $branch]) }}" onsubmit="return confirm('حذف الفرع؟')">
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
                                لا توجد فروع بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $branches->links() }}
    </div>
</div>
@endsection