{{-- resources/views/app/restaurant-menu/content-sections/index.blade.php --}}
@extends('app.layouts.app')

@section('title', 'أقسام الصفحة')
@section('page_title', 'أقسام الصفحة')
@section('page_description', 'إدارة الأقسام الإضافية في المنيو مثل المميز والعروض ومجموعات الأصناف.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <strong>
            عدد الأقسام:
            {{ $sections->total() }}
        </strong>
    </div>

    <a href="{{ route('app.restaurant-menu.content-sections.create', $workspace) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        إضافة قسم
    </a>
</div>

<div class="card content-card mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('app.restaurant-menu.content-sections.index', $workspace) }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">نوع القسم</label>
                <select name="type" class="form-select">
                    <option value="">كل الأنواع</option>
                    <option value="featured_items" @selected(request('type') === 'featured_items')>أصناف مميزة</option>
                    <option value="item_collection" @selected(request('type') === 'item_collection')>مجموعة أصناف</option>
                    <option value="offers_slider" @selected(request('type') === 'offers_slider')>سلايدر عروض</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">الفرع</label>
                <select name="branch_id" class="form-select">
                    <option value="">كل الفروع / عام</option>

                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected((string) request('branch_id') === (string) $branch->id)>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <button type="submit" class="btn btn-dark">
                    بحث
                </button>

                <a href="{{ route('app.restaurant-menu.content-sections.index', $workspace) }}" class="btn btn-outline-secondary">
                    إعادة ضبط
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card content-card">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>القسم</th>
                        <th>النوع</th>
                        <th>الفرع</th>
                        <th>المحتوى</th>
                        <th>الخلفية</th>
                        <th>الظهور</th>
                        <th>الترتيب</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($sections as $section)
                        <tr>
                            <td>
                                <div class="fw-bold">
                                    {{ $section->title }}
                                </div>

                                @if($section->subtitle)
                                    <div class="small text-muted">
                                        {{ $section->subtitle }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $section->typeLabel() }}
                                </span>
                            </td>

                            <td>
                                @if($section->branch)
                                    {{ $section->branch->name }}
                                @else
                                    <span class="text-muted">عام لكل الفروع</span>
                                @endif
                            </td>

                            <td>
                                @if(in_array($section->type, ['featured_items', 'item_collection'], true))
                                    <span class="badge bg-primary">
                                        {{ $section->section_items_count }} صنف
                                    </span>
                                @elseif($section->type === 'offers_slider')
                                    <span class="badge bg-warning text-dark">
                                        {{ $section->offers_count }} عرض
                                    </span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span
                                        class="rounded-circle border"
                                        style="display:inline-block;width:24px;height:24px;background: {{ $section->cssBackground() }};"
                                    ></span>

                                    <small class="text-muted">
                                        {{ $section->background_type === 'gradient' ? 'Gradient' : 'Solid' }}
                                    </small>
                                </div>
                            </td>

                            <td>
                                @if($section->is_active)
                                    <span class="badge bg-success">ظاهر</span>
                                @else
                                    <span class="badge bg-danger">مخفي</span>
                                @endif

                                @if($section->starts_at || $section->ends_at)
                                    <div class="small text-muted mt-1">
                                        @if($section->starts_at)
                                            من {{ $section->starts_at->format('Y-m-d') }}
                                        @endif

                                        @if($section->ends_at)
                                            إلى {{ $section->ends_at->format('Y-m-d') }}
                                        @endif
                                    </div>
                                @endif
                            </td>

                            <td>
                                {{ $section->sort_order }}
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    @if($section->type === 'offers_slider')
                                        <a
                                            href="{{ route('app.restaurant-menu.content-sections.offers.index', [$workspace, $section]) }}"
                                            class="btn btn-sm btn-outline-warning"
                                        >
                                            العروض
                                        </a>
                                    @endif

                                    <a
                                        href="{{ route('app.restaurant-menu.content-sections.edit', [$workspace, $section]) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        تعديل
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('app.restaurant-menu.content-sections.destroy', [$workspace, $section]) }}"
                                        onsubmit="return confirm('هل تريد حذف هذا القسم؟')"
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
                            <td colspan="8" class="text-center text-muted py-4">
                                لا توجد أقسام بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $sections->links() }}
        </div>
    </div>
</div>
@endsection