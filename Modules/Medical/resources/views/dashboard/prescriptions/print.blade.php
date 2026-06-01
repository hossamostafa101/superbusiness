<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>روشتة {{ $prescription->prescription_number }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Cairo", Arial, sans-serif;
            margin: 0;
            color: #111827;
            background: #f3f4f6;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            padding: 18mm;
        }

        .header {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            align-items: flex-start;
            border-bottom: 2px solid #111827;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }

        .brand h1 {
            margin: 0 0 6px;
            font-size: 24px;
            font-weight: 900;
        }

        .brand p {
            margin: 0;
            color: #4b5563;
            font-size: 13px;
            line-height: 1.7;
        }

        .rx-meta {
            text-align: left;
            direction: ltr;
            font-size: 13px;
        }

        .rx-meta strong {
            display: block;
            font-size: 18px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 18px;
            margin-bottom: 18px;
            padding: 14px;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
        }

        .info-item span {
            display: block;
            color: #6b7280;
            font-size: 12px;
            margin-bottom: 3px;
        }

        .info-item strong {
            font-size: 14px;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 900;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e5e7eb;
        }

        .medicine {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 12px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .medicine-name {
            font-size: 16px;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .medicine-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            font-size: 12px;
        }

        .medicine-grid span {
            color: #6b7280;
        }

        .medicine-instructions {
            margin-top: 8px;
            font-size: 13px;
            line-height: 1.8;
        }

        .text-block {
            white-space: pre-line;
            line-height: 1.9;
            font-size: 14px;
        }

        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: end;
        }

        .signature {
            text-align: center;
            min-width: 180px;
            border-top: 1px solid #111827;
            padding-top: 8px;
            font-weight: 800;
        }

        .print-actions {
            position: fixed;
            top: 16px;
            left: 16px;
        }

        .print-actions button {
            border: 0;
            background: #111827;
            color: #fff;
            padding: 10px 16px;
            border-radius: 999px;
            font-weight: 800;
            cursor: pointer;
        }

        @media print {
            body {
                background: #fff;
            }

            .page {
                width: auto;
                min-height: auto;
                margin: 0;
                padding: 12mm;
            }

            .print-actions {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="print-actions">
    <button onclick="window.print()">
        طباعة
    </button>
</div>

<div class="page">
    <div class="header">
        <div class="brand">
            <h1>
                {{ $settings?->display_name ?: $workspace->name }}
            </h1>

            @if($settings?->description)
                <p>
                    {{ $settings->description }}
                </p>
            @endif
        </div>

        <div class="rx-meta">
            <strong>{{ $prescription->prescription_number }}</strong>
            <div>{{ $prescription->issued_at?->format('Y-m-d H:i') }}</div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-item">
            <span>المريض</span>
            <strong>{{ $prescription->patient?->full_name ?: $prescription->patient_name }}</strong>
        </div>

        <div class="info-item">
            <span>الطبيب</span>
            <strong>{{ $prescription->staff?->name ?: $prescription->staff_name ?: '-' }}</strong>
        </div>

        <div class="info-item">
            <span>رقم الزيارة</span>
            <strong>{{ $prescription->visit?->visit_number ?: '-' }}</strong>
        </div>

        <div class="info-item">
            <span>الحالة</span>
            <strong>{{ $prescription->statusLabel() }}</strong>
        </div>
    </div>

    @if($prescription->diagnosis_summary)
        <div class="section">
            <div class="section-title">ملخص التشخيص</div>
            <div class="text-block">{{ $prescription->diagnosis_summary }}</div>
        </div>
    @endif

    <div class="section">
        <div class="section-title">الأدوية</div>

        @forelse($prescription->items as $item)
            <div class="medicine">
                <div class="medicine-name">
                    {{ $item->medicine_name }}
                </div>

                <div class="medicine-grid">
                    <div>
                        <span>الجرعة</span>
                        <strong>{{ $item->dosage ?: '-' }}</strong>
                    </div>

                    <div>
                        <span>التكرار</span>
                        <strong>{{ $item->frequency ?: '-' }}</strong>
                    </div>

                    <div>
                        <span>المدة</span>
                        <strong>{{ $item->duration ?: '-' }}</strong>
                    </div>

                    <div>
                        <span>الطريقة</span>
                        <strong>{{ $item->route ?: '-' }}</strong>
                    </div>
                </div>

                @if($item->instructions)
                    <div class="medicine-instructions">
                        {{ $item->instructions }}
                    </div>
                @endif
            </div>
        @empty
            <div class="text-block">لا توجد أدوية.</div>
        @endforelse
    </div>

    @if($prescription->instructions)
        <div class="section">
            <div class="section-title">تعليمات عامة</div>
            <div class="text-block">{{ $prescription->instructions }}</div>
        </div>
    @endif

    @if($prescription->notes)
        <div class="section">
            <div class="section-title">ملاحظات</div>
            <div class="text-block">{{ $prescription->notes }}</div>
        </div>
    @endif

    <div class="footer">
        <div>
            <strong>
                {{ $settings?->display_name ?: $workspace->name }}
            </strong>
        </div>

        <div class="signature">
            توقيع الطبيب
        </div>
    </div>
</div>

</body>
</html>