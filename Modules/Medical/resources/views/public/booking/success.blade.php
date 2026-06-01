<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم الحجز</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: "Cairo", system-ui, sans-serif;
            background: #f5f7fb;
        }

        .success-page {
            max-width: 520px;
            margin: 0 auto;
            padding: 28px 12px;
        }

        .success-card {
            border: 0;
            border-radius: 28px;
            box-shadow: 0 16px 40px rgba(15, 23, 42, .08);
        }

        .success-icon {
            width: 72px;
            height: 72px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            background: #dcfce7;
            color: #166534;
            font-size: 34px;
            margin: 0 auto 16px;
        }
    </style>
</head>
<body>

<div class="success-page">
    <div class="card success-card">
        <div class="card-body p-4 text-center">
            <div class="success-icon">
                ✓
            </div>

            <h1 class="h4 fw-bold mb-2">
                تم إرسال طلب الحجز
            </h1>

            <p class="text-muted">
                سنقوم بتأكيد الموعد قريبًا.
            </p>

            <div class="border rounded-4 p-3 text-start mt-4">
                <div class="mb-2">
                    <span class="text-muted">رقم الحجز:</span>
                    <strong dir="ltr">{{ $appointment->appointment_number }}</strong>
                </div>

                <div class="mb-2">
                    <span class="text-muted">الخدمة:</span>
                    <strong>{{ $appointment->service_name }}</strong>
                </div>

                <div class="mb-2">
                    <span class="text-muted">الموعد:</span>
                    <strong>
                        {{ $appointment->appointment_date?->format('Y-m-d') }}
                        -
                        {{ \Illuminate\Support\Carbon::parse($appointment->starts_at)->format('H:i') }}
                    </strong>
                </div>

                <div>
                    <span class="text-muted">الحالة:</span>
                    <strong>{{ $appointment->statusLabel() }}</strong>
                </div>
            </div>

            <a href="{{ route('public.medical.booking.create', $workspace) }}" class="btn btn-dark mt-4 rounded-pill px-4">
                حجز موعد آخر
            </a>
        </div>
    </div>
</div>

</body>
</html>