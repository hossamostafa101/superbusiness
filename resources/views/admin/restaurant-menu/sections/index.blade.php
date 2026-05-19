@extends('admin.layout.admin_app')

@section('title', 'أقسام قوالب المنيو')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1">أقسام قوالب المنيو</h1>
        <div class="text-muted">إدارة أقسام التصميم التي يختارها المطعم داخل المنيو.</div>
    </div>

    <a href="{{ route('admin.restaurant-menu-template-sections.create') }}" class="btn btn-primary">
        إضافة قسم
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.restaurant-menu-template-sections.index') }}" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">بحث</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="الاسم أو المفتاح">
            </div>

            <div class="col-md-4">
                <label class="form-label">نوع القسم</label>
                <select name="section_type" class="form-select">
                    <option value="">كل الأنواع</option>
                    <option value="hero" @selected(request('section_type') === 'hero')>Hero</option>
                    <option value="branch_switch" @selected(request('section_type') === 'branch_switch')>Branch Switch</option>
                    <option value="category_tabs" @selected(request('section_type') === 'category_tabs')>Category Tabs</option>
                    <option value="items" @selected(request('section_type') === 'items')>Items</option>
                    <option value="item_modal" @selected(request('section_type') === 'item_modal')>Item Modal</option>
                    <option value="cart" @selected(request('section_type') === 'cart')>Cart</option>
                    <option value="invoice" @selected(request('section_type') === 'invoice')>Invoice</option>
                    <option value="footer" @selected(request('section_type') === 'footer')>Footer</option>
                </select>
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-dark">بحث</button>
                <a href="{{ route('admin.restaurant-menu-template-sections.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                        <th>القسم</th>
                        <th>النوع</th>
                        <th>View</th>
                        <th>Premium</th>
                        <th>الحالة</th>
                        <th>الترتيب</th>
                        <th class="text-end">إجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($sections as $section)
                        <tr>
                            <td>
                                @if($section->previewImageUrl())
                                    <img src="{{ $section->previewImageUrl() }}" style="width:80px;height:52px;object-fit:cover;border-radius:10px;border:1px solid #e5e7eb;">
                                @else
                                    <div class="bg-light border rounded d-grid place-items-center" style="width:80px;height:52px;"></div>
                                @endif
                            </td>

                            <td>
                                <div class="fw-bold">{{ $section->name }}</div>
                                <div class="small text-muted">{{ $section->key }}</div>
                            </td>

                            <td>
                                <span class="badge bg-secondary">{{ $section->section_type }}</span>
                            </td>

                            <td dir="ltr" class="small">
                                {{ $section->config['view'] ?? '-' }}
                            </td>

                            <td>
                                @if($section->is_premium)
                                    <span class="badge bg-warning text-dark">Premium</span>
                                @else
                                    <span class="badge bg-light text-dark border">Free</span>
                                @endif
                            </td>

                            <td>
                                @if($section->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">غير نشط</span>
                                @endif
                            </td>

                            <td>{{ $section->sort_order }}</td>

                            <td class="text-end">
                                <a href="{{ route('admin.restaurant-menu-template-sections.edit', $section) }}" class="btn btn-sm btn-outline-primary">
                                    تعديل
                                </a>

                                <form method="POST" action="{{ route('admin.restaurant-menu-template-sections.destroy', $section) }}" class="d-inline" onsubmit="return confirm('حذف القسم؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                لا توجد أقسام.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $sections->links() }}
    </div>
</div>
@endsection