@extends('affiliate::dashboard.layout')

@section('title', 'روابطي')
@section('page_title', 'روابطي')
@section('page_description', 'روابط التسويق الخاصة بك حسب التخصص.')

@section('content')
<div class="card content-card mb-4">
    <div class="card-body p-4">
        <h2 class="h5 fw-bold mb-3">الرابط العام</h2>

        <div class="input-group" dir="ltr">
            <input
                type="text"
                class="form-control"
                value="{{ route('public.affiliate.track', $profile->code) }}"
                readonly
            >

            <button
                type="button"
                class="btn btn-dark"
                onclick="navigator.clipboard.writeText('{{ route('public.affiliate.track', $profile->code) }}')"
            >
                Copy
            </button>
        </div>
    </div>
</div>

<div class="card content-card">
    <div class="card-body p-4">
        <h2 class="h5 fw-bold mb-3">روابط التخصصات</h2>

        @forelse($links as $link)
            <div class="affiliate-link-card">
                <div class="flex-grow-1">
                    <div class="fw-bold">
                        {{ $link->title }}
                    </div>

                    <div class="small text-muted">
                        {{ $link->specification?->name ?: 'عام' }}
                    </div>

                    <div class="input-group mt-2" dir="ltr">
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $link->tracking_url ?: route('public.affiliate.track', $profile->code) . '?link_id=' . $link->id }}"
                            readonly
                        >

                        <button
                            type="button"
                            class="btn btn-dark"
                            onclick="navigator.clipboard.writeText('{{ $link->tracking_url ?: route('public.affiliate.track', $profile->code) . '?link_id=' . $link->id }}')"
                        >
                            Copy
                        </button>
                    </div>
                </div>

                <div class="link-stats">
                    <div>
                        <strong>{{ $link->clicks_count }}</strong>
                        <span>زيارات</span>
                    </div>

                    <div>
                        <strong>{{ $link->registrations_count }}</strong>
                        <span>تسجيلات</span>
                    </div>

                    <div>
                        <strong>{{ $link->conversions_count }}</strong>
                        <span>تحويلات</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-4">
                لا توجد روابط مخصصة بعد. استخدم الرابط العام حاليًا.
            </div>
        @endforelse
    </div>
</div>

<style>
    .affiliate-link-card {
        display: flex;
        align-items: center;
        gap: 18px;
        border: 1px solid #edf0f4;
        border-radius: 18px;
        padding: 16px;
        margin-bottom: 12px;
        background: #fff;
    }

    .affiliate-link-card:last-child {
        margin-bottom: 0;
    }

    .link-stats {
        display: flex;
        gap: 10px;
    }

    .link-stats > div {
        min-width: 86px;
        border-radius: 16px;
        background: #f8fafc;
        padding: 10px;
        text-align: center;
    }

    .link-stats strong {
        display: block;
        font-size: 20px;
        font-weight: 900;
    }

    .link-stats span {
        display: block;
        font-size: 12px;
        color: #64748b;
    }

    @media (max-width: 768px) {
        .affiliate-link-card {
            flex-direction: column;
            align-items: stretch;
        }

        .link-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }

        .link-stats > div {
            min-width: auto;
        }
    }
</style>
@endsection