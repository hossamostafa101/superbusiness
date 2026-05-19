{{-- resources/views/billing/cancelled.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم إلغاء الدفع</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f6f7fb;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .cancel-card {
            border-radius: 20px;
            border: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card cancel-card text-center">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="bi bi-x-circle-fill text-danger" style="font-size: 64px;"></i>
                    </div>

                    <h1 class="h3 fw-bold mb-3">تم إلغاء الدفع</h1>

                    <p class="text-muted">
                        لم يتم إكمال عملية الدفع. يمكنك المحاولة مرة أخرى أو اختيار طريقة دفع أخرى.
                    </p>

                    <div class="mt-4 d-grid gap-2">
                        <a href="{{ route('billing.plans', $workspace) }}" class="btn btn-primary">
                            العودة إلى الباقات
                        </a>

                        <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                            العودة للرئيسية
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>