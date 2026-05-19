{{-- resources/views/public/booking/success.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم إرسال الحجز</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background: #f6f7fb;
            display: grid;
            place-items: center;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            padding: 24px;
        }

        .success-card {
            max-width: 520px;
            width: 100%;
            background: #fff;
            border-radius: 26px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 18px 50px rgba(0,0,0,.08);
        }
    </style>
</head>
<body>

<div class="success-card">
    <div class="p-5 text-center">
        <div class="mb-4">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 64px;"></i>
        </div>

        <h1 class="h3 fw-bold mb-3">تم إرسال طلب الحجز</h1>

        <p class="text-muted mb-4">
            تم إرسال طلبك بنجاح إلى {{ $profile->display_name }}.
            سيتم مراجعة الموعد والتواصل معك للتأكيد.
        </p>

        <div class="d-grid gap-2">
            <a href="{{ route('public.business-page.show', $workspace) }}" class="btn btn-primary">
                العودة للصفحة
            </a>

            @if($profile->whatsapp_number)
                @php
                    $number = preg_replace('/\D+/', '', $profile->whatsapp_number);
                    $message = urlencode('مرحبًا، قمت بإرسال طلب حجز وأريد المتابعة.');
                @endphp

                <a href="https://wa.me/{{ $number }}?text={{ $message }}" target="_blank" class="btn btn-outline-success">
                    متابعة عبر واتساب
                </a>
            @endif
        </div>
    </div>
</div>

</body>
</html>