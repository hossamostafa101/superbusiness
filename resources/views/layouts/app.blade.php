<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Menu')</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    :root{
      --brand:#0EA5E9;
      --brand2:#22C55E;

      --bg:#eaf0ff;
      --panel: rgba(15,26,47,.70);
      --panel2: rgba(148,163,184,.08);
      --text:#e2e8f0;
      --muted:#94a3b8;
      --border: rgba(148,163,184,.16);
      --shadow: 0 14px 34px rgba(0,0,0,.30);
      --r: 18px;
    }

    body{
      font-family: "Cairo", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      background:
        radial-gradient(1200px 500px at 85% -15%, color-mix(in srgb, var(--brand) 25%, transparent), transparent 60%),
        radial-gradient(900px 380px at 10% -20%, rgba(34,197,94,.16), transparent 55%),
        var(--bg);
      color: var(--text);
      min-height: 100vh;
    }

    .menu-shell{ max-width: 560px; }

    .surface{
      background: var(--panel);
      border:1px solid var(--border);
      border-radius: var(--r);
      box-shadow: var(--shadow);
      /* أداء: blur أخف + fallback */
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
    }
    @supports not ((backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px))) {
      .surface{ background: rgba(15,26,47,.92); }
    }

    .muted{ color: var(--muted); }

    .chip{
      border:1px solid var(--border);
      background: var(--panel2);
      color: var(--text);
      border-radius: 999px;
      padding: .52rem .9rem;
      font-weight: 700;
      white-space: nowrap;
      transition: .12s ease;
    }
    .chip.active{
      background: linear-gradient(135deg, color-mix(in srgb, var(--brand) 28%, transparent), rgba(34,197,94,.14));
      border-color: color-mix(in srgb, var(--brand) 38%, transparent);
    }

    .search{
      background: rgba(148,163,184,.06);
      border:1px solid var(--border);
      color: var(--text);
      border-radius: 14px;
    }
    .search::placeholder{ color: rgba(148,163,184,.65); }

    .product{
      border:1px solid var(--border);
      background: rgba(15,26,47,.62);
      border-radius: var(--r);
      transition: transform .12s ease, border-color .12s ease;
    }
    .product:hover{ transform: translateY(-1px); border-color: color-mix(in srgb, var(--brand) 35%, transparent); }

    .thumb{
      width: 74px; height: 74px;
      border-radius: 16px;
      background: linear-gradient(135deg, color-mix(in srgb, var(--brand) 22%, transparent), rgba(34,197,94,.12));
      border:1px solid rgba(148,163,184,.16);
      display:flex; align-items:center; justify-content:center;
      font-weight: 900;
      color: rgba(226,232,240,.92);
      overflow:hidden;
      flex:0 0 auto;
    }
    .thumb img{ width:100%; height:100%; object-fit:cover; }

    .btn-brand{
      background: linear-gradient(135deg, color-mix(in srgb, var(--brand) 92%, #fff 0%), rgba(34,197,94,.72));
      border:0;
      color:#06111b;
      font-weight: 900;
      border-radius: 14px;
    }
    .btn-ghost{
      background: rgba(148,163,184,.10);
      border:1px solid var(--border);
      color: var(--text);
      border-radius: 14px;
    }

    .bottom-bar{
      position: fixed;
      left:0; right:0; bottom:0;
      z-index: 1050;
      padding: 12px 12px calc(env(safe-area-inset-bottom, 10px) + 10px);
      background: linear-gradient(180deg, transparent, rgba(7,11,20,.80) 20%, rgba(7,11,20,.95));
    }
    .safe-space{ height: 96px; }

    .soft-badge{
      background: rgba(14,165,233,.16);
      border:1px solid rgba(14,165,233,.26);
      color: var(--text);
    }

    /* لمسات صغيرة */
    .hr-soft{ height:1px; background: var(--border); border:0; margin: .75rem 0; }
  </style>

  @stack('styles')
</head>
<body>

  <main class="container menu-shell px-3 py-3">
    @yield('content')
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
