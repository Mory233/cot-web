<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#08090f">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
  <!-- Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-PR63CSERPH"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-PR63CSERPH');
  </script>
  <?php wp_head(); ?>
  <style>
  :root{--bg-deep:#060b18;--bg-panel:#0c1220;--bg-card:#111827;--border:rgba(255,255,255,0.10);--border-strong:rgba(255,255,255,0.16);--accent:#f59e0b;--accent-dim:rgba(245,158,11,0.10);--text-primary:#f1f5f9;--text-secondary:#64748b;--text-muted:#334155;--green:#22c55e;--red:#ef4444;--nav-h:76px}
  *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
  html{scroll-behavior:smooth}
  body{background:var(--bg-deep);color:var(--text-primary);font-family:'Inter',-apple-system,BlinkMacSystemFont,sans-serif;font-size:15px;line-height:1.6;min-height:100vh;-webkit-font-smoothing:antialiased}
  a{color:var(--accent);text-decoration:none}
  a:hover{opacity:.8}

  /* ── NAV ── */
  .cot-nav{position:fixed;top:0;left:0;right:0;height:var(--nav-h);background:rgba(6,11,24,.98);backdrop-filter:blur(24px);border-bottom:1px solid var(--border-strong);z-index:1000;box-shadow:0 1px 20px rgba(0,0,0,.5)}
  .admin-bar .cot-nav{top:32px}
  .cot-nav-inner{max-width:1440px;margin:0 auto;padding:0 24px;height:100%;display:flex;align-items:center;gap:0}

  /* Logo */
  .cot-nav-logo{display:flex;flex-direction:column;text-decoration:none;flex-shrink:0;margin-right:36px}
  .cot-nav-logo .logo-main{font-weight:800;font-size:19px;letter-spacing:.06em;color:var(--text-primary);line-height:1;text-transform:uppercase}
  .cot-nav-logo .logo-czech{color:var(--text-secondary);font-weight:700;letter-spacing:.08em}
  .cot-nav-logo .logo-cot{color:var(--accent)}
  .cot-nav-logo .logo-terminal{color:var(--text-primary)}
  .cot-nav-logo .logo-sub{font-size:11px;color:var(--text-muted);margin-top:4px;letter-spacing:.02em}

  /* Nav links */
  .cot-nav-links{display:flex;align-items:stretch;gap:0;flex:1;height:100%}
  .cot-nav-links .nav-link{display:flex;align-items:center;color:var(--text-secondary);font-size:13.5px;font-weight:500;padding:0 14px;border-bottom:2px solid transparent;transition:color 150ms ease,border-color 150ms ease,background 150ms ease;text-decoration:none;white-space:nowrap;cursor:pointer}
  .cot-nav-links .nav-link:hover{color:var(--text-primary);opacity:1;border-bottom-color:rgba(245,158,11,.4);background:rgba(245,158,11,.04)}
  .cot-nav-links .nav-link.active{color:var(--accent);border-bottom-color:var(--accent);opacity:1;background:rgba(245,158,11,.06)}
  .cot-nav-links .nav-sep{width:1px;background:var(--border);margin:18px 6px;flex-shrink:0}
  .cot-nav-links .nav-info{margin-left:6px;border:1px solid var(--border);border-radius:6px;border-bottom:1px solid var(--border)!important;margin-right:0;padding:0 14px;font-size:12px}
  .cot-nav-links .nav-info:hover{border-color:var(--accent);color:var(--accent);background:var(--accent-dim)}
  .cot-nav-links .nav-info.active{border-color:var(--accent);color:var(--accent);background:var(--accent-dim)}

  /* Status badge */
  .cot-nav-status{display:flex;align-items:center;gap:8px;font-size:11px;color:var(--text-secondary);flex-shrink:0;margin-left:16px;padding:5px 13px;background:var(--bg-card);border:1px solid var(--border);border-radius:20px}
  .cot-nav-status strong{color:var(--text-primary);font-weight:600}
  .cot-nav-dot{width:7px;height:7px;border-radius:50%;background:var(--green);animation:nav-pulse 2s ease-in-out infinite;flex-shrink:0;box-shadow:0 0 5px rgba(34,197,94,.5)}
  @keyframes nav-pulse{0%,100%{opacity:1;box-shadow:0 0 0 0 rgba(34,197,94,.5)}50%{opacity:.6;box-shadow:0 0 0 5px rgba(34,197,94,0)}}

  /* Hamburger */
  .cot-nav-toggle{display:none;background:transparent;border:1px solid var(--border);color:var(--text-primary);padding:8px 12px;border-radius:8px;cursor:pointer;font-size:18px;margin-left:auto;line-height:1;min-width:42px;min-height:42px;align-items:center;justify-content:center}
  .cot-nav-mobile{display:none;position:fixed;top:var(--nav-h);left:0;right:0;background:rgba(12,18,32,.98);backdrop-filter:blur(20px);border-bottom:1px solid var(--border-strong);padding:8px 12px 16px;flex-direction:column;gap:2px;z-index:999;max-height:calc(100vh - var(--nav-h));overflow-y:auto}
  .admin-bar .cot-nav-mobile{top:calc(var(--nav-h) + 32px)}
  .cot-nav-mobile.open{display:flex}
  .cot-nav-mobile a,.cot-nav-mobile .nav-link{display:flex;align-items:center;color:var(--text-secondary);padding:13px 16px;border-radius:8px;font-size:15px;font-weight:500;transition:all 150ms ease;border-bottom:none!important;text-decoration:none;min-height:48px}
  .cot-nav-mobile a:hover,.cot-nav-mobile .nav-link:hover,.cot-nav-mobile a:active{background:var(--bg-card);color:var(--text-primary);opacity:1}
  .cot-nav-mobile .nav-sep{display:none}
  .cot-nav-mobile-divider{height:1px;background:var(--border);margin:6px 8px}

  /* PAGE + HERO + FOOTER */
  .cot-page-wrap{padding-top:var(--nav-h);min-height:100vh}
  .cot-hero{background:var(--bg-panel);border-bottom:1px solid var(--border);padding:72px 24px 80px;text-align:center;position:relative;overflow:hidden}
  .cot-hero::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 60% at 50% -10%,rgba(245,158,11,.07) 0%,transparent 70%);pointer-events:none}
  .cot-hero-inner{max-width:820px;margin:0 auto;position:relative}
  .cot-hero-eyebrow{font-size:13px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--accent);margin-bottom:16px;opacity:.9}
  .cot-hero h1{font-weight:800;font-size:clamp(36px,5.5vw,58px);color:var(--text-primary);letter-spacing:-.03em;line-height:1.1;margin-bottom:20px}
  .cot-hero h1 span{color:var(--accent)}
  .cot-hero p{font-size:17px;color:var(--text-secondary);max-width:600px;margin:0 auto;line-height:1.7}
  .cot-hero p strong{color:var(--text-primary)}
  .cot-hero-meta{display:flex;align-items:center;justify-content:center;gap:12px;margin-top:32px;flex-wrap:wrap}
  .cot-hero-badge{display:flex;align-items:center;gap:8px;font-size:13px;font-weight:500;color:var(--text-secondary);background:var(--bg-card);border:1px solid var(--border-strong);border-radius:24px;padding:8px 18px;transition:border-color 200ms ease}
  .cot-hero-badge:hover{border-color:rgba(245,158,11,.4)}
  .cot-hero-badge .dot{width:7px;height:7px;border-radius:50%;flex-shrink:0}
  .cot-dashboard-section{max-width:1400px;margin:0 auto;padding:24px 16px 48px}
  .cot-footer{background:var(--bg-panel);border-top:1px solid var(--border);padding:24px 24px}
  .cot-footer-inner{max-width:1400px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}
  .cot-footer-left .footer-brand{font-weight:700;font-size:14px;color:var(--text-primary);margin-bottom:4px}
  .cot-footer-left p{font-size:12px;color:var(--text-muted)}
  .cot-footer-right{display:flex;gap:16px;align-items:center;flex-wrap:wrap}
  .cot-footer-right a{font-size:12px;color:var(--text-muted);transition:color 150ms ease}
  .cot-footer-right a:hover{color:var(--accent);opacity:1}
  .footer-divider{color:var(--text-muted);font-size:12px}
  .cot-info-page{max-width:860px;margin:0 auto;padding:48px 24px 80px}
  .cot-info-page h1{font-weight:700;font-size:clamp(28px,5vw,40px);color:var(--text-primary);margin-bottom:8px;line-height:1.2;letter-spacing:-.02em}
  .cot-lead{font-size:16px;color:var(--text-secondary);margin-bottom:48px;border-left:3px solid var(--accent);padding-left:16px}
  .cot-info-section{margin-bottom:48px}
  .cot-info-section h2{font-weight:700;font-size:20px;color:var(--text-primary);margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid var(--border);letter-spacing:-.01em}
  .cot-info-section p{color:var(--text-secondary);margin-bottom:12px;line-height:1.75}
  .cot-info-section strong{color:var(--text-primary)}
  .cot-group-cards{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:20px}
  .cot-group-card{background:var(--bg-card);border:1px solid var(--border);border-radius:8px;padding:20px}
  .cot-group-card .gc-label{font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:8px;font-weight:600}
  .cot-group-card .gc-name{font-weight:700;font-size:15px;margin-bottom:8px}
  .cot-group-card .gc-desc{font-size:13px;color:var(--text-secondary);line-height:1.6}
  .cot-index-scale{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:16px}
  .cot-scale-item{background:var(--bg-card);border:1px solid var(--border);border-radius:8px;padding:16px;text-align:center}
  .cot-scale-item .si-range{font-size:22px;font-weight:700;margin-bottom:4px;letter-spacing:-.01em}
  .cot-scale-item .si-label{font-size:12px;color:var(--text-secondary)}
  .cot-scale-item.bullish .si-range{color:var(--green)}
  .cot-scale-item.neutral .si-range{color:var(--accent)}
  .cot-scale-item.bearish .si-range{color:var(--red)}
  .cot-faq{margin-top:16px}
  .cot-faq-item{border-bottom:1px solid var(--border);padding:16px 0}
  .cot-faq-item:last-child{border-bottom:none}
  .cot-faq-q{font-weight:600;color:var(--text-primary);margin-bottom:6px}
  .cot-faq-a{font-size:14px;color:var(--text-secondary);line-height:1.7}
  .cot-timeline{display:flex;align-items:flex-start;gap:8px;margin-top:20px;flex-wrap:wrap}
  .cot-tl-item{background:var(--bg-card);border:1px solid var(--border);border-radius:8px;padding:14px 16px;flex:1;min-width:160px}
  .cot-tl-dot{width:10px;height:10px;border-radius:50%;margin-bottom:8px}
  .cot-tl-label{font-weight:700;font-size:13px;color:var(--text-primary);margin-bottom:4px}
  .cot-tl-desc{font-size:12px;color:var(--text-secondary);line-height:1.5}
  .cot-tl-arrow{font-size:20px;color:var(--text-muted);align-self:center;flex-shrink:0}
  @media(max-width:600px){.cot-tl-arrow{display:none}}
  .cot-back-home{display:inline-flex;align-items:center;gap:8px;color:var(--accent);font-weight:600;font-size:14px;margin-bottom:32px;padding:8px 16px;border:1px solid var(--border);border-radius:6px;transition:all 150ms ease}
  .cot-back-home:hover{border-color:var(--accent);background:var(--accent-dim);opacity:1}
  .cot-generic-page{max-width:860px;margin:0 auto;padding:48px 24px 80px}
  .cot-generic-page h1{font-weight:700;font-size:30px;margin-bottom:24px;letter-spacing:-.02em}
  .cot-generic-page .entry-content p{color:var(--text-secondary);margin-bottom:16px;line-height:1.75}
  #wpadminbar{position:fixed!important}

  @media(max-width:960px){
    .cot-nav-links{display:none}
    .cot-nav-status{display:none}
    .cot-nav-toggle{display:flex}
    .cot-group-cards,.cot-index-scale{grid-template-columns:1fr}
  }
  @media(max-width:600px){
    .cot-hero{padding:48px 20px 56px}
    .cot-hero h1{font-size:clamp(30px,8vw,42px)}
    .cot-hero p{font-size:15px}
    .cot-hero-meta{gap:8px}
    .cot-hero-badge{font-size:12px;padding:7px 14px}
    .cot-dashboard-section{padding:12px 8px 40px}
    .cot-info-page{padding:32px 16px 60px}
    .cot-footer-inner{flex-direction:column;align-items:flex-start;gap:12px}
    .cot-footer-right{gap:10px;flex-wrap:wrap}
    .cot-back-home{font-size:13px;padding:7px 12px}
    .cot-info-section h2{font-size:17px}
    .cot-group-card{padding:16px}
  }
  @media(max-width:400px){
    .cot-hero{padding:36px 16px 44px}
    .cot-hero-eyebrow{font-size:11px}
    .cot-hero-badge{padding:6px 12px;font-size:11.5px}
    .cot-nav-logo .logo-main{font-size:16px}
  }
  </style>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<nav class="cot-nav">
  <div class="cot-nav-inner">

    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="cot-nav-logo">
      <span class="logo-main"><span class="logo-czech">CZECH</span> <span class="logo-cot">COT</span> <span class="logo-terminal">TERMINAL</span></span>
      <span class="logo-sub">Tracks banks &middot; Hedge funds &middot; Retail</span>
    </a>

    <div class="cot-nav-links">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>"
         class="nav-link nav-link-overview <?php echo is_front_page() ? 'active' : ''; ?>">Overview</a>
      <a href="<?php echo esc_url( home_url('/') ); ?>#cot-section-forex"    class="nav-link" data-section="forex">Forex</a>
      <a href="<?php echo esc_url( home_url('/') ); ?>#cot-section-crypto"   class="nav-link" data-section="crypto">Crypto</a>
      <a href="<?php echo esc_url( home_url('/') ); ?>#cot-section-indices"  class="nav-link" data-section="indices">Indices</a>
      <a href="<?php echo esc_url( home_url('/') ); ?>#cot-section-bonds"    class="nav-link" data-section="bonds">Bonds</a>
      <a href="<?php echo esc_url( home_url('/') ); ?>#cot-section-energy"   class="nav-link" data-section="energy">Energy</a>
      <a href="<?php echo esc_url( home_url('/') ); ?>#cot-section-metals"   class="nav-link" data-section="metals">Metals</a>
      <a href="<?php echo esc_url( home_url('/') ); ?>#cot-section-grains"   class="nav-link" data-section="grains">Commodities</a>
      <a href="<?php echo esc_url( home_url( '/co-je-cot/' ) ); ?>"
         class="nav-link nav-info <?php echo is_page( 'co-je-cot' ) ? 'active' : ''; ?>">What is COT?</a>
    </div>

    <div class="cot-nav-status">
      <span class="cot-nav-dot"></span>
      <span id="cot-nav-last-update">CFTC Data</span>
    </div>

    <button class="cot-nav-toggle" id="cot-nav-toggle">&#9776;</button>
  </div>
</nav>

<div class="cot-nav-mobile" id="cot-nav-mobile">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Overview</a>
  <div class="cot-nav-mobile-divider"></div>
  <a href="<?php echo esc_url( home_url('/') ); ?>#cot-section-forex"    class="nav-link" data-section="forex">Forex</a>
  <a href="<?php echo esc_url( home_url('/') ); ?>#cot-section-crypto"   class="nav-link" data-section="crypto">Crypto</a>
  <a href="<?php echo esc_url( home_url('/') ); ?>#cot-section-indices"  class="nav-link" data-section="indices">Indices</a>
  <a href="<?php echo esc_url( home_url('/') ); ?>#cot-section-bonds"    class="nav-link" data-section="bonds">Bonds</a>
  <a href="<?php echo esc_url( home_url('/') ); ?>#cot-section-energy"   class="nav-link" data-section="energy">Energy</a>
  <a href="<?php echo esc_url( home_url('/') ); ?>#cot-section-metals"   class="nav-link" data-section="metals">Metals</a>
  <a href="<?php echo esc_url( home_url('/') ); ?>#cot-section-grains"   class="nav-link" data-section="grains">Commodities</a>
  <div class="cot-nav-mobile-divider"></div>
  <a href="<?php echo esc_url( home_url( '/co-je-cot/' ) ); ?>">What is COT?</a>
</div>

<div class="cot-page-wrap">
