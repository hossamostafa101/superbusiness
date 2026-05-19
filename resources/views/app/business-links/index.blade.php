{{-- resources/views/app/business-links/index.blade.php --}}
@extends('app.layouts.app')

@section('title', 'الروابط')
@section('page_title', 'الروابط')
@section('page_description', 'أضف روابط السوشيال، المتجر، اللوكيشن، أو أي رابط مهم لعملائك.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <strong>
            عدد الروابط:
            {{ $links->total() }}
            /
            {{ $isUnlimited ? 'غير محدود' : $linksLimit }}
        </strong>
    </div>

    <a href="{{ route('app.links.create', $workspace) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        إضافة رابط
    </a>
</div>

<div class="card content-card">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>العنوان</th>
                        <th>الرابط</th>
                        <th>الأيقونة</th>
                        <th>الترتيب</th>
                        <th>الحالة</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($links as $link)
                        <tr>
                            <td>{{ $link->id }}</td>

                            <td class="fw-semibold">{{ $link->title }}</td>

                            <td>
                                <a href="{{ $link->url }}" target="_blank">
                                    {{ \Illuminate\Support\Str::limit($link->url, 50) }}
                                </a>
                            </td>

                            <td>
                                @if($link->icon)
                                    <span class="badge bg-secondary">{{ $link->icon }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>{{ $link->sort_order }}</td>

                            <td>
                                @if($link->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">غير نشط</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('app.links.edit', [$workspace, $link]) }}" class="btn btn-sm btn-outline-primary">
                                        تعديل
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('app.links.destroy', [$workspace, $link]) }}"
                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا الرابط؟')"
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
                            <td colspan="7" class="text-center text-muted py-4">
                                لا توجد روابط بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $links->links() }}
        </div>
    </div>
</div>
@endsection