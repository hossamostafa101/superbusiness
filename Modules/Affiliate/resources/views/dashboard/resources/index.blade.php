@extends('affiliate::dashboard.layout')

@section('title', 'أدوات التسويق')
@section('page_title', 'أدوات التسويق')
@section('page_description', 'نصوص وروابط وديموهات تساعدك في البيع.')

@section('content')
<div class="card content-card mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">النوع</label>

                <select name="type" class="form-select">
                    <option value="">كل الأنواع</option>
                    <option value="text" @selected(request('type') === 'text')>نص</option>
                    <option value="link" @selected(request('type') === 'link')>رابط</option>
                    <option value="video" @selected(request('type') === 'video')>فيديو</option>
                    <option value="image" @selected(request('type') === 'image')>صورة</option>
                    <option value="pdf" @selected(request('type') === 'pdf')>PDF</option>
                    <option value="demo" @selected(request('type') === 'demo')>ديمو</option>
                    <option value="whatsapp_script" @selected(request('type') === 'whatsapp_script')>نص واتساب</option>
                    <option value="other" @selected(request('type') === 'other')>أخرى</option>
                </select>
            </div>

            <div class="col-md-2 d-grid">
                <button class="btn btn-dark">
                    عرض
                </button>
            </div>

            <div class="col-md-2 d-grid">
                <a href="{{ route('affiliate.resources.index') }}" class="btn btn-light">
                    إعادة
                </a>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    @forelse($resources as $resource)
        <div class="col-md-6 col-xl-4">
            <div class="card content-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between gap-2 mb-3">
                        <div>
                            <h2 class="h6 fw-bold mb-1">
                                {{ $resource->title }}
                            </h2>

                            <div class="small text-muted">
                                {{ $resource->specification?->name ?: 'عام' }}
                            </div>
                        </div>

                        <span class="badge bg-light text-dark border">
                            {{ $resource->typeLabel() }}
                        </span>
                    </div>

                    @if($resource->description)
                        <p class="text-muted small">
                            {{ $resource->description }}
                        </p>
                    @endif

                    @if($resource->content)
                        <div class="resource-content">
                            {{ $resource->content }}
                        </div>

                        <button
                            type="button"
                            class="btn btn-sm btn-outline-dark mt-3"
                            onclick="navigator.clipboard.writeText(@js($resource->content))"
                        >
                            نسخ النص
                        </button>
                    @endif

                    @if($resource->url)
                        <div class="mt-3">
                            <a href="{{ $resource->url }}" target="_blank" class="btn btn-sm btn-primary">
                                فتح الرابط
                            </a>
                        </div>
                    @endif

                    @if($resource->file_path)
                        <div class="mt-3">
                            <a href="{{ asset('storage/' . $resource->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                تحميل الملف
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card content-card">
                <div class="card-body p-5 text-center text-muted">
                    لا توجد أدوات تسويق متاحة حاليًا.
                </div>
            </div>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $resources->links() }}
</div>

<style>
    .resource-content {
        white-space: pre-line;
        line-height: 1.8;
        border: 1px solid #edf0f4;
        border-radius: 16px;
        padding: 12px;
        background: #f8fafc;
        font-size: 14px;
    }
</style>
@endsection