<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>{{ $invitation->title ?? 'دعوة' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- تقدر تحط خطوطك هنا أو تستعمل app.css لو عندك --}}
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background-color: #f3f4f6;
        }

        .page-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: stretch;
            justify-content: center;
        }

        .invitation-page {
            width: 100%;
            max-width: 600px;
            margin: 16px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0,0,0,0.08);
            background: #ffffff;
        }

        .invitation-header {
            padding: 32px 24px 16px;
            text-align: center;
        }

        .invitation-body {
            padding: 16px 24px 24px;
        }

        .hero-image {
            width: 100%;
            max-height: 260px;
            object-fit: cover;
            display: block;
        }

        .names {
            font-size: 26px;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .divider {
            width: 80px;
            height: 2px;
            margin: 16px auto;
            opacity: .6;
        }

        .meta-row {
            margin-bottom: 8px;
            font-size: 14px;
        }

        .meta-label {
            font-weight: 600;
            margin-left: 4px;
        }

        .message-text {
            margin-top: 16px;
            line-height: 1.7;
            font-size: 15px;
        }

        .rsvp-box {
            margin-top: 24px;
            padding: 16px 16px 20px;
            border-radius: 18px;
            background: rgba(0,0,0,0.02);
        }

        .rsvp-box h3 {
            margin-top: 0;
            margin-bottom: 8px;
            font-size: 18px;
        }

        .rsvp-row {
            margin-bottom: 12px;
        }

        .rsvp-row label {
            display: block;
            font-size: 13px;
            margin-bottom: 4px;
        }

        .rsvp-row input[type="text"],
        .rsvp-row input[type="number"],
        .rsvp-row select,
        .rsvp-row textarea {
            width: 100%;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            padding: 8px 10px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .companions-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px;
        }

        @media (min-width: 480px) {
            .companions-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 10px 18px;
            border: none;
            font-size: 14px;
            cursor: pointer;
            color: #fff;
        }

        .rsvp-status {
            margin-top: 12px;
            font-size: 13px;
            padding: 8px 10px;
            border-radius: 10px;
        }

        .alert {
            margin: 12px 24px 0;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 13px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* اختلاف شكل التصميم حسب template_json['layout'] */

        /* default classic */
        .layout-classic .invitation-header {
            background: linear-gradient(135deg, #facc15 0%, #f97316 40%, #7c2d12 100%);
            color: #fff;
        }

        .layout-classic .divider {
            background-color: rgba(255,255,255,0.8);
        }

        /* minimal */
        .layout-minimal .invitation-header {
            background: #111827;
            color: #f9fafb;
        }

        .layout-minimal .invitation-page {
            border-radius: 18px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 30px rgba(15,23,42,0.35);
        }

        .layout-minimal .divider {
            background-color: #4b5563;
        }

        /* romantic */
        .layout-romantic .invitation-header {
            background: radial-gradient(circle at top, #f9a8d4 0%, #ec4899 40%, #831843 100%);
            color: #fff;
        }

        .layout-romantic .divider {
            background-color: rgba(255,255,255,0.85);
        }

    </style>
</head>
<body>
@php
    $layout      = $templateConfig['layout'] ?? 'classic'; // classic | minimal | romantic ...
    $primary     = $templateConfig['primary_color'] ?? '#C49A3A';
    $secondary   = $templateConfig['secondary_color'] ?? '#ffffff';
    $fontFamily  = $templateConfig['font_family'] ?? null;

    $date        = $invitation->event_date ? $invitation->event_date->format('Y-m-d') : null;
    $time        = $invitation->event_time ? substr((string) $invitation->event_time, 0, 5) : null;

    $namesLine   = trim(($invitation->groom_name ? $invitation->groom_name : '') . ' & ' . ($invitation->bride_name ? $invitation->bride_name : ''));
@endphp

<div class="page-wrapper" style="background-color: {{ $secondary }};">
    <div class="invitation-page layout-{{ $layout }}" style="{{ $fontFamily ? "font-family: {$fontFamily}, inherit;" : '' }}">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <div class="invitation-header" style="--primary: {{ $primary }};">
            @if ($invitation->hero_media_url)
                <img src="{{ $invitation->hero_media_url }}" alt="" class="hero-image">
            @endif

            <div style="margin-top: {{ $invitation->hero_media_url ? '16px' : '0' }};">
                <div style="font-size: 13px; opacity: .85;">
                    بطاقة دعوة لحضور
                </div>

                <div class="names">
                    {{ $namesLine !== '&' ? $namesLine : ($invitation->title ?? 'حفل الزفاف') }}
                </div>

                @if ($invitation->title)
                    <div style="font-size: 13px; opacity:.85; margin-top:4px;">
                        {{ $invitation->title }}
                    </div>
                @endif

                <div class="divider"></div>
            </div>
        </div>

        <div class="invitation-body">
            <div class="meta-row">
                <span class="meta-label">التاريخ:</span>
                <span>{{ $date ?? 'غير محدد' }}</span>
            </div>

            <div class="meta-row">
                <span class="meta-label">الوقت:</span>
                <span>{{ $time ? $time : 'غير محدد' }}</span>
            </div>

            <div class="meta-row">
                <span class="meta-label">المكان:</span>
                <span>
                    {{ $invitation->venue_name ?? 'غير محدد' }}
                    @if ($invitation->location_url)
                        - <a href="{{ $invitation->location_url }}" target="_blank" rel="noopener" style="color:#2563eb; text-decoration:none;">عرض على الخريطة</a>
                    @endif
                </span>
            </div>

            <div class="meta-row">
                <span class="meta-label">ضيفنا الكريم:</span>
                <span>{{ $guest->full_name }}</span>
                @if ($guest->tag)
                    <span style="font-size:12px; opacity:.8;"> ({{ $guest->tag }})</span>
                @endif
            </div>

            @if ($invitation->message_text)
                <div class="message-text">
                    {!! nl2br(e($invitation->message_text)) !!}
                </div>
            @endif

            {{-- مربع الـ RSVP --}}
            <div class="rsvp-box">
                <h3>تأكيد الحضور</h3>

                @if ($rsvp)
                    <div class="rsvp-status" style="background:#ecfdf5; color:#166534; border:1px solid #bbf7d0;">
                        @if ($rsvp->response === 'accepted')
                            تم تسجيل حضورك
                            @if ($rsvp->companions_count > 0)
                                ومعك {{ $rsvp->companions_count }} مرافق.
                            @endif
                        @else
                            تم تسجيل اعتذارك عن الحضور.
                        @endif
                        @if ($rsvp->responded_at)
                            <br>
                            <span style="font-size:12px; opacity:.8;">(بتاريخ {{ $rsvp->responded_at->format('Y-m-d H:i') }})</span>
                        @endif
                    </div>

                    <p style="font-size:12px; margin-top:8px; opacity:.8;">
                        يمكنك إرسال رد جديد ليتم استبدال الرد الحالي.
                    </p>
                @endif

                <form method="POST" action="{{ route('guest.invitation.rsvp', ['token' => $guest->invite_token]) }}">
                    @csrf

                    <div class="rsvp-row">
                        <label for="response">الرجاء اختيار ردك</label>
                        <select name="response" id="response" required>
                            <option value="accepted" {{ optional($rsvp)->response === 'accepted' ? 'selected' : '' }}>سأحضر</option>
                            <option value="declined" {{ optional($rsvp)->response === 'declined' ? 'selected' : '' }}>أعتذر عن الحضور</option>
                        </select>
                        @error('response')
                            <div style="color:#b91c1c; font-size:12px; margin-top:3px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="rsvp-row">
                        <label for="companions_count">عدد المرافقين (إن وجد)</label>
                        <input
                            type="number"
                            name="companions_count"
                            id="companions_count"
                            min="0"
                            max="20"
                            value="{{ old('companions_count', optional($rsvp)->companions_count ?? 0) }}"
                        >
                        @error('companions_count')
                            <div style="color:#b91c1c; font-size:12px; margin-top:3px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="rsvp-row">
                        <label>أسماء المرافقين (اختياري)</label>
                        <div class="companions-grid">
                            @php
                                $companionsOld = old('companions', optional($rsvp)->companions_json ?? []);
                                $companionsOld = is_array($companionsOld) ? $companionsOld : [];
                                $maxInputs = max(3, count($companionsOld));
                            @endphp

                            @for ($i = 0; $i < $maxInputs; $i++)
                                <input
                                    type="text"
                                    name="companions[]"
                                    placeholder="اسم المرافق {{ $i + 1 }}"
                                    value="{{ $companionsOld[$i] ?? '' }}"
                                >
                            @endfor
                        </div>
                        @error('companions.*')
                            <div style="color:#b91c1c; font-size:12px; margin-top:3px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="rsvp-row">
                        <label for="message">رسالة (اختياري)</label>
                        <textarea
                            name="message"
                            id="message"
                            rows="3"
                            placeholder="أي ملاحظة تود إضافتها..."
                        >{{ old('message', optional($rsvp)->message) }}</textarea>
                        @error('message')
                            <div style="color:#b91c1c; font-size:12px; margin-top:3px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="margin-top: 8px; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};">
                        <button type="submit" class="btn-primary" style="background-color: {{ $primary }};">
                            إرسال الرد
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
