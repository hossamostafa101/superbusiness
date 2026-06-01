<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $workspace->name }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 text-center">
            <h1 class="h3 fw-bold">
                {{ $workspace->name }}
            </h1>

            <p class="text-muted mb-0">
                الصفحة العامة للموديول الطبي.
            </p>
            <a href="{{ route('public.medical.booking.create', $workspace) }}" class="btn btn-primary">
    حجز موعد
</a>
        </div>
    </div>
</div>

</body>
</html>