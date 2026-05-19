@extends('app.layouts.app')

@section('title', 'عروض القسم')
@section('page_title', 'عروض: ' . $contentSection->title)
@section('page_description', 'إدارة عروض سلايدر العروض داخل المنيو.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('app.restaurant-menu.content-sections.index', $workspace) }}" class="btn btn-light">
        رجوع للأقسام
    </a>

    <div class="d-flex gap-2">
    <a
        href="{{ route('public.restaurant-menu.workspace', $workspace) }}"
        target="_blank"
        class="btn btn-outline-primary"
    >
        معاينة المنيو
    </a>

    <a href="{{ route('app.restaurant-menu.content-sections.offers.create', [$workspace, $contentSection]) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        إضافة عرض
    </a>
</div>
</div>

<div class="card content-card">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>الصورة</th>
                        <th>العرض</th>
                        <th>الصنف المرتبط</th>
                        <th>السعر</th>
                        <th>الألوان</th>
                        <th>الحالة</th>
                        <th>الترتيب</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($offers as $offer)
                        <tr>
                            <td>
                                @if($offer->imageUrl())
                                    <img
                                        src="{{ $offer->imageUrl() }}"
                                        alt="{{ $offer->title }}"
                                        class="rounded border"
                                        style="width:90px;height:60px;object-fit:cover;"
                                    >
                                @else
                                    <div class="rounded border bg-light" style="width:90px;height:60px;"></div>
                                @endif
                            </td>

                            <td>
                                <div class="fw-bold">{{ $offer->title }}</div>

                                @if($offer->subtitle)
                                    <div class="small text-muted">{{ $offer->subtitle }}</div>
                                @endif

                                @if($offer->badge_text)
                                    <span class="badge bg-warning text-dark mt-1">
                                        {{ $offer->badge_text }}
                                    </span>
                                @endif
                            </td>

                            <td>
                                {{ $offer->item?->name ?: '-' }}
                            </td>

                            <td>
                                @if($offer->new_price)
                                    <strong>
                                        {{ number_format((float) $offer->new_price, 2) }}
                                        {{ $offer->currency }}
                                    </strong>

                                    @if($offer->old_price)
                                        <div class="small text-muted text-decoration-line-through">
                                            {{ number_format((float) $offer->old_price, 2) }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex gap-2">
                                    <span class="rounded-circle border" style="width:24px;height:24px;background:{{ $offer->background_color }};"></span>
                                    <span class="rounded-circle border" style="width:24px;height:24px;background:{{ $offer->text_color }};"></span>
                                </div>
                            </td>

                            <td>
                                @if($offer->is_active)
                                    <span class="badge bg-success">ظاهر</span>
                                @else
                                    <span class="badge bg-danger">مخفي</span>
                                @endif
                            </td>

                            <td>{{ $offer->sort_order }}</td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a
                                        href="{{ route('app.restaurant-menu.content-sections.offers.edit', [$workspace, $contentSection, $offer]) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        تعديل
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('app.restaurant-menu.content-sections.offers.destroy', [$workspace, $contentSection, $offer]) }}"
                                        onsubmit="return confirm('هل تريد حذف هذا العرض؟')"
                                    >
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
                            <td colspan="8" class="text-center text-muted py-4">
                                لا توجد عروض بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $offers->links() }}
    </div>
</div>
@endsection