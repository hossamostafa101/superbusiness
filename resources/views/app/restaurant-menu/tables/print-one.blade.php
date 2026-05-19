{{-- resources/views/app/restaurant-menu/tables/print-one.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة QR - {{ $restaurantTable->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        @page {
            size: A5 portrait;
            margin: 12mm;
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

        .qr-card {
            background: #fff;
            border: 2px solid #111827;
            border-radius: 28px;
            padding: 28px;
            max-width: 460px;
            margin: 0 auto;
            text-align: center;
        }

        .brand {
            font-size: 28px;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .branch {
            font-size: 18px;
            color: #4b5563;
            margin-bottom: 18px;
        }

        .table-name {
            border: 2px dashed #d1d5db;
            border-radius: 18px;
            padding: 14px;
            margin-bottom: 22px;
            font-size: 24px;
            font-weight: 900;
        }

        .qr-img {
            width: 320px;
            height: 320px;
            max-width: 100%;
        }

        .hint {
            margin-top: 20px;
            font-size: 18px;
            font-weight: 700;
        }

        .url {
            direction: ltr;
            word-break: break-all;
            color: #6b7280;
            font-size: 11px;
            margin-top: 14px;
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
                border: 2px solid #111827;
                box-shadow: none;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

<div class="print-actions">
    <button onclick="window.print()">طباعة</button>
</div>

<div class="qr-card">
    <div class="brand">
        {{ $workspace->name }}
    </div>

    <div class="branch">
        {{ $restaurantTable->branch?->name }}
    </div>

    <div class="table-name">
        {{ $restaurantTable->name }}
        <br>
        <small>رقم {{ $restaurantTable->number }}</small>
    </div>

    <img src="{{ $qrImage }}" class="qr-img" alt="QR">

    <div class="hint">
        امسح الكود لعرض المنيو وطلبك من الطاولة
    </div>

    <div class="url">
        {{ $publicUrl }}
    </div>
</div>

<script>
    window.addEventListener('load', function () {
        setTimeout(function () {
            window.print();
        }, 600);
    });
</script>

</body>
</html>