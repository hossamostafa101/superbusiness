{{-- resources/views/admin/restaurant-menu/templates/index.blade.php --}}
@extends('admin.layout.admin_app')

@section('title', 'قوالب منيو المطاعم')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1">قوالب منيو المطاعم</h1>
        <div class="text-muted">
            إدارة القوالب الكاملة التي يستطيع المطعم اختيارها للمنيو العام.
        </div>
    </div>

    <a href="{{ route('admin.restaurant-menu-templates.create') }}" class="btn btn-primary">
        إضافة قالب
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.restaurant-menu-templates.index') }}" class="row g-3 align-items-end">
            <div class="col-md-8">
                <label class="form-label">بحث</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="اسم القالب أو المفتاح"
                >
            </div>

            <div class="col-md-4">
                <button type="submit" class="btn btn-dark">
                    بحث
                </button>

                <a href="{{ route('admin.restaurant-menu-templates.index') }}" class="btn btn-outline-secondary">
                    إعادة ضبط
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>الصورة</th>
                        <th>القالب</th>
                        <th>الأقسام</th>
                        <th>Premium</th>
                        <th>الحالة</th>
                        <th>الترتيب</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($templates as $template)
                        <tr>
                            <td>
                                @if($template->previewImageUrl())
                                    <img
                                        src="{{ $template->previewImageUrl() }}"
                                        alt="{{ $template->name }}"
                                        style="width:100px;height:64px;object-fit:cover;border-radius:12px;border:1px solid #e5e7eb;"
                                    >
                                @else
                                    <div
                                        class="bg-light border rounded"
                                        style="width:100px;height:64px;"
                                    ></div>
                                @endif
                            </td>

                            <td>
                                <div class="fw-bold">{{ $template->name }}</div>
                                <div class="small text-muted" dir="ltr">{{ $template->key }}</div>

                                @if($template->description)
                                    <div class="small text-muted mt-1">
                                        {{ \Illuminate\Support\Str::limit($template->description, 90) }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                @php
                                    $layout = $template->layout_config ?? [];
                                @endphp

                                <div class="d-flex gap-1 flex-wrap">
                                    @foreach(['hero','branch_switch','category_tabs','items','item_modal','cart','invoice','footer'] as $sectionKey)
                                        @if(!empty($layout[$sectionKey]))
                                            <span class="badge bg-light text-dark border" dir="ltr">
                                                {{ $layout[$sectionKey] }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </td>

                            <td>
                                @if($template->is_premium)
                                    <span class="badge bg-warning text-dark">Premium</span>
                                @else
                                    <span class="badge bg-light text-dark border">Free</span>
                                @endif
                            </td>

                            <td>
                                @if($template->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">غير نشط</span>
                                @endif
                            </td>

                            <td>{{ $template->sort_order }}</td>

                            <td class="text-end">
                                <a
                                    href="{{ route('admin.restaurant-menu-templates.edit', $template) }}"
                                    class="btn btn-sm btn-outline-primary"
                                >
                                    تعديل
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('admin.restaurant-menu-templates.destroy', $template) }}"
                                    class="d-inline"
                                    onsubmit="return confirm('هل تريد حذف هذا القالب؟')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-sm btn-outline-danger">
                                        حذف
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                لا توجد قوالب بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $templates->links() }}
    </div>
</div>
@endsection