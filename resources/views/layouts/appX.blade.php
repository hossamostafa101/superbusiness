<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Menu')</title>

  {{-- Bootstrap RTL + Icons --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  {{-- Font (اختياري) --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    :root{
      --brand:#0EA5E9;
      --brand-2:#22C55E;
      --bg:#0b1220;
      --card:#0f1a2f;
      --muted:#94a3b8;
      --text:#e2e8f0;
      --border:rgba(148,163,184,.18);
      --shadow: 0 12px 28px rgba(0,0,0,.25);
    }
    body{
      font-family: "Cairo", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      background: radial-gradient(1200px 400px at 80% -10%, rgba(14,165,233,.25), transparent 60%),
                  radial-gradient(900px 350px at 10% -20%, rgba(34,197,94,.18), transparent 55%),
                  #070b14;
      color: var(--text);
      min-height: 100vh;
    }
    .app-shell{max-width: 520px;}
    .glass{
      background: rgba(15,26,47,.78);
      border:1px solid var(--border);
      box-shadow: var(--shadow);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
    }
    .pill{
      border:1px solid var(--border);
      background: rgba(148,163,184,.08);
      color: var(--text);
      border-radius: 999px;
      padding: .55rem .9rem;
      font-weight: 600;
      white-space: nowrap;
    }
    .pill.active{
      background: linear-gradient(135deg, rgba(14,165,233,.28), rgba(34,197,94,.18));
      border-color: rgba(14,165,233,.35);
    }
    .search{
      border:1px solid var(--border);
      background: rgba(148,163,184,.06);
      color: var(--text);
    }
    .search::placeholder{color: rgba(148,163,184,.65);}
    .item-card{
      border:1px solid var(--border);
      background: rgba(15,26,47,.72);
      border-radius: 18px;
      overflow: hidden;
      transition: transform .12s ease, border-color .12s ease;
    }
    .item-card:hover{transform: translateY(-1px); border-color: rgba(14,165,233,.35);}
    .thumb{
      width: 74px; height: 74px;
      border-radius: 16px;
      background: linear-gradient(135deg, rgba(14,165,233,.25), rgba(34,197,94,.14));
      border:1px solid rgba(148,163,184,.18);
      display:flex; align-items:center; justify-content:center;
      color: rgba(226,232,240,.9);
      font-weight: 800;
      flex: 0 0 auto;
    }
    .muted{color: var(--muted);}
    .badge-soft{
      background: rgba(14,165,233,.18);
      border:1px solid rgba(14,165,233,.28);
      color: var(--text);
    }
    .btn-brand{
      background: linear-gradient(135deg, rgba(14,165,233,.95), rgba(34,197,94,.75));
      border:0;
      color:#041018;
      font-weight: 800;
    }
    .btn-ghost{
      background: rgba(148,163,184,.10);
      border:1px solid var(--border);
      color: var(--text);
    }
    .sticky-bottom-bar{
      position: fixed;
      left: 0; right: 0; bottom: 0;
      padding: 12px 12px 18px;
      background: linear-gradient(180deg, transparent, rgba(7,11,20,.85) 25%, rgba(7,11,20,.95));
      z-index: 1050;
    }
    .safe-space{height: 92px;}
    .divider{height:1px;background: var(--border);}
  </style>

  @stack('styles')
</head>
<body>

  <main class="container app-shell px-3 py-3">
    @yield('content')
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
