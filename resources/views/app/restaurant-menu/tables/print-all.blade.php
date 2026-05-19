{{-- resources/views/app/restaurant-menu/tables/print-all.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة QR الطاولات</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        body {
            font-family: Tahoma, Arial, sans-serif;
            background: #f6f7fb;
            color: #111827;
            margin: 0;
            padding: 20px;
        }

        .print-actions {
            text-align: center;
            margin-bottom: 20px;
        }

        .print-actions button {
            border: 0;
            background: #2563eb;
            color: #fff;
            border-radius: 10px;
            padding: 10px 18px;
            font-weight: bold;
            cursor: pointer;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }

        .qr-card {
            background: #fff;
            border: 2px solid #111827;
            border-radius: 22px;
            padding: 18px;
            text-align: center;
            page-break-inside: avoid;
        }

        .brand {
            font-size: 20px;
            font-weight: 900;
            margin-bottom: 4px;
        }

        .branch {
            font-size: 14px;
            color: #4b5563;
            margin-bottom: 10px;
        }

        .table-name {
            border: 1px dashed #d1d5db;
            border-radius: 14px;
            padding: 10px;
            margin-bottom: 12px;
            font-size: 18px;
            font-weight: 900;
        }

        .qr-img {
            width: 190px;
            height: 190px;
            max-width: 100%;
        }

        .hint {
            margin-top: 10px;
            font-size: 13px;
            font-weight: 700;
        }

        .url {
            direction: ltr;
            word-break: break-all;
            color: #6b7280;
            font-size: 8px;
            margin-top: 8px;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .print-actions {
                display: none;
            }

            .qr-card {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

<div class="print-actions">
    <button onclick="window.print()">طباعة</button>
</div>

@if($tables->isEmpty())
    <div style="text-align:center; margin-top:80px;">
        لا توجد طاولات نشطة للطباعة.
    </div>
@else
    <div class="grid">
        @foreach($tables as $table)
            @php
                $publicUrl = route('public.restaurant-menu.branch', [
                    'workspace' => $workspace,
                    'branch' => $table->branch,
                ]) . '?table_code=' . urlencode($table->code);

                $qrImage = 'https://api.qrserver.com/v1/create-qr-code/?size=260x260&data=' . urlencode($publicUrl);
            @endphp

            <div class="qr-card">
                <div class="brand">
                    {{ $workspace->name }}
                </div>

                <div class="branch">
                    {{ $table->branch?->name }}
                </div>

                <div class="table-name">
                    {{ $table->name }}
                    <br>
                    <small>رقم {{ $table->number }}</small>
                </div>

                <img src="{{ $qrImage }}" class="qr-img" alt="QR">

                <div class="hint">
                    امسح الكود لعرض المنيو
                </div>

                <div class="url">
                    {{ $publicUrl }}
                </div>
            </div>
        @endforeach
    </div>
@endif

<script>
    window.addEventListener('load', function () {
        setTimeout(function () {
            window.print();
        }, 800);
    });
</script>

</body>
</html>