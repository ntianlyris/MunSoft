<?php
// PHP logic can go here if needed, but the current index.php has it in the script section.
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>IntelliGov — Smart Governance. Seamless Operations.</title>

  <link rel="icon" href="includes/images/polanco_logo.png" type="image/png" />
  <link rel="shortcut icon" href="includes/images/polanco_logo.png" type="image/png" />

  <!-- Local Fonts -->
  <link rel="stylesheet" href="css/fonts.css">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style (AdminLTE) -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- Sweetalert2 -->
  <link rel="stylesheet" href="plugins/sweetalert2/sweetalert2.min.css">

  <style>
    :root {
      --navy: #080c24;
      --deep: #0a0f2e;
      --blue-mid: #1a2a6c;
      --blue: #1565c0;
      --cyan: #29b6f6;
      --cyan-bright: #4dd0e1;
      --accent: #00e5ff;
      --gold: #ffc107;
      --white: #f0f6ff;
      --muted: rgba(173,210,255,0.55);
      --glass: rgba(255,255,255,0.04);
      --glass-border: rgba(255,255,255,0.08);
      --radius: 16px;
      --radius-sm: 10px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html { scroll-behavior: smooth; }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--navy);
      color: var(--white);
      overflow-x: hidden;
      line-height: 1.6;
    }

    /* ── SCROLLBAR ── */
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: var(--navy); }
    ::-webkit-scrollbar-thumb { background: var(--blue); border-radius: 9px; }

    /* ── ANIMATED BG CANVAS ── */
    #bg-canvas {
      position: fixed; inset: 0; z-index: 0;
      pointer-events: none;
    }

    /* ── NAV ── */
    nav {
      position: fixed; top: 0; left: 0; right: 0; z-index: 100;
      display: flex; align-items: center; justify-content: space-between;
      padding: 14px 5vw;
      background: rgba(8,12,36,0.75);
      backdrop-filter: blur(18px);
      border-bottom: 1px solid var(--glass-border);
      transition: background 0.3s;
    }

    .nav-brand {
      display: flex; align-items: center; gap: 10px;
      text-decoration: none;
    }

    .nav-logo { width: 36px; height: 36px; object-fit: contain; border-radius: 8px; }

    .nav-title {
      font-family: 'Syne', sans-serif;
      font-size: 1.2rem; font-weight: 800;
      background: linear-gradient(90deg, var(--cyan), var(--accent));
      -webkit-background-clip: text; -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .nav-links {
      display: flex; gap: 6px; align-items: center;
      list-style: none;
    }

    .nav-links a {
      color: var(--muted); text-decoration: none;
      font-size: 0.82rem; font-weight: 500; letter-spacing: 0.04em;
      padding: 6px 12px; border-radius: 8px;
      transition: color 0.2s, background 0.2s;
    }

    .nav-links a:hover { color: var(--white); background: var(--glass); }

    .nav-cta {
      background: linear-gradient(135deg, var(--blue), var(--cyan)) !important;
      color: #fff !important;
      font-weight: 600 !important;
      -webkit-text-fill-color: #fff !important;
    }

    .hamburger {
      display: none; flex-direction: column; gap: 5px;
      cursor: pointer; background: none; border: none; padding: 4px;
    }
    .hamburger span {
      display: block; width: 24px; height: 2px;
      background: var(--cyan); border-radius: 2px;
      transition: transform 0.3s, opacity 0.3s;
    }
    .hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
    .hamburger.open span:nth-child(2) { opacity: 0; }
    .hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

    .mobile-menu {
      display: none; position: fixed;
      top: 65px; left: 0; right: 0;
      background: rgba(8,12,36,0.97);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--glass-border);
      padding: 16px 5vw 24px; z-index: 99;
      flex-direction: column; gap: 4px;
    }
    .mobile-menu.show { display: flex; }
    .mobile-menu a {
      color: var(--muted); text-decoration: none;
      font-size: 1rem; font-weight: 500; padding: 10px 0;
      border-bottom: 1px solid var(--glass-border);
      transition: color 0.2s;
    }
    .mobile-menu a:hover { color: var(--accent); }

    /* ── HERO ── */
    #hero {
      min-height: 100svh;
      display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      text-align: center;
      padding: 100px 5vw 60px;
      position: relative; z-index: 1;
    }

    .hero-logos {
      display: flex; align-items: center; justify-content: center;
      gap: clamp(20px, 5vw, 60px);
      margin-bottom: 40px;
      flex-wrap: wrap;
    }

    .hero-logo-wrap {
      position: relative;
      animation: floatLogo 6s ease-in-out infinite;
    }
    .hero-logo-wrap:nth-child(3) { animation-delay: -3s; }

    .hero-logo-wrap::after {
      content: '';
      position: absolute; inset: -8px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(41,182,246,0.2), transparent 70%);
      animation: pulse 3s ease-in-out infinite;
    }

    @keyframes floatLogo {
      0%,100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }
    @keyframes pulse {
      0%,100% { opacity: 0.4; transform: scale(1); }
      50% { opacity: 1; transform: scale(1.15); }
    }

    .hero-logo { width: clamp(80px, 15vw, 130px); height: clamp(80px, 15vw, 130px); object-fit: contain; border-radius: 16px; }
    .hero-logo.round { border-radius: 50%; }

    .hero-divider {
      width: 2px; height: clamp(60px, 10vw, 100px);
      background: linear-gradient(180deg, transparent, var(--cyan), transparent);
      flex-shrink: 0;
    }

    .hero-badge {
      display: inline-flex; align-items: center; gap: 6px;
      background: rgba(41,182,246,0.1); border: 1px solid rgba(41,182,246,0.3);
      color: var(--cyan); font-size: 0.75rem; font-weight: 600; letter-spacing: 0.1em;
      text-transform: uppercase; border-radius: 50px;
      padding: 5px 14px; margin-bottom: 20px;
      animation: fadeUp 0.8s ease both 0.1s;
    }
    .badge-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--accent); animation: blink 1.5s infinite; }
    @keyframes blink { 0%,100% { opacity:1; } 50% { opacity:0.2; } }

    h1 {
      font-family: 'Syne', sans-serif;
      font-size: clamp(2.4rem, 7vw, 5.5rem);
      font-weight: 800; line-height: 1.05; letter-spacing: -0.02em;
      margin-bottom: 16px;
      animation: fadeUp 0.9s ease both 0.2s;
    }

    .h1-gradient {
      background: linear-gradient(135deg, #fff 0%, var(--cyan) 50%, var(--accent) 100%);
      -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
    }

    .hero-tagline {
      font-size: clamp(1rem, 2.5vw, 1.25rem);
      color: var(--muted); font-weight: 300; font-style: italic;
      margin-bottom: 12px;
      animation: fadeUp 0.9s ease both 0.3s;
    }

    .hero-sub {
      max-width: 600px; margin: 0 auto 36px;
      font-size: clamp(0.88rem, 2vw, 1rem);
      color: rgba(173,210,255,0.7); line-height: 1.7;
      animation: fadeUp 0.9s ease both 0.4s;
    }

    .hero-actions {
      display: flex; gap: 12px; flex-wrap: wrap; justify-content: center;
      animation: fadeUp 0.9s ease both 0.5s;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--blue) 0%, var(--cyan) 100%);
      color: #fff; border: none; cursor: pointer;
      font-family: 'DM Sans', sans-serif; font-size: 0.95rem; font-weight: 600;
      padding: 14px 30px; border-radius: 50px;
      box-shadow: 0 4px 30px rgba(41,182,246,0.35);
      transition: transform 0.2s, box-shadow 0.2s;
      letter-spacing: 0.02em;
      text-decoration: none;
      display: inline-block;
    }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 40px rgba(41,182,246,0.5); color: #fff; }
    .btn-primary:active { transform: translateY(0); }

    .btn-outline {
      background: transparent; border: 1.5px solid rgba(41,182,246,0.4);
      color: var(--cyan); cursor: pointer;
      font-family: 'DM Sans', sans-serif; font-size: 0.95rem; font-weight: 500;
      padding: 14px 30px; border-radius: 50px;
      transition: background 0.2s, border-color 0.2s;
      text-decoration: none;
      display: inline-block;
    }
    .btn-outline:hover { background: rgba(41,182,246,0.08); border-color: var(--cyan); color: var(--cyan); }

    .scroll-hint {
      position: absolute; bottom: 28px; left: 50%; transform: translateX(-50%);
      display: flex; flex-direction: column; align-items: center; gap: 6px;
      color: var(--muted); font-size: 0.72rem; letter-spacing: 0.1em; text-transform: uppercase;
      animation: fadeUp 1s ease both 1s;
    }
    .scroll-arrow { width: 20px; height: 20px; border-right: 2px solid var(--cyan); border-bottom: 2px solid var(--cyan); transform: rotate(45deg); animation: scrollBounce 1.5s infinite; }
    @keyframes scrollBounce { 0%,100%{transform:rotate(45deg) translateY(0)} 50%{transform:rotate(45deg) translateY(5px)} }

    /* ── LOGIN OVERLAY ── */
    #login-overlay {
      position: fixed; inset: 0; z-index: 200;
      background: rgba(4,6,20,0.85);
      backdrop-filter: blur(12px);
      display: flex; align-items: center; justify-content: center;
      padding: 20px;
      opacity: 0; pointer-events: none;
      transition: opacity 0.35s ease;
    }
    #login-overlay.show { opacity: 1; pointer-events: all; }

    .auth-card {
      background: linear-gradient(145deg, rgba(15,22,55,0.95), rgba(10,15,46,0.98));
      border: 1px solid rgba(41,182,246,0.2);
      border-radius: 24px;
      padding: clamp(28px, 5vw, 44px);
      width: 100%; max-width: 420px;
      box-shadow: 0 30px 80px rgba(0,0,0,0.6), 0 0 0 1px rgba(41,182,246,0.05);
      position: relative; overflow: hidden;
    }

    .auth-card::before {
      content: '';
      position: absolute; top: -60px; right: -60px;
      width: 200px; height: 200px; border-radius: 50%;
      background: radial-gradient(circle, rgba(41,182,246,0.12), transparent 70%);
      pointer-events: none;
    }

    .auth-tabs {
      display: flex; background: rgba(255,255,255,0.04);
      border-radius: 12px; padding: 4px; margin-bottom: 28px;
      overflow: hidden; position: relative;
    }

    .auth-tab-slider {
      position: absolute; top: 4px; bottom: 4px;
      width: calc(50% - 4px); border-radius: 9px;
      background: linear-gradient(135deg, var(--blue), var(--cyan));
      box-shadow: 0 2px 12px rgba(41,182,246,0.3);
      transition: transform 0.35s cubic-bezier(0.4,0,0.2,1);
    }
    .auth-tabs.register-mode .auth-tab-slider { transform: translateX(calc(100% + 0px)); }

    .auth-tab {
      flex: 1; padding: 9px; text-align: center;
      font-family: 'Syne', sans-serif; font-weight: 600; font-size: 0.85rem;
      cursor: pointer; border-radius: 9px; position: relative; z-index: 1;
      color: var(--muted); transition: color 0.3s;
      border: none; background: none;
    }
    .auth-tab.active { color: #fff; }

    .auth-forms-wrapper { overflow: hidden; }

    .auth-forms-track {
      display: flex; width: 200%;
      transition: transform 0.4s cubic-bezier(0.4,0,0.2,1);
    }
    .auth-forms-track.show-register { transform: translateX(-50%); }

    .auth-form-panel { width: 50%; flex-shrink: 0; }

    .auth-logo { width: 52px; margin: 0 auto 12px; display: block; border-radius: 10px; }
    .auth-heading {
      font-family: 'Syne', sans-serif; font-size: 1.3rem; font-weight: 700;
      text-align: center; margin-bottom: 4px;
    }
    .auth-sub { text-align: center; font-size: 0.82rem; color: var(--muted); margin-bottom: 24px; }

    .form-group { margin-bottom: 16px; text-align: left; }
    .form-group label {
      display: block; font-size: 0.78rem; font-weight: 600;
      color: var(--muted); letter-spacing: 0.06em; text-transform: uppercase; margin-bottom: 6px;
    }
    .form-group input {
      width: 100%; padding: 12px 16px;
      background: rgba(255,255,255,0.05); border: 1.5px solid rgba(255,255,255,0.08);
      border-radius: 10px; color: var(--white);
      font-family: 'DM Sans', sans-serif; font-size: 0.95rem;
      transition: border-color 0.2s, background 0.2s;
      outline: none;
    }
    .form-group input:focus {
      border-color: var(--cyan); background: rgba(41,182,246,0.06);
    }
    .form-group input::placeholder { color: rgba(173,210,255,0.3); }

    .form-submit {
      width: 100%; padding: 13px;
      background: linear-gradient(135deg, var(--blue), var(--cyan));
      border: none; border-radius: 10px; color: #fff;
      font-family: 'Syne', sans-serif; font-size: 0.95rem; font-weight: 700;
      cursor: pointer; margin-top: 8px;
      box-shadow: 0 4px 20px rgba(41,182,246,0.25);
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .form-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 28px rgba(41,182,246,0.4); }

    .auth-close {
      position: absolute; top: 16px; right: 16px;
      background: rgba(255,255,255,0.06); border: 1px solid var(--glass-border);
      color: var(--muted); width: 34px; height: 34px; border-radius: 50%;
      cursor: pointer; font-size: 1.1rem; display: flex; align-items: center; justify-content: center;
      transition: background 0.2s, color 0.2s;
    }
    .auth-close:hover { background: rgba(255,255,255,0.12); color: #fff; }

    .form-footer { text-align: center; margin-top: 14px; font-size: 0.8rem; color: var(--muted); }
    .form-footer a { color: var(--cyan); text-decoration: none; font-weight: 600; cursor: pointer; }
    .form-footer a:hover { text-decoration: underline; }

    /* ── SECTIONS ── */
    section { position: relative; z-index: 1; }

    .section-pad { padding: clamp(60px, 10vw, 110px) 5vw; }

    .section-label {
      display: inline-flex; align-items: center; gap: 8px;
      color: var(--cyan); font-size: 0.75rem; font-weight: 600;
      letter-spacing: 0.12em; text-transform: uppercase; margin-bottom: 12px;
    }
    .section-label::before, .section-label::after {
      content: ''; height: 1px; width: 24px;
      background: var(--cyan); opacity: 0.5;
    }

    .section-heading {
      font-family: 'Syne', sans-serif;
      font-size: clamp(1.8rem, 4vw, 2.8rem);
      font-weight: 700; line-height: 1.15; margin-bottom: 16px;
    }

    .section-desc {
      max-width: 560px; font-size: clamp(0.88rem, 1.8vw, 1rem);
      color: rgba(173,210,255,0.7); line-height: 1.7;
    }

    .centered { text-align: center; }
    .centered .section-label, .centered .section-desc { margin-left: auto; margin-right: auto; }
    .centered .section-label { justify-content: center; }

    /* ── STATS ── */
    #stats {
      background: linear-gradient(180deg, var(--navy) 0%, rgba(21,101,192,0.12) 100%);
      border-top: 1px solid var(--glass-border);
      border-bottom: 1px solid var(--glass-border);
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
      gap: 30px;
      max-width: 1100px; margin: 0 auto;
    }

    .stat-item { text-align: center; }
    .stat-number {
      display: block; font-family: 'Syne', sans-serif; font-size: 2.22rem; font-weight: 800;
      background: linear-gradient(135deg, #fff, var(--cyan));
      -webkit-background-clip: text; -webkit-text-fill-color: transparent;
      margin-bottom: 4px;
    }
    .stat-label { font-size: 0.75rem; vertical-align: middle; color: var(--muted); text-transform: uppercase; letter-spacing: 0.08em; font-weight: 600; }

    /* ── MODULES ── */
    .modules-grid {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 24px; margin-top: 50px;
    }

    .module-card {
      background: var(--glass); border: 1px solid var(--glass-border);
      border-radius: var(--radius); padding: 32px;
      transition: transform 0.3s, background 0.3s, border-color 0.3s;
      position: relative; overflow: hidden;
    }
    .module-card::before {
      content: ''; position: absolute; inset: 0;
      background: radial-gradient(circle at top right, rgba(41,182,246,0.1), transparent 60%);
      opacity: 0; transition: opacity 0.3s;
    }
    .module-card:hover { 
      transform: translateY(-5px); background: rgba(255,255,255,0.06); 
      border-color: rgba(41,182,246,0.3);
    }
    .module-card:hover::before { opacity: 1; }

    .module-icon { 
      font-size: 1.8rem; margin-bottom: 20px; display: inline-block; 
      filter: drop-shadow(0 0 10px rgba(41,182,246,0.5));
    }
    .module-title { font-family: 'Syne', sans-serif; font-size: 1.1rem; font-weight: 700; margin-bottom: 12px; }
    .module-desc { font-size: 0.88rem; color: var(--muted); line-height: 1.6; }

    .ai-badge {
      display: inline-flex; align-items: center; gap: 5px;
      background: linear-gradient(135deg, rgba(0,229,255,0.15), rgba(41,182,246,0.15));
      border: 1px solid rgba(0,229,255,0.3); color: var(--accent);
      font-size: 0.68rem; font-weight: 700; border-radius: 4px;
      padding: 3px 8px; margin-top: 16px; text-transform: uppercase;
    }

    /* ── REVEAL ── */
    .reveal { opacity: 0; transform: translateY(24px); transition: opacity 0.8s ease, transform 0.8s ease; }
    .reveal.visible { opacity: 1; transform: translateY(0); }

    /* ── FOOTER ── */
    footer {
      padding: 80px 5vw 40px; border-top: 1px solid var(--glass-border);
      background: rgba(4,6,20,0.5);
    }

    .footer-grid {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 50px; max-width: 1200px; margin: 0 auto;
    }

    .footer-brand .nav-title { font-size: 1.5rem; margin-bottom: 16px; }
    .footer-desc { font-size: 0.88rem; color: var(--muted); margin-bottom: 24px; max-width: 300px; }

    .footer-logos { display: flex; align-items: center; gap: 20px; }
    .footer-logo { height: 40px; object-fit: contain; }

    .footer-links h4 { font-family: 'Syne', sans-serif; font-size: 0.95rem; margin-bottom: 20px; color: #fff; }
    .footer-links ul { list-style: none; }
    .footer-links ul li { margin-bottom: 12px; }
    .footer-links ul a { color: var(--muted); text-decoration: none; font-size: 0.88rem; transition: color 0.2s; }
    .footer-links ul a:hover { color: var(--cyan); }

    .footer-bottom {
      margin-top: 60px; padding-top: 30px; border-top: 1px solid var(--glass-border);
      display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;
    }
    .copy { font-size: 0.82rem; color: var(--muted); }

    @media (max-width: 850px) {
      nav .nav-links { display: none; }
      .hamburger { display: flex; }
      h1 { font-size: 3.2rem; }
    }

    .is-invalid { border-color: #dc3545 !important; background: rgba(220, 53, 69, 0.05) !important; }
    .text-red { color: #dc3545 !important; font-size: 0.75rem; margin-top: 4px; display: block; }

    /* ── PRIVACY MODAL ── */
    #privacy-overlay {
      position: fixed; inset: 0; z-index: 300;
      background: rgba(4,6,20,0.92);
      backdrop-filter: blur(15px);
      display: flex; align-items: center; justify-content: center;
      padding: 20px;
      opacity: 0; pointer-events: none;
      transition: opacity 0.4s ease;
    }
    #privacy-overlay.show { opacity: 1; pointer-events: all; }

    .privacy-card {
      background: linear-gradient(145deg, rgba(15,22,55,0.98), rgba(10,15,46,1));
      border: 1px solid rgba(41,182,246,0.25);
      border-radius: 24px;
      padding: 0;
      width: 100%; max-width: 800px;
      max-height: 85vh;
      box-shadow: 0 40px 100px rgba(0,0,0,0.8);
      position: relative; overflow: hidden;
      display: flex; flex-direction: column;
    }

    .privacy-header {
      padding: 24px 40px;
      border-bottom: 1px solid var(--glass-border);
      background: rgba(255,255,255,0.02);
    }
    .privacy-header h2 {
      font-family: 'Syne', sans-serif; font-size: 1.5rem; font-weight: 800;
      margin: 0; color: var(--cyan);
    }
    .privacy-header p { font-size: 0.85rem; color: var(--muted); margin: 5px 0 0; }

    .privacy-body {
      padding: 40px;
      overflow-y: auto;
      font-size: 0.92rem; color: rgba(173,210,255,0.85);
      line-height: 1.8;
    }
    .privacy-body h3 {
      font-family: 'Syne', sans-serif; font-size: 1.1rem; color: #fff;
      margin: 25px 0 12px;
    }
    .privacy-body p { margin-bottom: 15px; }
    .privacy-body ul { margin-bottom: 15px; padding-left: 20px; list-style-type: square; }
    .privacy-body li { margin-bottom: 8px; }

    .privacy-footer {
      padding: 20px 40px;
      border-top: 1px solid var(--glass-border);
      background: rgba(255,255,255,0.02);
      text-align: right;
    }

    .privacy-close-btn {
      background: rgba(255,255,255,0.06); border: 1px solid var(--glass-border);
      color: #fff; padding: 10px 24px; border-radius: 10px;
      cursor: pointer; font-family: 'Syne', sans-serif; font-weight: 700;
      transition: background 0.2s;
    }
    .privacy-close-btn:hover { background: rgba(255,255,255,0.12); }
  </style>
</head>

<body>

<canvas id="bg-canvas"></canvas>

<nav id="main-nav">
  <a href="#" class="nav-brand">
    <img src="includes/images/intelligov.png" class="nav-logo" alt="IntelliGov Logo" id="nav-igov-logo">
    <span class="nav-title">IntelliGov</span>
  </a>
  <ul class="nav-links">
    <li><a href="#hero">Overview</a></li>
    <li><a href="#modules">System Modules</a></li>
    <li><a href="#tech">Technologies</a></li>
    <li><a href="#" onclick="showLogin(event)" class="nav-cta">Access Portal</a></li>
  </ul>
  <button class="hamburger" id="hamburger" onclick="toggleMenu()">
    <span></span><span></span><span></span>
  </button>
</nav>

<div class="mobile-menu" id="mobile-menu">
  <a href="#hero" onclick="toggleMenu()">Overview</a>
  <a href="#modules" onclick="toggleMenu()">System Modules</a>
  <a href="#tech" onclick="toggleMenu()">Technologies</a>
  <a href="#" onclick="showLogin(event); toggleMenu()">Access Portal</a>
</div>

<!-- LOGIN OVERLAY -->
<div id="login-overlay" onclick="overlayClickClose(event)">
  <div class="auth-card">
    <button class="auth-close" onclick="hideLogin()">&times;</button>
    
    <div class="auth-tabs" id="auth-tabs">
      <div class="auth-tab-slider"></div>
      <button class="auth-tab active" onclick="switchTab('login')">Login</button>
      <button class="auth-tab" onclick="switchTab('register')">Register</button>
    </div>

    <div class="auth-forms-wrapper">
      <div class="auth-forms-track" id="auth-forms-track">
        
        <!-- LOGIN PANEL -->
        <div class="auth-form-panel" id="panel-login">
          <img class="auth-logo" src="includes/images/intelligov.png" alt="IntelliGov" id="auth-igov-logo">
          <h2 class="auth-heading">Welcome Back</h2>
          <p class="auth-sub">Sign in to your IntelliGov account</p>
          
          <form action="login.php" method="POST" name="login" onsubmit="return submitlogin()">
            <div class="form-group">
              <label>Username</label>
              <input type="text" name="username" id="inputUsername" placeholder="Enter your username" autocomplete="username">
              <span id="username-help" class="input-help"></span>
            </div>
            <div class="form-group">
              <label>Password</label>
              <input type="password" name="password" id="inputPassword" placeholder="Enter your password" autocomplete="current-password">
              <span id="password-help" class="input-help"></span>
            </div>
            <button type="submit" class="form-submit">Sign In</button>
          </form>
          <p class="form-footer">No account yet? <a onclick="switchTab('register')">Register here</a></p>
        </div>

        <!-- REGISTER PANEL -->
        <div class="auth-form-panel" id="panel-register">
          <img class="auth-logo" src="includes/images/intelligov.png" alt="IntelliGov" name="auth-igov-logo2">
          <h2 class="auth-heading">Create Account</h2>
          <p class="auth-sub">Join your organization's IntelliGov system</p>
          
          <form action="register.php" method="POST" name="register" onsubmit="return validateForm()">
            <div class="form-group">
              <label>Username</label>
              <input type="text" name="username" id="regUsername" class="inputs" placeholder="Choose a username" autocomplete="username">
              <span id="reg-username-help" class="input-help"></span>
            </div>
            <div class="form-group">
              <label>Mobile Number</label>
              <input type="tel" name="mobile" id="regMobile" class="inputs" placeholder="09XX XXX XXXX" autocomplete="tel" maxlength="11">
              <span id="reg-mobile-help" class="input-help"></span>
            </div>
            <div class="form-group">
              <label>Password</label>
              <input type="password" name="password" id="regPassword" class="inputs" placeholder="Create a strong password" autocomplete="new-password">
              <span id="reg-password-help" class="input-help"></span>
            </div>
            <button type="submit" name="register" class="form-submit">Create Account</button>
          </form>
          <p class="form-footer">Already have an account? <a onclick="switchTab('login')">Sign in</a></p>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- PRIVACY MODAL -->
<div id="privacy-overlay" onclick="if(event.target.id === 'privacy-overlay') hidePrivacy()">
  <div class="privacy-card reveal visible">
    <div class="privacy-header">
      <h2>PRIVACY NOTICE</h2>
      <p>IntelliGov: Intelligent Payroll Management System | LGU Polanco, Zamboanga del Norte</p>
    </div>
    <div class="privacy-body">
      <h3>1. Introduction</h3>
      <p>The Office of the Municipal Accountant of LGU Polanco ("the Office") is committed to protecting the privacy and security of the personal and sensitive personal information of all municipal employees. This Privacy Notice explains how IntelliGov collects, processes, and stores your data in accordance with the Data Privacy Act of 2012.</p>
      
      <h3>2. Data We Collect</h3>
      <p>To facilitate accurate payroll processing and statutory compliance, IntelliGov processes the following information:</p>
      <ul>
        <li><strong>Personal Identification:</strong> Full name, employee ID number, and position/designation.</li>
        <li><strong>Salary & Compensation:</strong> Basic Pay, PERA, RATA, and other authorized allowances.</li>
        <li><strong>Statutory Identification:</strong> GSIS, PhilHealth, and Pag-IBIG membership numbers, and BIR TIN.</li>
        <li><strong>Deductions:</strong> Loan amortizations, tax withholdings, and mandatory contributions.</li>
      </ul>
      <p><em>Note: IntelliGov does not collect, store, or process employee bank account numbers. Payroll distribution remains subject to existing LGU disbursement protocols.</em></p>

      <h3>3. Purpose of Processing</h3>
      <p>Your data is processed strictly for the following purposes:</p>
      <ul>
        <li>Calculation of monthly and supplemental payrolls.</li>
        <li>Generation of Payroll Registers (GOP) and individual Payslips.</li>
        <li>Preparation of mandatory remittance files for GSIS, PhilHealth, Pag-IBIG, and BIR.</li>
        <li>Internal accounting audits and budget monitoring.</li>
      </ul>

      <h3>4. Data Storage and Local Hosting</h3>
      <p>IntelliGov is hosted locally on a secured server managed by the Office of the Municipal Accountant. Your data is not stored in the cloud nor transferred to any external third-party hosting providers. Access is restricted to authorized personnel through Role-Based Access Control (RBAC).</p>

      <h3>5. Data Sharing and Disclosure</h3>
      <p>We only share your information with the following government agencies as required by law:</p>
      <ul>
        <li><strong>GSIS, PhilHealth, and Pag-IBIG:</strong> For membership contributions and loan remittances.</li>
        <li><strong>Bureau of Internal Revenue (BIR):</strong> For income tax reporting (Form 1601-C).</li>
        <li><strong>Commission on Audit (COA):</strong> For official auditing purposes.</li>
      </ul>

      <h3>6. Data Retention</h3>
      <p>Payroll records are retained in the IntelliGov system for the duration required by the National Archives of the Philippines (NAP) and COA regulations. Once the retention period expires, physical and digital records will be disposed of via secure shredding or permanent digital deletion.</p>

      <h3>7. Your Rights</h3>
      <p>Under the Data Privacy Act, you have the right to:</p>
      <ol style="margin-left: 20px; margin-bottom: 15px;">
        <li>Access your payroll data.</li>
        <li>Correct or update any inaccuracies in your records.</li>
        <li>Object to processing in case of unauthorized use.</li>
        <li>File a complaint if you feel your privacy rights have been violated.</li>
      </ol>

      <h3>8. Contact Information</h3>
      <p>For any concerns regarding your data privacy, please contact:</p>
      <p style="color: #fff; font-weight: 600;">Proserphine G. Godinez, CPA</p>
      <p>Municipal Accountant<br>
      Office of the Municipal Accountant, LGU Polanco<br>
      Polanco, Zamboanga del Norte</p>
    </div>
    <div class="privacy-footer">
      <button class="privacy-close-btn" onclick="hidePrivacy()">I Understand</button>
    </div>
  </div>
</div>

<!-- HERO -->
<section id="hero">
  <div class="hero-badge"><span class="badge-dot"></span>Now Live for Local Government Units</div>
  <div class="hero-logos">
    <div class="hero-logo-wrap">
      <img class="hero-logo" src="includes/images/polanco_logo.png" alt="Municipality of Polanco" id="hero-polanco-logo">
    </div>
    <div class="hero-divider"></div>
    <div class="hero-logo-wrap">
      <img class="hero-logo" src="includes/images/intelligov.png" alt="IntelliGov" id="hero-igov-logo">
    </div>
  </div>
  <h1><span class="h1-gradient">IntelliGov</span></h1>
  <p class="hero-tagline">"Smart Governance. Seamless Operations."</p>
  <p class="hero-sub">An intelligent Payroll Management platform purpose-built for local government units — powered by AI-assisted compliance and intelligent workflow automation.</p>
  <div class="hero-actions">
    <a href="#" onclick="showLogin(event)" class="btn-primary">Access Portal &rarr;</a>
    <a href="#modules" class="btn-outline">Explore Features</a>
  </div>
  <div class="scroll-hint">
    <span>Scroll</span>
    <div class="scroll-arrow"></div>
  </div>
</section>

<!-- STATS -->
<section id="stats">
  <div class="section-pad">
    <div class="stats-grid">
      <div class="stat-item reveal">
        <div class="stat-number">11</div>
        <div class="stat-label">Core Modules</div>
      </div>
      <div class="stat-item reveal">
        <div class="stat-number">4</div>
        <div class="stat-label">System Portals</div>
      </div>
      <div class="stat-item reveal">
        <div class="stat-number">6</div>
        <div class="stat-label">AI Risk Tiers</div>
      </div>
      <div class="stat-item reveal">
        <div class="stat-number">100%</div>
        <div class="stat-label">GAA Compliant</div>
      </div>
      <div class="stat-item reveal">
        <div class="stat-number">PWA</div>
        <div class="stat-label">Mobile Ready</div>
      </div>
    </div>
  </div>
</section>

<!-- MODULES -->
<section id="modules" class="section-pad">
  <div class="centered reveal">
    <div class="section-label">Platform Modules</div>
    <h2 class="section-heading">Everything Your LGU Needs</h2>
    <p class="section-desc">Comprehensive workforce management covering the full payroll lifecycle with AI-powered compliance enforcement.</p>
  </div>
  <div class="modules-grid">
    <div class="module-card reveal">
      <div class="module-icon">🗂️</div>
      <div class="module-title">Employee Records Management</div>
      <div class="module-desc">Centralized master data with personal info, employment history, position tracking, and automated photo management.</div>
    </div>
    <div class="module-card reveal">
      <div class="module-icon">🏛️</div>
      <div class="module-title">HRIS Configuration</div>
      <div class="module-desc">Departments, positions, and organizational structure with dual-layer role-based access control at frontend and backend.</div>
    </div>
    <div class="module-card reveal">
      <div class="module-icon">💰</div>
      <div class="module-title">Payroll Processing</div>
      <div class="module-desc">Full computation engine for monthly and semi-monthly pay frequencies. Automatic earnings, deductions, government shares, and journal entry generation.</div>
    </div>
    <div class="module-card reveal">
      <div class="module-icon">🤖</div>
      <div class="module-title">GAA Net Pay Intelligence</div>
      <div class="module-desc">AI-powered compliance enforcing the ₱5,000 GAA threshold with real-time validation and six intelligent risk tiers.</div>
      <span class="ai-badge">✦ AI-Powered</span>
    </div>
    <div class="module-card reveal">
      <div class="module-icon">🔒</div>
      <div class="module-title">Payroll Edit Blocking</div>
      <div class="module-desc">Two-layer smart protection: period-based blocking during active cycles and status-based blocking beyond Draft stage.</div>
    </div>
    <div class="module-card reveal">
      <div class="module-icon">🏦</div>
      <div class="module-title">Remittance Management</div>
      <div class="module-desc">GSIS, PhilHealth, Pag-IBIG, BIR/Tax remittance records with per-employee breakdowns and PDF export.</div>
    </div>
    <div class="module-card reveal">
      <div class="module-icon">🧾</div>
      <div class="module-title">Payslip Generation</div>
      <div class="module-desc">Per-employee payslips with full breakdowns. PDF download via TCPDF plus Summary List of Payroll (SLP) reports.</div>
    </div>
    <div class="module-card reveal">
      <div class="module-icon">📅</div>
      <div class="module-title">Leave Management</div>
      <div class="module-desc">Application submission, approval workflows, balance tracking, and credit management across multiple leave types.</div>
    </div>
  </div>
</section>

<!-- TECH STACK -->
<section id="tech" class="section-pad" style="background: rgba(255,255,255,0.02);">
  <div class="centered reveal">
    <div class="section-label">Enterprise-Grade Stack</div>
    <h2 class="section-heading">Built for Scale & Security</h2>
    <p class="section-desc">Utilizing industry-standard technologies to ensure reliability, performance, and data integrity.</p>
  </div>
  <div class="modules-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
    <div class="module-card reveal" style="padding: 24px; text-align: center;">
      <div class="module-title">Backend</div>
      <div class="module-desc">PHP 7.4+ with Optimized Query Engine</div>
    </div>
    <div class="module-card reveal" style="padding: 24px; text-align: center;">
      <div class="module-title">Database</div>
      <div class="module-desc">MariaDB/MySQL with ACID Compliance</div>
    </div>
    <div class="module-card reveal" style="padding: 24px; text-align: center;">
      <div class="module-title">Frontend</div>
      <div class="module-desc">AdminLTE 3 & Modern CSS3 Glassmorphism</div>
    </div>
    <div class="module-card reveal" style="padding: 24px; text-align: center;">
      <div class="module-title">Security</div>
      <div class="module-desc">Bcrypt Hashing & Dual-Layer Session Validation</div>
    </div>
  </div>
</section>

<footer>
  <div class="footer-grid">
    <div class="footer-brand">
      <div class="nav-title" style="margin-bottom: 20px;">IntelliGov</div>
      <p class="footer-desc">Next-generation payroll and HR system for Polanco, Zamboanga del Norte. Modernizing local governance through digital innovation.</p>
      <div class="footer-logos">
        <img src="includes/images/polanco_logo.png" class="footer-logo" alt="Polanco Logo" id="footer-polanco-logo">
        <img src="includes/images/intelligov.png" class="footer-logo" alt="IntelliGov Logo" id="footer-igov-logo">
      </div>
    </div>
    
    <div class="footer-links">
      <h4>Platform</h4>
      <ul>
        <li><a href="#hero">Overview</a></li>
        <li><a href="#modules">System Modules</a></li>
        <li><a href="#stats">Key Metrics</a></li>
      </ul>
    </div>
    
    <div class="footer-links">
      <h4>LGU Portals</h4>
      <ul>
        <li><a href="#" onclick="showLogin(event)">Employee Self-Service</a></li>
        <li><a href="#" onclick="showLogin(event)">Department Dashboard</a></li>
        <li><a href="#" onclick="showLogin(event)">HR/Admin Central</a></li>
      </ul>
    </div>
    
    <div class="footer-links">
      <h4>Resources</h4>
      <ul>
        <li><a href="#" onclick="showPrivacy(event)">Privacy Notice</a></li>
        <li><a href="#">Security Protocols</a></li>
        <li><a href="#">Compliance Center</a></li>
        <li><a href="#">Internal Documentation</a></li>
      </ul>
    </div>
  </div>

  <div class="footer-content" style="margin-top: 60px; padding-top: 30px; border-top: 1px solid var(--glass-border); text-align: center;">
    <p class="copy">
      &copy; <?php echo date("Y"); ?> Tagz Software Solutions. All rights reserved.
    </p>
    <p class="distribution-notice" style="opacity: 0.6; font-size: 0.75rem; margin-top: 10px;">
      <small>This software is proprietary and distributed to Municipal Government of Polanco. Unauthorized copying, modification, or distribution is strictly prohibited.</small>
    </p>
    <p class="copy" style="opacity: 0.6; font-size: 0.65rem; margin-top: 5px;">Powered by IntelliGov Enterprise Edition v3.0 & AdminLTE v3.1.0</p>
  </div>
</footer>

<!-- SCRIPTS -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="dist/js/urlcheck.js"></script>

<script>
  // ── ANIMATED BACKGROUND ──
  const canvas = document.getElementById('bg-canvas');
  const ctx = canvas.getContext('2d');
  let particles = [];
  let w, h;
  let t = 0;

  function resize() {
    w = canvas.width = window.innerWidth;
    h = canvas.height = window.innerHeight;
  }

  function initParticles() {
    particles = [];
    const count = Math.floor((w * h) / 12000);
    for (let i = 0; i < count; i++) {
      particles.push({
        x: Math.random() * w,
        y: Math.random() * h,
        vx: (Math.random() - 0.5) * 0.4,
        vy: (Math.random() - 0.5) * 0.4,
        size: Math.random() * 2 + 1
      });
    }
  }

  function drawBg() {
    ctx.clearRect(0, 0, w, h);
    ctx.fillStyle = 'rgba(41,182,246,0.15)';
    for (let i = 0; i < particles.length; i++) {
      let p = particles[i];
      p.x += p.vx;
      p.y += p.vy;

      if (p.x < 0) p.x = w;
      if (p.x > w) p.x = 0;
      if (p.y < 0) p.y = h;
      if (p.y > h) p.y = 0;

      ctx.beginPath();
      ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
      ctx.fill();

      for (let j = i + 1; j < particles.length; j++) {
        const dx = p.x - particles[j].x;
        const dy = p.y - particles[j].y;
        const dist = Math.sqrt(dx * dx + dy * dy);
        if (dist < 100) {
          ctx.beginPath();
          ctx.moveTo(p.x, p.y);
          ctx.lineTo(particles[j].x, particles[j].y);
          ctx.strokeStyle = `rgba(41,182,246,${0.06 * (1 - dist / 100)})`;
          ctx.lineWidth = 0.5;
          ctx.stroke();
        }
      }
    }
    t++;
    requestAnimationFrame(drawBg);
  }

  resize();
  initParticles();
  drawBg();
  window.addEventListener('resize', () => { resize(); initParticles(); });

  // ── NAV SCROLL ──
  window.addEventListener('scroll', () => {
    const nav = document.getElementById('main-nav');
    if(window.scrollY > 40) {
      nav.style.background = 'rgba(8,12,36,0.95)';
    } else {
      nav.style.background = 'rgba(8,12,36,0.75)';
    }
  });

  // ── HAMBURGER ──
  function toggleMenu() {
    const ham = document.getElementById('hamburger');
    const menu = document.getElementById('mobile-menu');
    ham.classList.toggle('open');
    menu.classList.toggle('show');
  }

  // ── LOGIN/AUTH UI ──
  function showLogin(e) {
    if (e) e.preventDefault();
    document.getElementById('login-overlay').classList.add('show');
    document.body.style.overflow = 'hidden';
  }
  function hideLogin() {
    document.getElementById('login-overlay').classList.remove('show');
    document.body.style.overflow = '';
  }
  function overlayClickClose(e) {
    if (e.target.id === 'login-overlay') hideLogin();
  }

  // ── PRIVACY UI ──
  function showPrivacy(e) {
    if (e) e.preventDefault();
    document.getElementById('privacy-overlay').classList.add('show');
    document.body.style.overflow = 'hidden';
  }
  function hidePrivacy() {
    document.getElementById('privacy-overlay').classList.remove('show');
    document.body.style.overflow = '';
  }

  function switchTab(tab) {
    const tabs = document.getElementById('auth-tabs');
    const track = document.getElementById('auth-forms-track');
    const btns = tabs.querySelectorAll('.auth-tab');
    if (tab === 'login') {
      tabs.classList.remove('register-mode');
      track.classList.remove('show-register');
      btns[0].classList.add('active'); btns[1].classList.remove('active');
    } else {
      tabs.classList.add('register-mode');
      track.classList.add('show-register');
      btns[1].classList.add('active'); btns[0].classList.remove('active');
    }
  }

  document.addEventListener('keydown', e => { 
    if (e.key === 'Escape') {
      hideLogin();
      hidePrivacy();
    }
  });

  // ── SCROLL REVEAL ──
  const revealEls = document.querySelectorAll('.reveal');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('visible');
      }
    });
  }, { threshold: 0.12 });
  revealEls.forEach(el => observer.observe(el));

  // ── LOGIN LOGIC ──
  function submitlogin() {
    var form = document.login;
    if (form.username.value == null || form.username.value == "") {
      $("#inputUsername").addClass("is-invalid");
      $('#username-help').html("Please provide your registered username.").addClass("text-red");
      form.username.focus();
      return false;
    }
    else if (form.password.value == null || form.password.value == "") {
      $("#inputPassword").addClass("is-invalid");
      $('#password-help').html("Password should not be empty.").addClass("text-red");
      form.password.focus();
      return false;
    }
    else {
      return true;
    }
  }

  // ── REGISTER LOGIC ──
  function validateForm() {
    var form = document.register;
    var userText = form.username;
    var mobileText = form.mobile;
    var password = form.password;

    var anum = /^[0-9a-zA-Z]+$/;
    var phoneno = /^\d{11}$/;

    // Basic required check
    if (userText.value == "" || mobileText.value == "" || password.value == "") {
      Swal.fire({ icon: 'error', title: 'Oops...', text: 'Please fill out all fields.' });
      return false;
    }

    if (!userText.value.match(anum)) {
      $("#regUsername").addClass("is-invalid");
      $('#reg-username-help').html("Username must contain alphanumeric characters only.").addClass("text-red");
      userText.focus();
      return false;
    }

    if (!mobileText.value.match(phoneno)) {
      $("#regMobile").addClass("is-invalid");
      $('#reg-mobile-help').html("Mobile must be 11 numeric characters only.").addClass("text-red");
      mobileText.focus();
      return false;
    }

    if (!password.value.match(anum)) {
      $("#regPassword").addClass("is-invalid");
      $('#reg-password-help').html("Password must be alphanumeric characters only.").addClass("text-red");
      password.focus();
      return false;
    }

    if (password.value.length < 6) {
      $("#regPassword").addClass("is-invalid");
      $('#reg-password-help').html("Password must be at least 6 characters long.").addClass("text-red");
      password.focus();
      return false;
    }

    return true;
  }

  // Handle URL parameters (Login status / Registration status)
  $(document).ready(function() {
    var query = getQuery();
    if (query !== undefined) {
      if (query.login == 'failed') {
        showLogin();
        if (query.reason == 'wrong_username') {
          $("#inputUsername").addClass("is-invalid");
          $('#username-help').html('Invalid username. Please try again.').addClass("text-red");
        }
        if (query.reason == 'wrong_password') {
          $("#inputUsername").val(query.user);
          $("#inputPassword").addClass("is-invalid");
          $('#password-help').html('Wrong password. Please try again.').addClass("text-red");
        }
        if (query.reason == 'not_privuser') {
          Swal.fire({
            title: 'Error',
            text: 'Access Denied!',
            icon: 'error',
            confirmButtonColor: '#1565c0'
          }).then((result) => {
            if (result.isConfirmed) { window.location.href = './'; }
          });
        }
      }
      
      if (query.register == 'success') {
        Swal.fire({
          title: 'Success!',
          text: 'Account created successfully. Please login.',
          icon: 'success',
          confirmButtonColor: '#1565c0'
        }).then((result) => {
          showLogin();
          switchTab('login');
        });
      }

      if (query.register == 'failed') {
        showLogin();
        switchTab('register');
        Swal.fire({
          title: 'Registration Failed',
          text: 'Username might already be taken. Please try another.',
          icon: 'error',
          confirmButtonColor: '#1565c0'
        });
      }
    }

    // Remove invalid state on focus
    $('input').focus(function() {
      $(this).removeClass("is-invalid");
      $(this).next('.input-help').empty().removeClass("text-red");
    });
  });
</script>

</body>
</html>
