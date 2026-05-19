{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    {{-- Basic --}}
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Primary SEO --}}
    <title>{{ config('app.name', 'Super Business') }} | صفحة بزنس ذكية بروابط ومنتجات وطلبات واتساب</title>
    <meta name="description" content="أنشئ صفحة بزنس ذكية لعرض روابطك ومنتجاتك وخدماتك واستقبال الطلبات والحجوزات عبر واتساب من رابط واحد.">
    <meta name="keywords" content="صفحة بزنس, ديجيتال بايو, واتساب, كتالوج منتجات, حجز مواعيد, CRM, روابط السوشيال, صفحة ذكية">
    <meta name="author" content="{{ config('app.name', 'Super Business') }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ config('app.name', 'Super Business') }} | صفحة بزنس ذكية">
    <meta property="og:description" content="اعرض روابطك ومنتجاتك وخدماتك واستقبل طلبات واتساب وحجوزات من صفحة واحدة.">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ config('app.name', 'Super Business') }}">
    <meta property="og:locale" content="ar_EG">
    <meta property="og:image" content="{{ asset('public/frontend/assets/img/og/super-business-og.jpg') }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ config('app.name', 'Super Business') }} | صفحة بزنس ذكية">
    <meta name="twitter:description" content="صفحة واحدة لروابطك، منتجاتك، حجوزاتك، وطلبات واتساب.">
    <meta name="twitter:image" content="{{ asset('public/frontend/assets/img/og/super-business-og.jpg') }}">

    {{-- Theme --}}
    <meta name="theme-color" content="#dbeafe">
    <meta name="msapplication-TileColor" content="#dbeafe">

    {{-- Favicons - عدّل المسارات لاحقًا حسب ملفاتك --}}
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- Bootstrap RTL + Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Structured Data --}}
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "{{ config('app.name', 'Super Business') }}",
            "applicationCategory": "BusinessApplication",
            "operatingSystem": "Web",
            "description": "منصة لإنشاء صفحة بزنس ذكية مع روابط ومنتجات وخدمات وحجوزات واتساب.",
            "offers": {
                "@type": "Offer",
                "priceCurrency": "EGP",
                "price": "0"
            }
        }
    </script>

    <style>
        :root {
            --sb-bg: #f8fbff;
            --sb-soft-blue: #eff6ff;
            --sb-soft-blue-2: #dbeafe;
            --sb-primary: #2563eb;
            --sb-primary-dark: #1d4ed8;
            --sb-primary-soft: rgba(37, 99, 235, .10);
            --sb-text: #0f172a;
            --sb-muted: #64748b;
            --sb-border: #dbe7f7;
            --sb-white: #ffffff;
            --sb-shadow: 0 22px 70px rgba(37, 99, 235, .10);
            --sb-radius: 24px;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            background:
                radial-gradient(circle at top right, rgba(37, 99, 235, .12), transparent 34%),
                radial-gradient(circle at bottom left, rgba(147, 197, 253, .18), transparent 30%),
                linear-gradient(180deg, #ffffff 0%, var(--sb-bg) 100%);
            color: var(--sb-text);
            font-family: "Cairo", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            line-height: 1.8;
            overflow-x: hidden;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .page-shell {
            position: relative;
            min-height: 100vh;
        }

        .landing-navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, .78);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(219, 231, 247, .75);
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 15px;
            display: grid;
            place-items: center;
            color: #fff;
            font-weight: 900;
            background: linear-gradient(135deg, #60a5fa, var(--sb-primary));
            box-shadow: 0 12px 30px rgba(37, 99, 235, .22);
        }

        .brand-name {
            font-weight: 900;
            letter-spacing: -.3px;
            color: var(--sb-text);
        }

        .nav-link-clean {
            color: var(--sb-muted);
            font-weight: 700;
            font-size: 14px;
            transition: .2s ease;
        }

        .nav-link-clean:hover {
            color: var(--sb-primary);
        }

        .btn-sb-primary {
            border: 0;
            border-radius: 999px;
            padding: 12px 22px;
            color: #fff;
            font-weight: 800;
            background: linear-gradient(135deg, #60a5fa, var(--sb-primary));
            box-shadow: 0 16px 34px rgba(37, 99, 235, .22);
            transition: .2s ease;
        }

        .btn-sb-primary:hover {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 20px 42px rgba(37, 99, 235, .26);
        }

        .btn-sb-light {
            border: 1px solid var(--sb-border);
            border-radius: 999px;
            padding: 12px 22px;
            color: var(--sb-text);
            font-weight: 800;
            background: rgba(255, 255, 255, .75);
            transition: .2s ease;
        }

        .btn-sb-light:hover {
            color: var(--sb-primary);
            border-color: rgba(37, 99, 235, .28);
            transform: translateY(-2px);
        }

        .hero-section {
            position: relative;
            padding: 88px 0 54px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: var(--sb-primary-soft);
            color: var(--sb-primary-dark);
            font-size: 13px;
            font-weight: 800;
            border: 1px solid rgba(37, 99, 235, .12);
            margin-bottom: 18px;
        }

        .hero-title {
            font-size: clamp(34px, 6vw, 64px);
            line-height: 1.22;
            letter-spacing: -1.2px;
            font-weight: 900;
            margin-bottom: 20px;
        }

        .hero-title span {
            color: var(--sb-primary);
        }

        .hero-subtitle {
            color: var(--sb-muted);
            font-size: clamp(16px, 2.4vw, 20px);
            max-width: 680px;
            margin: 0 auto 30px;
        }

        .hero-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 34px;
        }

        .trust-row {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .trust-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: var(--sb-muted);
            font-size: 13px;
            font-weight: 700;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .7);
            border: 1px solid rgba(219, 231, 247, .9);
        }

        .preview-wrap {
            padding: 22px;
            border-radius: 34px;
            background: rgba(255, 255, 255, .56);
            border: 1px solid rgba(219, 231, 247, .85);
            box-shadow: var(--sb-shadow);
            max-width: 1040px;
            margin: 46px auto 0;
        }

        .preview-card {
            overflow: hidden;
            border-radius: 26px;
            background: #fff;
            border: 1px solid var(--sb-border);
        }

        .preview-topbar {
            display: flex;
            gap: 8px;
            padding: 14px 16px;
            border-bottom: 1px solid var(--sb-border);
            background: #f8fbff;
        }

        .dot {
            width: 11px;
            height: 11px;
            border-radius: 999px;
            background: #bfdbfe;
        }

        .preview-body {
            display: grid;
            grid-template-columns: .8fr 1.2fr;
            gap: 0;
            min-height: 360px;
        }

        .phone-preview {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 34px;
            background: linear-gradient(180deg, #eff6ff, #ffffff);
            border-left: 1px solid var(--sb-border);
        }

        .phone-frame {
            width: 230px;
            border-radius: 34px;
            padding: 12px;
            background: #0f172a;
            box-shadow: 0 24px 60px rgba(15, 23, 42, .20);
        }

        .phone-screen {
            min-height: 430px;
            border-radius: 26px;
            overflow: hidden;
            background: #fff;
        }

        .phone-cover {
            height: 105px;
            background: linear-gradient(135deg, #93c5fd, #2563eb);
        }

        .phone-avatar {
            width: 64px;
            height: 64px;
            margin: -32px auto 10px;
            border-radius: 20px;
            display: grid;
            place-items: center;
            background: #fff;
            color: var(--sb-primary);
            box-shadow: 0 10px 24px rgba(15, 23, 42, .14);
        }

        .phone-line {
            height: 10px;
            border-radius: 999px;
            background: #e2e8f0;
            margin: 10px auto;
        }

        .phone-line.w1 {
            width: 55%;
        }

        .phone-line.w2 {
            width: 72%;
        }

        .phone-button {
            width: calc(100% - 30px);
            height: 42px;
            border-radius: 15px;
            margin: 14px auto;
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 13px;
            font-weight: 800;
            background: var(--sb-primary);
        }

        .preview-copy {
            padding: 42px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .preview-copy h2 {
            font-size: clamp(24px, 4vw, 38px);
            font-weight: 900;
            margin-bottom: 14px;
            letter-spacing: -.7px;
        }

        .preview-copy p {
            color: var(--sb-muted);
            margin-bottom: 0;
            max-width: 520px;
        }

        .section-placeholder {
            padding: 72px 0;
        }

        .placeholder-card {
            border: 1px dashed rgba(37, 99, 235, .25);
            border-radius: var(--sb-radius);
            background: rgba(255, 255, 255, .58);
            padding: 34px;
            text-align: center;
            color: var(--sb-muted);
        }

        .landing-footer {
            border-top: 1px solid var(--sb-border);
            padding: 26px 0;
            color: var(--sb-muted);
            background: rgba(255, 255, 255, .72);
        }

        @media (max-width: 991px) {
            .hero-section {
                padding-top: 62px;
            }

            .preview-body {
                grid-template-columns: 1fr;
            }

            .phone-preview {
                border-left: 0;
                border-bottom: 1px solid var(--sb-border);
            }

            .preview-copy {
                padding: 28px;
                text-align: center;
            }
        }

        @media (max-width: 575px) {
            .landing-navbar .container {
                padding-inline: 16px;
            }

            .hero-section {
                padding: 48px 0 36px;
            }

            .hero-actions .btn {
                width: 100%;
            }

            .preview-wrap {
                padding: 12px;
                border-radius: 26px;
                margin-top: 34px;
            }

            .phone-preview {
                padding: 22px 12px;
            }

            .phone-frame {
                width: 210px;
            }

            .preview-copy {
                padding: 24px 18px;
            }

            .section-placeholder {
                padding: 46px 0;
            }
        }




















        .section-heading {
    max-width: 760px;
    margin: 0 auto 34px;
    text-align: center;
}

.section-kicker {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--sb-primary-dark);
    background: var(--sb-primary-soft);
    border: 1px solid rgba(37, 99, 235, .12);
    border-radius: 999px;
    padding: 7px 13px;
    font-size: 13px;
    font-weight: 800;
    margin-bottom: 14px;
}

.section-title {
    font-size: clamp(26px, 4vw, 42px);
    font-weight: 900;
    line-height: 1.35;
    letter-spacing: -.8px;
    margin-bottom: 12px;
}

.section-description {
    color: var(--sb-muted);
    font-size: 17px;
    margin-bottom: 0;
}

.feature-card {
    height: 100%;
    padding: 26px;
    border-radius: var(--sb-radius);
    background: rgba(255, 255, 255, .78);
    border: 1px solid var(--sb-border);
    box-shadow: 0 14px 42px rgba(37, 99, 235, .06);
    transition: .22s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 55px rgba(37, 99, 235, .11);
    border-color: rgba(37, 99, 235, .22);
}

.feature-icon {
    width: 54px;
    height: 54px;
    border-radius: 18px;
    display: grid;
    place-items: center;
    color: var(--sb-primary);
    background: var(--sb-primary-soft);
    font-size: 24px;
    margin-bottom: 18px;
}

.feature-card h3 {
    font-size: 19px;
    font-weight: 900;
    margin-bottom: 10px;
}

.feature-card p {
    color: var(--sb-muted);
    margin-bottom: 0;
    font-size: 15px;
}

.audience-card {
    height: 100%;
    border-radius: 22px;
    padding: 22px;
    background: #fff;
    border: 1px solid var(--sb-border);
    display: flex;
    align-items: flex-start;
    gap: 14px;
    transition: .2s ease;
}

.audience-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 38px rgba(37, 99, 235, .08);
}

.audience-icon {
    width: 44px;
    height: 44px;
    border-radius: 15px;
    display: grid;
    place-items: center;
    flex-shrink: 0;
    background: #eff6ff;
    color: var(--sb-primary);
    font-size: 20px;
}

.audience-card h3 {
    font-size: 16px;
    font-weight: 900;
    margin-bottom: 4px;
}

.audience-card p {
    color: var(--sb-muted);
    font-size: 14px;
    margin-bottom: 0;
}

.cta-strip {
    position: relative;
    overflow: hidden;
    border-radius: 32px;
    padding: 38px;
    background:
        radial-gradient(circle at top left, rgba(255,255,255,.22), transparent 28%),
        linear-gradient(135deg, #60a5fa, var(--sb-primary));
    color: #fff;
    box-shadow: 0 24px 70px rgba(37, 99, 235, .22);
}

.cta-strip h2 {
    font-weight: 900;
    font-size: clamp(24px, 4vw, 38px);
    margin-bottom: 10px;
}

.cta-strip p {
    color: rgba(255,255,255,.86);
    margin-bottom: 0;
}

.cta-strip .btn {
    background: #fff;
    color: var(--sb-primary-dark);
    border-radius: 999px;
    padding: 12px 22px;
    font-weight: 900;
    border: 0;
}

.cta-strip .btn:hover {
    color: var(--sb-primary-dark);
    transform: translateY(-2px);
}

@media (max-width: 575px) {
    .feature-card,
    .audience-card {
        padding: 20px;
    }

    .cta-strip {
        padding: 28px 22px;
        text-align: center;
    }

    .cta-strip .btn {
        width: 100%;
        margin-top: 18px;
    }
}

































.how-card {
    position: relative;
    height: 100%;
    padding: 28px;
    border-radius: var(--sb-radius);
    background: #fff;
    border: 1px solid var(--sb-border);
    box-shadow: 0 14px 42px rgba(37, 99, 235, .06);
    overflow: hidden;
}

.how-card::before {
    content: "";
    position: absolute;
    width: 150px;
    height: 150px;
    border-radius: 999px;
    background: rgba(37, 99, 235, .07);
    top: -70px;
    left: -70px;
}

.how-number {
    position: relative;
    width: 52px;
    height: 52px;
    border-radius: 18px;
    display: grid;
    place-items: center;
    color: #fff;
    font-size: 20px;
    font-weight: 900;
    background: linear-gradient(135deg, #60a5fa, var(--sb-primary));
    box-shadow: 0 14px 30px rgba(37, 99, 235, .18);
    margin-bottom: 18px;
}

.how-card h3 {
    position: relative;
    font-size: 20px;
    font-weight: 900;
    margin-bottom: 10px;
}

.how-card p {
    position: relative;
    color: var(--sb-muted);
    font-size: 15px;
    margin-bottom: 18px;
}

.how-list {
    position: relative;
    display: grid;
    gap: 9px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.how-list li {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--sb-text);
    font-size: 14px;
    font-weight: 700;
}

.how-list i {
    color: var(--sb-primary);
}

.how-connector {
    display: none;
}

@media (min-width: 992px) {
    .how-steps-row {
        position: relative;
    }

    .how-connector {
        display: block;
        position: absolute;
        top: 70px;
        right: 16%;
        left: 16%;
        height: 2px;
        background: linear-gradient(90deg, transparent, rgba(37, 99, 235, .22), transparent);
        z-index: 0;
    }

    .how-steps-row > [class*="col-"] {
        position: relative;
        z-index: 1;
    }
}

.mobile-note-card {
    border-radius: 26px;
    padding: 26px;
    background: linear-gradient(180deg, #ffffff, #eff6ff);
    border: 1px solid var(--sb-border);
    box-shadow: 0 18px 50px rgba(37, 99, 235, .08);
}

.mobile-note-card h3 {
    font-weight: 900;
    font-size: 22px;
    margin-bottom: 10px;
}

.mobile-note-card p {
    color: var(--sb-muted);
    margin-bottom: 0;
}

.mobile-note-icon {
    width: 56px;
    height: 56px;
    border-radius: 20px;
    display: grid;
    place-items: center;
    background: var(--sb-primary-soft);
    color: var(--sb-primary);
    font-size: 26px;
    margin-bottom: 16px;
}

@media (max-width: 575px) {
    .how-card {
        padding: 22px;
    }

    .mobile-note-card {
        padding: 22px;
    }
}



























.pricing-toggle {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px;
    border-radius: 999px;
    background: rgba(255,255,255,.72);
    border: 1px solid var(--sb-border);
    margin-bottom: 28px;
}

.pricing-toggle span {
    padding: 9px 16px;
    border-radius: 999px;
    font-size: 14px;
    font-weight: 800;
    color: var(--sb-muted);
}

.pricing-toggle span.active {
    background: var(--sb-primary);
    color: #fff;
    box-shadow: 0 10px 26px rgba(37, 99, 235, .18);
}

.pricing-card {
    position: relative;
    height: 100%;
    border-radius: 28px;
    padding: 28px;
    background: rgba(255,255,255,.86);
    border: 1px solid var(--sb-border);
    box-shadow: 0 16px 46px rgba(37, 99, 235, .07);
    transition: .22s ease;
    overflow: hidden;
}

.pricing-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 24px 70px rgba(37, 99, 235, .12);
}

.pricing-card.featured {
    border-color: rgba(37, 99, 235, .32);
    box-shadow: 0 28px 80px rgba(37, 99, 235, .16);
}

.pricing-card.featured::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at top left, rgba(37, 99, 235, .12), transparent 28%),
        linear-gradient(180deg, rgba(239,246,255,.75), transparent 40%);
    pointer-events: none;
}

.pricing-badge {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 7px 12px;
    border-radius: 999px;
    background: var(--sb-primary-soft);
    color: var(--sb-primary-dark);
    font-size: 12px;
    font-weight: 900;
    margin-bottom: 18px;
}

.pricing-card.featured .pricing-badge {
    background: var(--sb-primary);
    color: #fff;
}

.pricing-name {
    position: relative;
    font-size: 22px;
    font-weight: 900;
    margin-bottom: 8px;
}

.pricing-desc {
    position: relative;
    color: var(--sb-muted);
    font-size: 15px;
    min-height: 54px;
    margin-bottom: 20px;
}

.pricing-price {
    position: relative;
    display: flex;
    align-items: baseline;
    gap: 6px;
    margin-bottom: 20px;
}

.pricing-price strong {
    font-size: 38px;
    line-height: 1;
    font-weight: 900;
    letter-spacing: -1px;
}

.pricing-price span {
    color: var(--sb-muted);
    font-size: 14px;
    font-weight: 700;
}

.pricing-features {
    position: relative;
    display: grid;
    gap: 11px;
    padding: 0;
    margin: 0 0 24px;
    list-style: none;
}

.pricing-features li {
    display: flex;
    align-items: flex-start;
    gap: 9px;
    color: var(--sb-text);
    font-size: 14px;
    font-weight: 700;
}

.pricing-features i {
    color: var(--sb-primary);
    margin-top: 3px;
}

.pricing-card .btn {
    position: relative;
    width: 100%;
}

.pricing-note {
    margin-top: 22px;
    color: var(--sb-muted);
    font-size: 14px;
    text-align: center;
}

.pricing-note strong {
    color: var(--sb-text);
}

@media (max-width: 575px) {
    .pricing-card {
        padding: 22px;
    }

    .pricing-desc {
        min-height: auto;
    }

    .pricing-price strong {
        font-size: 34px;
    }

    .pricing-toggle {
        width: 100%;
        justify-content: center;
    }

    .pricing-toggle span {
        flex: 1;
        text-align: center;
    }
}




















.faq-wrapper {
    max-width: 900px;
    margin: 0 auto;
}

.faq-item {
    border: 1px solid var(--sb-border);
    border-radius: 22px;
    background: rgba(255, 255, 255, .86);
    overflow: hidden;
    box-shadow: 0 12px 38px rgba(37, 99, 235, .05);
    margin-bottom: 14px;
}

.faq-button {
    width: 100%;
    border: 0;
    background: transparent;
    padding: 20px 22px;
    text-align: right;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    color: var(--sb-text);
    font-weight: 900;
    font-size: 16px;
}

.faq-button i {
    color: var(--sb-primary);
    transition: .2s ease;
}

.faq-button[aria-expanded="true"] i {
    transform: rotate(180deg);
}

.faq-body {
    padding: 0 22px 22px;
    color: var(--sb-muted);
    font-size: 15px;
}

.final-cta {
    position: relative;
    overflow: hidden;
    border-radius: 34px;
    padding: 48px 28px;
    background:
        radial-gradient(circle at top right, rgba(255,255,255,.25), transparent 30%),
        linear-gradient(135deg, #1d4ed8, #60a5fa);
    color: #fff;
    text-align: center;
    box-shadow: 0 28px 80px rgba(37, 99, 235, .24);
}

.final-cta h2 {
    font-size: clamp(26px, 5vw, 46px);
    font-weight: 900;
    line-height: 1.35;
    margin-bottom: 14px;
}

.final-cta p {
    color: rgba(255,255,255,.86);
    max-width: 680px;
    margin: 0 auto 24px;
    font-size: 17px;
}

.final-cta .btn {
    border-radius: 999px;
    padding: 13px 24px;
    font-weight: 900;
}

.final-cta .btn-white {
    background: #fff;
    color: var(--sb-primary-dark);
    border: 0;
}

.final-cta .btn-white:hover {
    color: var(--sb-primary-dark);
    transform: translateY(-2px);
}

.final-cta .btn-outline-white {
    background: transparent;
    color: #fff;
    border: 1px solid rgba(255,255,255,.48);
}

.final-cta .btn-outline-white:hover {
    background: rgba(255,255,255,.12);
    color: #fff;
    transform: translateY(-2px);
}

@media (max-width: 575px) {
    .faq-button {
        padding: 18px;
        font-size: 15px;
    }

    .faq-body {
        padding: 0 18px 18px;
    }

    .final-cta {
        padding: 36px 20px;
    }

    .final-cta .btn {
        width: 100%;
    }
}
    </style>

    @stack('head')
</head>

<body>
<div class="page-shell">

    {{-- Header --}}
    <header class="landing-navbar">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a href="{{ route('home') }}" class="navbar-brand d-flex align-items-center gap-2 m-0">
                    <span class="brand-mark">SB</span>
                    <span class="brand-name">{{ config('app.name', 'Super Business') }}</span>
                </a>

                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#landingNavbar" aria-controls="landingNavbar" aria-expanded="false" aria-label="فتح القائمة">
                    <i class="bi bi-list fs-2"></i>
                </button>

                <div class="collapse navbar-collapse" id="landingNavbar">
                    <ul class="navbar-nav mx-auto gap-lg-3 py-3 py-lg-0">
                        <li class="nav-item">
                            <a class="nav-link nav-link-clean" href="#features">المميزات</a>
                        </li>
                        <li class="nav-item">
    <a class="nav-link nav-link-clean" href="#audience">مناسب لمين</a>
</li>

                        <li class="nav-item">
                            <a class="nav-link nav-link-clean" href="#how-it-works">طريقة العمل</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-clean" href="#pricing">الأسعار</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-clean" href="#faq">الأسئلة</a>
                        </li>
                    </ul>

                    <div class="d-flex gap-2 flex-column flex-lg-row">
                        @auth
                            @php
                                $firstWorkspace = auth()->user()
                                    ->ownedWorkspaces()
                                    ->latest('id')
                                    ->first();
                            @endphp

                            @if($firstWorkspace)
                                <a href="{{ route('app.business-profile.edit', $firstWorkspace) }}" class="btn btn-sb-light">
                                    لوحة التحكم
                                </a>
                            @else
                                <a href="{{ route('onboarding.create') }}" class="btn btn-sb-light">
                                    إعداد الصفحة
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-sb-light">
                                تسجيل الدخول
                            </a>

                            <a href="{{ route('register') }}" class="btn btn-sb-primary">
                                ابدأ الآن
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
    </header>

    {{-- Hero --}}
    <main>
        <section class="hero-section">
            <div class="container text-center">
                <div class="hero-badge">
                    <i class="bi bi-stars"></i>
                    صفحة واحدة لإدارة حضورك الرقمي
                </div>

                <h1 class="hero-title">
                    صفحة بزنس ذكية<br>
                    تجمع <span>روابطك ومنتجاتك وحجوزاتك</span>
                </h1>

                <p class="hero-subtitle">
                    اعرض بيانات نشاطك، روابط السوشيال، الكتالوج، الخدمات، واستقبل طلبات واتساب وحجوزات العملاء من رابط واحد مناسب للموبايل.
                </p>

                <div class="hero-actions">
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-sb-primary">
                            ابدأ مجانًا
                            <i class="bi bi-arrow-left-short"></i>
                        </a>

                        <a href="#preview" class="btn btn-sb-light">
                            شاهد الشكل
                        </a>
                    @else
                        <a href="{{ route('onboarding.create') }}" class="btn btn-sb-primary">
                            أنشئ صفحة بزنس
                            <i class="bi bi-arrow-left-short"></i>
                        </a>
                    @endguest
                </div>

                <div class="trust-row">
                    <span class="trust-pill">
                        <i class="bi bi-phone"></i>
                        مناسب للموبايل
                    </span>
                    <span class="trust-pill">
                        <i class="bi bi-search"></i>
                        جاهز للـ SEO
                    </span>
                    <span class="trust-pill">
                        <i class="bi bi-whatsapp"></i>
                        واتساب مباشر
                    </span>
                </div>

                <div class="preview-wrap" id="preview">
                    <div class="preview-card">
                        <div class="preview-topbar">
                            <span class="dot"></span>
                            <span class="dot"></span>
                            <span class="dot"></span>
                        </div>

                        <div class="preview-body">
                            <div class="phone-preview">
                                <div class="phone-frame">
                                    <div class="phone-screen">
                                        <div class="phone-cover"></div>
                                        <div class="phone-avatar">
                                            <i class="bi bi-shop fs-3"></i>
                                        </div>
                                        <div class="phone-line w1"></div>
                                        <div class="phone-line w2"></div>
                                        <div class="phone-button">
                                            <i class="bi bi-whatsapp ms-1"></i>
                                            اطلب عبر واتساب
                                        </div>
                                        <div class="phone-line w2"></div>
                                        <div class="phone-line w1"></div>
                                        <div class="phone-button" style="background:#60a5fa;">
                                            احجز موعد
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="preview-copy">
                                <h2>قاعدة جاهزة للتسويق والإعلانات</h2>
                                <p>
                                    هذا التصميم هو الأساس فقط. الأقسام التفصيلية مثل المميزات، طريقة العمل، الأسعار، الأسئلة الشائعة، وتجارب العملاء سيتم إضافتها كأقسام مستقلة لاحقًا.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Sections will be added later --}}
        {{-- Features --}}
<section id="features" class="section-placeholder">
    <div class="container">
        <div class="section-heading">
            <div class="section-kicker">
                <i class="bi bi-lightning-charge"></i>
                المميزات الأساسية
            </div>

            <h2 class="section-title">
                كل ما يحتاجه صاحب البزنس في رابط واحد
            </h2>

            <p class="section-description">
                صفحة ذكية تساعدك تعرض نشاطك بشكل احترافي، وتحوّل الزوار إلى عملاء محتملين بدون تعقيد.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-person-badge"></i>
                    </div>

                    <h3>صفحة بزنس احترافية</h3>
                    <p>
                        اعرض اسم النشاط، الوصف، بيانات التواصل، اللوكيشن، والشعار في صفحة مناسبة للموبايل.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-link-45deg"></i>
                    </div>

                    <h3>روابطك في مكان واحد</h3>
                    <p>
                        أضف روابط واتساب، انستجرام، فيسبوك، تيك توك، المتجر، أو أي رابط مهم لعملائك.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>

                    <h3>كتالوج منتجات وخدمات</h3>
                    <p>
                        اعرض منتجاتك أو خدماتك بصور وأسعار وتصنيفات، مع زر طلب مباشر عبر واتساب.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>

                    <h3>حجز مواعيد</h3>
                    <p>
                        اسمح للعملاء بإرسال طلب حجز من الصفحة العامة، مع إدارة المواعيد من لوحة التحكم.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-people"></i>
                    </div>

                    <h3>CRM مبسط</h3>
                    <p>
                        احتفظ ببيانات العملاء، مصادرهم، ملاحظاتهم، ومواعيدهم في مكان واحد منظم.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>

                    <h3>تتبع التفاعل</h3>
                    <p>
                        اعرف عدد الضغطات على واتساب، الروابط، والمنتجات الأكثر اهتمامًا من العملاء.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- Audience --}}
<section id="audience" class="section-placeholder pt-0">
    <div class="container">
        <div class="section-heading">
            <div class="section-kicker">
                <i class="bi bi-bullseye"></i>
                مناسب لمين؟
            </div>

            <h2 class="section-title">
                مصمم للبزنس الصغير الذي يعتمد على واتساب والسوشيال
            </h2>

            <p class="section-description">
                مناسب لأي نشاط يريد صفحة سريعة، واضحة، وسهلة المشاركة مع العملاء.
            </p>
        </div>

        <div class="row g-3">
            <div class="col-md-6 col-lg-4">
                <div class="audience-card">
                    <div class="audience-icon">
                        <i class="bi bi-bag"></i>
                    </div>

                    <div>
                        <h3>المتاجر الصغيرة</h3>
                        <p>ملابس، إكسسوارات، هاند ميد، منتجات منزلية، أو أي منتجات تباع عبر السوشيال.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="audience-card">
                    <div class="audience-icon">
                        <i class="bi bi-scissors"></i>
                    </div>

                    <div>
                        <h3>الصالونات ومراكز التجميل</h3>
                        <p>عرض الخدمات والأسعار واستقبال طلبات الحجز بشكل منظم.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="audience-card">
                    <div class="audience-icon">
                        <i class="bi bi-heart-pulse"></i>
                    </div>

                    <div>
                        <h3>العيادات والخدمات الصحية</h3>
                        <p>صفحة تعريفية وحجز مواعيد واستفسارات من المرضى والعملاء.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="audience-card">
                    <div class="audience-icon">
                        <i class="bi bi-cup-hot"></i>
                    </div>

                    <div>
                        <h3>الكافيهات والمطاعم الخفيفة</h3>
                        <p>عرض منيو مختصر، اللوكيشن، أوقات العمل، والتواصل السريع.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="audience-card">
                    <div class="audience-icon">
                        <i class="bi bi-briefcase"></i>
                    </div>

                    <div>
                        <h3>مقدمي الخدمات</h3>
                        <p>مصممين، مدربين، مستشارين، فنيين، ومديري خدمات محلية.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="audience-card">
                    <div class="audience-icon">
                        <i class="bi bi-megaphone"></i>
                    </div>

                    <div>
                        <h3>صناع المحتوى</h3>
                        <p>تجميع روابط التواصل، العروض، الخدمات، والمنتجات الرقمية في صفحة واحدة.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>







{{-- Small CTA --}}
<section class="section-placeholder pt-0">
    <div class="container">
        <div class="cta-strip">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h2>ابدأ بصفحة بسيطة، ثم طوّرها حسب احتياج نشاطك</h2>
                    <p>
                        يمكنك البدء بروابطك وبياناتك الأساسية، ثم إضافة المنتجات، الخدمات، الحجوزات، والعملاء لاحقًا.
                    </p>
                </div>

                <div class="col-lg-4 text-lg-end">
                    @guest
                        <a href="{{ route('register') }}" class="btn">
                            إنشاء حساب مجاني
                            <i class="bi bi-arrow-left-short"></i>
                        </a>
                    @else
                        <a href="{{ route('onboarding.create') }}" class="btn">
                            إعداد صفحة البزنس
                            <i class="bi bi-arrow-left-short"></i>
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</section>












        {{-- How It Works --}}
<section id="how-it-works" class="section-placeholder pt-0">
    <div class="container">
        <div class="section-heading">
            <div class="section-kicker">
                <i class="bi bi-magic"></i>
                طريقة العمل
            </div>

            <h2 class="section-title">
                من أول تسجيل إلى أول عميل في خطوات بسيطة
            </h2>

            <p class="section-description">
                لا تحتاج موقع كامل أو إعدادات معقدة. جهّز صفحة البزنس، شارك الرابط، واستقبل الطلبات أو الحجوزات.
            </p>
        </div>

        <div class="row g-4 how-steps-row">
            <span class="how-connector"></span>

            <div class="col-lg-4">
                <div class="how-card">
                    <div class="how-number">1</div>

                    <h3>أنشئ حسابك</h3>

                    <p>
                        ابدأ بحساب بسيط، ثم أضف اسم نشاطك ورقم واتساب لتجهيز صفحة البزنس الأولى.
                    </p>

                    <ul class="how-list">
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            تسجيل سريع
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            إنشاء مساحة عمل
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            رابط مخصص للنشاط
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="how-card">
                    <div class="how-number">2</div>

                    <h3>أضف محتواك</h3>

                    <p>
                        أضف روابطك، منتجاتك، خدماتك، أوقات الحجز، وبيانات التواصل من لوحة تحكم سهلة.
                    </p>

                    <ul class="how-list">
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            روابط السوشيال
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            منتجات وخدمات
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            إعدادات الحجز
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="how-card">
                    <div class="how-number">3</div>

                    <h3>شارك الرابط</h3>

                    <p>
                        ضع الرابط في البايو أو الإعلانات أو الرسائل، واجعل العملاء يتواصلون أو يحجزون مباشرة.
                    </p>

                    <ul class="how-list">
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            واتساب مباشر
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            طلبات وحجوزات
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            تحليلات وتفاعل
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mobile-note-card mt-4 mt-lg-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <div class="mobile-note-icon">
                        <i class="bi bi-phone"></i>
                    </div>

                    <h3>مصمم أساسًا للموبايل</h3>

                    <p>
                        أغلب العملاء يفتحون الرابط من واتساب أو انستجرام أو فيسبوك، لذلك الصفحة خفيفة وسريعة وواضحة على شاشة الموبايل.
                    </p>
                </div>

                <div class="col-lg-5">
                    <div class="d-grid gap-2">
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-sb-primary">
                                ابدأ الآن
                                <i class="bi bi-arrow-left-short"></i>
                            </a>

                            <a href="#pricing" class="btn btn-sb-light">
                                عرض الباقات
                            </a>
                        @else
                            <a href="{{ route('onboarding.create') }}" class="btn btn-sb-primary">
                                إعداد صفحة البزنس
                                <i class="bi bi-arrow-left-short"></i>
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

       {{-- Pricing --}}
<section id="pricing" class="section-placeholder pt-0">
    <div class="container">
        <div class="section-heading">
            <div class="section-kicker">
                <i class="bi bi-credit-card"></i>
                الأسعار
            </div>

            <h2 class="section-title">
                باقات بسيطة تبدأ مجانًا وتكبر مع نشاطك
            </h2>

            <p class="section-description">
                ابدأ بصفحة أساسية، ثم انتقل إلى باقة أكبر عندما تحتاج منتجات أكثر، حجوزات، وتحليلات أفضل.
            </p>
        </div>

        <div class="text-center">
            <div class="pricing-toggle">
                <span class="active">شهري</span>
                <span>سنوي وفر أكثر</span>
            </div>
        </div>

        <div class="row g-4 align-items-stretch">
            <div class="col-lg-4">
                <div class="pricing-card">
                    <div class="pricing-badge">
                        <i class="bi bi-seedling"></i>
                        بداية مجانية
                    </div>

                    <h3 class="pricing-name">Free</h3>

                    <p class="pricing-desc">
                        مناسبة للتجربة أو لصفحة تعريفية بسيطة لنشاطك.
                    </p>

                    <div class="pricing-price">
                        <strong>0</strong>
                        <span>جنيه / شهر</span>
                    </div>

                    <ul class="pricing-features">
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            صفحة بزنس عامة
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            روابط أساسية
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            عدد محدود من المنتجات
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            تتبع بسيط للضغطات
                        </li>
                    </ul>

                    @guest
                        <a href="{{ route('register') }}" class="btn btn-sb-light">
                            ابدأ مجانًا
                        </a>
                    @else
                        <a href="{{ route('onboarding.create') }}" class="btn btn-sb-light">
                            إعداد الصفحة
                        </a>
                    @endguest
                </div>
            </div>

            <div class="col-lg-4">
                <div class="pricing-card featured">
                    <div class="pricing-badge">
                        <i class="bi bi-stars"></i>
                        الأكثر مناسبة للبداية
                    </div>

                    <h3 class="pricing-name">Starter</h3>

                    <p class="pricing-desc">
                        لأصحاب الأنشطة الذين يريدون صفحة أفضل مع كتالوج وحجوزات.
                    </p>

                    <div class="pricing-price">
                        <strong>150</strong>
                        <span>جنيه / شهر</span>
                    </div>

                    <ul class="pricing-features">
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            كل مميزات الباقة المجانية
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            منتجات وخدمات أكثر
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            تفعيل طلبات الحجز
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            CRM مبسط للعملاء
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            تحليلات أفضل للتفاعل
                        </li>
                    </ul>

                    @guest
                        <a href="{{ route('register') }}" class="btn btn-sb-primary">
                            جرّب Starter
                            <i class="bi bi-arrow-left-short"></i>
                        </a>
                    @else
                        <a href="{{ route('onboarding.create') }}" class="btn btn-sb-primary">
                            ترقية لاحقًا من اللوحة
                            <i class="bi bi-arrow-left-short"></i>
                        </a>
                    @endguest
                </div>
            </div>

            <div class="col-lg-4">
                <div class="pricing-card">
                    <div class="pricing-badge">
                        <i class="bi bi-graph-up-arrow"></i>
                        للنمو
                    </div>

                    <h3 class="pricing-name">Growth</h3>

                    <p class="pricing-desc">
                        للأنشطة التي لديها منتجات أكثر وتحتاج إدارة عملاء ومواعيد بشكل أوسع.
                    </p>

                    <div class="pricing-price">
                        <strong>300</strong>
                        <span>جنيه / شهر</span>
                    </div>

                    <ul class="pricing-features">
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            حدود أعلى للمنتجات والخدمات
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            عملاء ومواعيد أكثر
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            تقويم المواعيد
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            تتبع المنتجات والروابط
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            مناسب للإعلانات والحملات
                        </li>
                    </ul>

                    @guest
                        <a href="{{ route('register') }}" class="btn btn-sb-light">
                            ابدأ الآن
                        </a>
                    @else
                        <a href="{{ route('onboarding.create') }}" class="btn btn-sb-light">
                            عرض الباقات من اللوحة
                        </a>
                    @endguest
                </div>
            </div>
        </div>

        {{-- <p class="pricing-note">
            <strong>ملاحظة:</strong>
            الأسعار قابلة للتعديل من لوحة الإدارة، ويمكن إضافة خصومات أو تجربة مجانية حسب خطة الإطلاق.
        </p> --}}
    </div>
</section>

        
{{-- FAQ --}}
<section id="faq" class="section-placeholder pt-0">
    <div class="container">
        <div class="section-heading">
            <div class="section-kicker">
                <i class="bi bi-question-circle"></i>
                الأسئلة الشائعة
            </div>

            <h2 class="section-title">
                أسئلة تساعدك تبدأ بثقة
            </h2>

            <p class="section-description">
                إجابات مختصرة عن طريقة استخدام المنصة، الباقات، واتساب، والصفحة العامة.
            </p>
        </div>

        <div class="faq-wrapper" id="faqAccordion">
            <div class="faq-item">
                <button
                    class="faq-button"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#faqOne"
                    aria-expanded="true"
                    aria-controls="faqOne"
                >
                    هل أحتاج موقع إلكتروني كامل؟
                    <i class="bi bi-chevron-down"></i>
                </button>

                <div id="faqOne" class="collapse show" data-bs-parent="#faqAccordion">
                    <div class="faq-body">
                        لا. الفكرة أن تبدأ بصفحة بزنس ذكية وسريعة تجمع بياناتك وروابطك ومنتجاتك وحجوزاتك في رابط واحد، بدون تعقيد موقع كامل.
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button
                    class="faq-button collapsed"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#faqTwo"
                    aria-expanded="false"
                    aria-controls="faqTwo"
                >
                    هل الصفحة مناسبة للموبايل؟
                    <i class="bi bi-chevron-down"></i>
                </button>

                <div id="faqTwo" class="collapse" data-bs-parent="#faqAccordion">
                    <div class="faq-body">
                        نعم. التصميم مبني أساسًا للموبايل لأن أغلب العملاء يفتحون الروابط من واتساب، إنستجرام، فيسبوك، أو الإعلانات.
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button
                    class="faq-button collapsed"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#faqThree"
                    aria-expanded="false"
                    aria-controls="faqThree"
                >
                    هل أقدر أضيف منتجات وخدمات؟
                    <i class="bi bi-chevron-down"></i>
                </button>

                <div id="faqThree" class="collapse" data-bs-parent="#faqAccordion">
                    <div class="faq-body">
                        نعم. يمكنك إضافة منتجات أو خدمات مع صور وأسعار وتصنيفات، ويظهر لكل منتج أو خدمة زر تواصل أو طلب عبر واتساب.
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button
                    class="faq-button collapsed"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#faqFour"
                    aria-expanded="false"
                    aria-controls="faqFour"
                >
                    هل يوجد نظام حجز مواعيد؟
                    <i class="bi bi-chevron-down"></i>
                </button>

                <div id="faqFour" class="collapse" data-bs-parent="#faqAccordion">
                    <div class="faq-body">
                        نعم. يمكنك تفعيل الحجز، تحديد أيام وساعات العمل، واستقبال طلبات الحجز من الصفحة العامة، ثم إدارة المواعيد من لوحة التحكم.
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button
                    class="faq-button collapsed"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#faqFive"
                    aria-expanded="false"
                    aria-controls="faqFive"
                >
                    هل المنصة مرتبطة بواتساب؟
                    <i class="bi bi-chevron-down"></i>
                </button>

                <div id="faqFive" class="collapse" data-bs-parent="#faqAccordion">
                    <div class="faq-body">
                        المنصة تستخدم روابط واتساب مباشرة لفتح محادثة برسالة جاهزة. ويمكن لاحقًا تطوير التكامل مع WhatsApp Business API حسب احتياج الباقة.
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button
                    class="faq-button collapsed"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#faqSix"
                    aria-expanded="false"
                    aria-controls="faqSix"
                >
                    هل أقدر أتابع العملاء والتفاعل؟
                    <i class="bi bi-chevron-down"></i>
                </button>

                <div id="faqSix" class="collapse" data-bs-parent="#faqAccordion">
                    <div class="faq-body">
                        نعم. لوحة التحكم تحتوي على CRM مبسط للعملاء، مواعيدهم، ومصادرهم، بالإضافة إلى تتبع الضغطات على واتساب والروابط والمنتجات.
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button
                    class="faq-button collapsed"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#faqSeven"
                    aria-expanded="false"
                    aria-controls="faqSeven"
                >
                    هل أستطيع البدء مجانًا؟
                    <i class="bi bi-chevron-down"></i>
                </button>

                <div id="faqSeven" class="collapse" data-bs-parent="#faqAccordion">
                    <div class="faq-body">
                        نعم. يمكنك البدء بباقة مجانية أو تجربة محدودة، ثم الترقية لاحقًا عندما تحتاج منتجات أكثر، حجوزات، أو إمكانيات CRM أكبر.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>





{{-- Final CTA --}}
<section class="section-placeholder pt-0">
    <div class="container">
        <div class="final-cta">
            <h2>
                جهّز صفحة بزنسك وابدأ مشاركة الرابط اليوم
            </h2>

            <p>
                لا تنتظر موقع كامل أو إعدادات معقدة. ابدأ بصفحة بسيطة، ثم أضف المنتجات والحجوزات والعملاء خطوة بخطوة.
            </p>

            <div class="d-flex justify-content-center gap-2 flex-wrap">
                @guest
                    <a href="{{ route('register') }}" class="btn btn-white">
                        ابدأ مجانًا
                        <i class="bi bi-arrow-left-short"></i>
                    </a>

                    <a href="{{ route('login') }}" class="btn btn-outline-white">
                        تسجيل الدخول
                    </a>
                @else
                    <a href="{{ route('onboarding.create') }}" class="btn btn-white">
                        إعداد صفحة البزنس
                        <i class="bi bi-arrow-left-short"></i>
                    </a>
                @endguest
            </div>
        </div>
    </div>
</section>
    </main>

    {{-- Footer --}}
    <footer class="landing-footer">
        <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
            <div>
                © {{ date('Y') }} {{ config('app.name', 'Super Business') }}. جميع الحقوق محفوظة.
            </div>

            <div class="d-flex gap-3">
                <a href="{{ url('/privacy') }}" class="nav-link-clean">الخصوصية</a>
                <a href="{{ url('/terms') }}" class="nav-link-clean">الشروط</a>
            </div>
        </div>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": [
            {
                "@type": "Question",
                "name": "هل أحتاج موقع إلكتروني كامل؟",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "لا. يمكنك البدء بصفحة بزنس ذكية تجمع بياناتك وروابطك ومنتجاتك وحجوزاتك في رابط واحد."
                }
            },
            {
                "@type": "Question",
                "name": "هل الصفحة مناسبة للموبايل؟",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "نعم. التصميم مناسب للموبايل لأن أغلب العملاء يفتحون الروابط من واتساب والسوشيال والإعلانات."
                }
            },
            {
                "@type": "Question",
                "name": "هل أقدر أضيف منتجات وخدمات؟",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "نعم. يمكنك إضافة منتجات أو خدمات مع صور وأسعار وتصنيفات وأزرار طلب عبر واتساب."
                }
            },
            {
                "@type": "Question",
                "name": "هل يوجد نظام حجز مواعيد؟",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "نعم. يمكنك تفعيل الحجز وتحديد أيام وساعات العمل واستقبال طلبات الحجز من الصفحة العامة."
                }
            },
            {
                "@type": "Question",
                "name": "هل المنصة مرتبطة بواتساب؟",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "المنصة تستخدم روابط واتساب مباشرة لفتح محادثة برسالة جاهزة ويمكن تطوير التكامل لاحقًا مع WhatsApp Business API."
                }
            },
            {
                "@type": "Question",
                "name": "هل أقدر أتابع العملاء والتفاعل؟",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "نعم. تحتوي لوحة التحكم على CRM مبسط وتتبع للضغطات على واتساب والروابط والمنتجات."
                }
            },
            {
                "@type": "Question",
                "name": "هل أستطيع البدء مجانًا؟",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "نعم. يمكنك البدء بباقة مجانية أو تجربة محدودة ثم الترقية لاحقًا حسب احتياج نشاطك."
                }
            }
        ]
    }
</script>

@stack('scripts')
</body>
</html>