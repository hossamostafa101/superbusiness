@extends('app.layouts.app')

@section('title', 'التخصصات')
@section('page_title', 'التخصصات')
@section('page_description', 'إدارة تخصصات الأطباء والفريق الطبي.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <strong>عدد التخصصات: {{ $specialties->total() }}</strong>

    <a href="{{ route('app.medical.specialties.create', $workspace) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        إضافة تخصص
    </a>
</div>

<div class="card content-card">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>التخصص</th>
                        <th>الحالة</th>
                        <th>الترتيب</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($specialties as $specialty)
                        <tr>
                            <td>
                                <div class="fw-bold">
                                    {{ $specialty->name }}
                                </div>

                                @if($specialty->description)
                                    <div class="small text-muted">
                                        {{ \Illuminate\Support\Str::limit($specialty->description, 80) }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                @if($specialty->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">متوقف</span>
                                @endif
                            </td>

                            <td>{{ $specialty->sort_order }}</td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('app.medical.specialties.edit', [$workspace, $specialty]) }}" class="btn btn-sm btn-outline-primary">
                                        تعديل
                                    </a>

                                    <form method="POST" action="{{ route('app.medical.specialties.destroy', [$workspace, $specialty]) }}" onsubmit="return confirm('حذف التخصص؟')">
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
                            <td colspan="4" class="text-center text-muted py-4">
                                لا توجد تخصصات بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $specialties->links() }}
    </div>
</div>
@endsection