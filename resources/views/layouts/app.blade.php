<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pengolahan Data Siswa per Satdik') — SIPANDU-WBK</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    
    <!-- Alpine.js for Interactive Sidebar -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&family=Inter:wght@400;500;600;700;800&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,500,1,0" rel="stylesheet">

    <style>
        :root {
            --o900: #10170C;
            --o800: #1D2A16;
            --o700: #2F4222;
            --o600: #3D5229;
            --o500: #556E3B;
            --o200: #C8D6B9;
            --o100: #E4ECE0;
            --o50: #F4F7F2;

            --gold: #C9A227;
            --gold2: #E8C862;
            --gold-bg: #FBF6E5;
            
            --bg: #F8FAF6;
            --card-bg: #FFFFFF;
            --text: #1E2818;
            --muted: #5B6A52;
            --line: #DCE4D6;

            --green: #2E7D32;
            --amber: #D99A1E;
            --red: #C62828;
            --blue: #1565C0;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.5;
            font-size: 14.5px;
        }

        .ms {
            font-family: 'Material Symbols Rounded';
            font-weight: normal; font-style: normal;
            line-height: 1; vertical-align: middle;
            display: inline-block;
        }

        /* NAVBAR */
        .app-topbar {
            background: #ffffff;
            color: #0f172a;
            height: 68px;
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e2e8f0;
            position: sticky; top: 0; z-index: 100;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .brand {
            display: flex; align-items: center; gap: 14px;
            text-decoration: none; color: #fff;
        }
        .brand-badge {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--gold), #8A680C);
            border-radius: 10px;
            display: grid; place-items: center;
            color: var(--o900); font-weight: 900; font-size: 22px;
        }
        .brand-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 17px; font-weight: 800;
            line-height: 1.2;
        }
        .brand-title span { color: var(--gold2); }
        .brand-sub {
            font-size: 11px; color: var(--o200);
            letter-spacing: 0.08em; text-transform: uppercase;
        }

        .nav-links {
            display: flex; align-items: center; gap: 8px;
        }
        .nav-link {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            font-weight: 600; font-size: 13.5px;
            padding: 8px 14px; border-radius: 8px;
            display: flex; align-items: center; gap: 6px;
            transition: all 0.2s;
        }
        .nav-link:hover, .nav-link.active {
            background: rgba(201,162,39,0.18);
            color: var(--gold2);
        }

        .user-tag {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            padding: 6px 12px; border-radius: 999px;
            font-size: 12px; display: flex; align-items: center; gap: 8px;
        }
        .user-tag .dot-online {
            width: 8px; height: 8px; border-radius: 50%;
            background: #4CAF50; display: inline-block;
        }

        /* MAIN WRAPPER */
        .container {
            max-width: 1360px;
            margin: 0 auto;
            padding: 28px 24px 60px;
        }

        /* HEADER BANNER */
        .header-banner {
            background: linear-gradient(135deg, var(--o800), var(--o700));
            color: #fff;
            border-radius: 18px;
            padding: 24px 28px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 8px 24px rgba(29,42,22,0.12);
            position: relative; overflow: hidden;
        }
        .header-banner::after {
            content: "";
            position: absolute; right: -60px; top: -60px;
            width: 240px; height: 240px; border-radius: 50%;
            background: radial-gradient(circle, rgba(201,162,39,0.15), transparent 70%);
        }
        .header-banner h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 24px; font-weight: 800; margin: 0 0 6px;
            color: #fff;
        }
        .header-banner p {
            margin: 0; color: var(--o200); font-size: 14px;
            max-width: 720px;
        }
        .pdp-badge {
            background: var(--gold-bg);
            color: #7A5C07;
            border: 1px solid var(--gold);
            padding: 6px 14px; border-radius: 10px;
            font-size: 12px; font-weight: 700;
            display: inline-flex; align-items: center; gap: 6px;
        }

        /* STATS GRID */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px; margin-bottom: 24px;
        }
        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 16px 20px;
            box-shadow: 0 4px 12px rgba(29,42,22,0.04);
            display: flex; align-items: center; gap: 16px;
        }
        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: grid; place-items: center;
            font-size: 26px; color: #fff;
        }
        .stat-val {
            font-family: 'Montserrat', sans-serif;
            font-size: 24px; font-weight: 900;
            color: var(--o800); line-height: 1.1;
        }
        .stat-lbl {
            font-size: 12.5px; color: var(--muted);
            margin-top: 3px; font-weight: 500;
        }

        /* SATDIK TABS */
        .satdik-nav {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            overflow-x: auto;
            padding-bottom: 4px;
        }
        .satdik-tab {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 10px 18px;
            text-decoration: none;
            color: var(--text);
            font-weight: 700;
            font-size: 13.5px;
            display: flex; align-items: center; gap: 8px;
            white-space: nowrap;
            transition: all 0.2s;
        }
        .satdik-tab:hover {
            border-color: var(--o600);
            background: var(--o50);
        }
        .satdik-tab.active {
            background: var(--o800);
            color: #fff;
            border-color: var(--o800);
            box-shadow: 0 4px 12px rgba(29,42,22,0.18);
        }
        .satdik-tab .tab-badge {
            background: rgba(0,0,0,0.08);
            padding: 2px 7px; border-radius: 6px;
            font-size: 11px;
        }
        .satdik-tab.active .tab-badge {
            background: rgba(255,255,255,0.2);
            color: var(--gold2);
        }

        /* CARD */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: 0 4px 14px rgba(29,42,22,0.04);
            overflow: hidden;
            margin-bottom: 24px;
        }
        .card-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--line);
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 16px; font-weight: 800;
            color: var(--o800); margin: 0;
            display: flex; align-items: center; gap: 8px;
        }
        .card-body {
            padding: 24px;
        }

        /* TABLE */
        .table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }
        table.data-table th {
            background: var(--o50);
            color: var(--o800);
            font-weight: 700;
            text-align: left;
            padding: 12px 14px;
            border-bottom: 1px solid var(--line);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        table.data-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #EEF2EB;
            vertical-align: middle;
        }
        table.data-table tr:hover td {
            background: #F9FAF7;
        }

        /* CUSTOM MILITARY PAGINATION */
        .custom-pagination {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            margin: 0;
            padding: 0;
        }
        .pagination-nav-list {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            list-style: none;
            padding: 0;
            margin: 0;
            flex-wrap: wrap;
        }
        .pagination-nav-list .page-item {
            display: inline-block;
        }
        .pagination-nav-list .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            min-width: 36px;
            height: 36px;
            padding: 0 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            color: var(--o800);
            background: #fff;
            border: 1px solid var(--line);
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
            transition: all 0.2s ease;
            user-select: none;
        }
        .pagination-nav-list .page-link:hover {
            background: var(--o50);
            border-color: var(--o700);
            color: var(--o900);
            transform: translateY(-1px);
        }
        .pagination-nav-list .page-item.active .page-link,
        .pagination-nav-list .page-link.current {
            background: linear-gradient(135deg, var(--o800), var(--o700));
            color: var(--gold2);
            border-color: var(--o900);
            box-shadow: 0 4px 10px rgba(29,42,22,0.22);
            font-weight: 800;
            cursor: default;
        }
        .pagination-nav-list .page-item.disabled .page-link {
            color: #94A3B8;
            background: #F8FAFC;
            border-color: #E2E8F0;
            cursor: not-allowed;
            opacity: 0.65;
            box-shadow: none;
            transform: none;
        }
        .pagination-nav-list .page-link.dots {
            border: none;
            background: transparent;
            color: var(--muted);
            min-width: 24px;
            padding: 0 4px;
            box-shadow: none;
            cursor: default;
        }
        .pagination-nav-list .btn-prev,
        .pagination-nav-list .btn-next {
            font-size: 12.5px;
            font-weight: 700;
            padding: 0 14px;
        }
        @media (max-width: 640px) {
            .pagination-nav-list .btn-prev span:not(.ms),
            .pagination-nav-list .btn-next span:not(.ms) {
                display: none;
            }
            .pagination-nav-list .page-link {
                min-width: 32px;
                height: 32px;
                padding: 0 8px;
                font-size: 12px;
            }
        }

        /* TABLE PAGINATION FOOTER WRAPPER */
        .table-pagination-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            background: #FAFAF8;
        }
        .table-pagination-info {
            font-size: 12.5px;
            color: var(--muted);
            font-weight: 500;
        }
        .table-pagination-info b {
            color: var(--o800);
            font-weight: 700;
        }
        @media (max-width: 640px) {
            .table-pagination-footer {
                padding: 14px 16px;
                flex-direction: column;
                align-items: center;
                gap: 12px;
                text-align: center;
            }
            .custom-pagination {
                justify-content: center;
                width: 100%;
            }
            .pagination-nav-list {
                justify-content: center;
            }
        }

        /* BADGES */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 9px; border-radius: 6px;
            font-size: 11.5px; font-weight: 700;
        }
        .badge-green { background: #E8F5E9; color: var(--green); }
        .badge-amber { background: #FFF8E1; color: #8A680C; }
        .badge-red   { background: #FFEBEE; color: var(--red); }
        .badge-blue  { background: #E3F2FD; color: var(--blue); }
        .badge-satdik{ background: var(--o100); color: var(--o800); }

        .masked-pill {
            font-family: 'Fira Code', monospace;
            background: #F3F4ED;
            border: 1px dashed var(--line);
            padding: 3px 8px; border-radius: 6px;
            font-size: 12px; color: var(--o800);
            display: inline-flex; align-items: center; gap: 5px;
            white-space: nowrap;
        }

        /* BUTTONS */
        .btn {
            font-family: 'Inter', sans-serif;
            font-size: 13px; font-weight: 700;
            padding: 8px 16px; border-radius: 9px;
            border: 1px solid transparent;
            cursor: pointer;
            display: inline-flex; align-items: center; gap: 6px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-primary {
            background: var(--o700); color: #fff;
        }
        .btn-primary:hover { background: var(--o800); }
        .btn-gold {
            background: var(--gold); color: var(--o900);
        }
        .btn-gold:hover { background: #B38F1F; }
        .btn-outline {
            border-color: var(--line); background: #fff; color: var(--text);
        }
        .btn-outline:hover { background: var(--o50); border-color: var(--o600); }
        .btn-danger {
            background: #FFEBEE; color: var(--red); border-color: #FFCDD2;
        }
        .btn-sm {
            padding: 5px 10px; font-size: 12px; border-radius: 7px;
        }

        /* MODAL */
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(16,23,12,0.7);
            backdrop-filter: blur(5px);
            display: none; place-items: center;
            z-index: 1000; padding: 20px;
        }
        .modal-overlay.active { display: grid; }
        .modal-card {
            background: #fff;
            width: 100%; max-width: 580px;
            border-radius: 18px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            overflow: hidden;
            animation: modalIn 0.2s ease-out;
        }
        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .modal-header {
            background: var(--o800); color: #fff;
            padding: 18px 24px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .modal-body { padding: 24px; }
        .modal-footer {
            padding: 16px 24px; background: var(--o50);
            border-top: 1px solid var(--line);
            display: flex; justify-content: flex-end; gap: 10px;
        }

        /* ALERT */
        .alert {
            padding: 12px 18px; border-radius: 10px;
            margin-bottom: 20px; font-size: 13.5px;
            display: flex; align-items: center; gap: 10px;
        }
        .alert-success { background: #E8F5E9; color: var(--green); border: 1px solid #C8E6C9; }
        .alert-info    { background: #E3F2FD; color: var(--blue); border: 1px solid #BBDEFB; }
        .alert-warning { background: var(--gold-bg); color: #7A5C07; border: 1px solid #FFE082; }

        /* FORM ELEMENTS */
        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block; font-weight: 700;
            font-size: 13px; margin-bottom: 6px;
            color: var(--o800);
        }
        .form-control {
            width: 100%; padding: 9px 13px;
            border: 1px solid var(--line); border-radius: 8px;
            font-family: inherit; font-size: 13.5px;
            outline: none; transition: border-color 0.2s;
        }
        .form-control:focus {
            border-color: var(--o700);
            box-shadow: 0 0 0 3px rgba(61,82,41,0.12);
        }
        .form-hint { font-size: 11.5px; color: var(--muted); margin-top: 4px; }

        /* =========================================================
           SIDEBAR LAYOUT SYSTEM (AUTO-COLLAPSE SUPPORT)
           ========================================================= */
        :root {
            --sidebar-collapsed-w: 72px;
            --sidebar-expanded-w: 275px;
        }

        .app-layout {
            display: flex;
            min-height: 100vh;
            background: var(--bg);
        }

        /* SIDEBAR COMPONENT - DEFAULT AUTO-COLLAPSED (72px) */
        .app-sidebar {
            width: var(--sidebar-collapsed-w);
            min-width: var(--sidebar-collapsed-w);
            background: #ffffff;
            color: #334155;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #e2e8f0;
            box-shadow: 4px 0 20px rgba(0,0,0,0.03);
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1), 
                        min-width 0.25s cubic-bezier(0.4, 0, 0.2, 1),
                        box-shadow 0.25s ease, 
                        transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* EXPAND ON HOVER WHEN IN AUTO-COLLAPSE MODE */
        body:not(.sidebar-pinned) .app-sidebar:hover,
        body:not(.sidebar-pinned) .app-sidebar.is-hovered {
            width: var(--sidebar-expanded-w);
            min-width: var(--sidebar-expanded-w);
            box-shadow: 12px 0 38px rgba(0,0,0,0.48);
        }

        /* PINNED STATE (LOCKED OPEN) */
        body.sidebar-pinned .app-sidebar {
            width: var(--sidebar-expanded-w);
            min-width: var(--sidebar-expanded-w);
        }

        /* MAIN CONTENT AREA */
        .main-wrapper {
            flex: 1;
            margin-left: var(--sidebar-collapsed-w);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: calc(100% - var(--sidebar-collapsed-w));
            transition: margin-left 0.25s cubic-bezier(0.4, 0, 0.2, 1), width 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body.sidebar-pinned .main-wrapper {
            margin-left: var(--sidebar-expanded-w);
            width: calc(100% - var(--sidebar-expanded-w));
        }

        /* SIDEBAR HEADER / BRAND */
        .sidebar-header {
            padding: 16px 12px 14px;
            border-bottom: 1px solid #e2e8f0;
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .sidebar-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #0f172a;
            overflow: hidden;
            flex: 1;
        }
        .sidebar-logo {
            width: 44px; height: 44px;
            min-width: 44px;
            background: #0f172a;
            border-radius: 12px;
            display: grid; place-items: center;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            flex-shrink: 0;
            margin: 0 auto;
            transition: margin 0.2s ease;
        }
        .sidebar-logo .ms { font-size: 26px; }
        
        .sidebar-brand-text {
            overflow: hidden;
            white-space: nowrap;
            opacity: 0;
            max-width: 0;
            transition: opacity 0.2s ease, max-width 0.25s ease;
        }
        body.sidebar-pinned .sidebar-brand-text,
        body:not(.sidebar-pinned) .app-sidebar:hover .sidebar-brand-text {
            opacity: 1;
            max-width: 190px;
        }
        body.sidebar-pinned .sidebar-logo,
        body:not(.sidebar-pinned) .app-sidebar:hover .sidebar-logo {
            margin: 0;
        }

        .sidebar-brand-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            font-weight: 900;
            letter-spacing: -0.01em;
            line-height: 1.2;
        }
        .sidebar-brand-title span { color: #2563eb; }
        .sidebar-brand-sub {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 600;
            margin-top: 2px;
        }

        .sidebar-pin-btn {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #94a3b8;
            border-radius: 8px;
            width: 28px; height: 28px;
            display: none;
            place-items: center;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
        }
        .sidebar-pin-btn:hover {
            background: #e2e8f0;
            color: #0f172a;
            border-color: #cbd5e1;
        }
        body.sidebar-pinned .sidebar-pin-btn {
            color: #0f172a;
            background: #f1f5f9;
            border-color: #cbd5e1;
        }
        body.sidebar-pinned .sidebar-pin-btn,
        body:not(.sidebar-pinned) .app-sidebar:hover .sidebar-pin-btn {
            display: grid;
        }

        .sidebar-wbk-badge {
            background: #fffbeb;
            border: 1px solid #fde68a;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 10.5px;
            font-weight: 700;
            color: #b45309;
            display: flex;
            align-items: center;
            justify-content: space-between;
            overflow: hidden;
            max-height: 0;
            opacity: 0;
            padding: 0;
            margin: 0;
            border-width: 0;
            transition: all 0.24s ease;
        }
        body.sidebar-pinned .sidebar-wbk-badge,
        body:not(.sidebar-pinned) .app-sidebar:hover .sidebar-wbk-badge {
            max-height: 40px;
            opacity: 1;
            padding: 4px 10px;
            margin-top: 4px;
            border-width: 1px;
        }

        /* SIDEBAR MENU NAVIGATION */
        .sidebar-nav {
            padding: 12px 8px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .sidebar-section-title {
            font-size: 0;
            height: 1px;
            padding: 0;
            margin: 8px 6px;
            background: #e2e8f0;
            overflow: hidden;
            transition: all 0.2s ease;
        }
        body.sidebar-pinned .sidebar-section-title,
        body:not(.sidebar-pinned) .app-sidebar:hover .sidebar-section-title {
            font-size: 10px;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            padding: 10px 10px 4px;
            margin: 6px 0 2px;
            height: auto;
            background: transparent;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 0;
            border-radius: 10px;
            color: #64748b;
            text-decoration: none;
            font-weight: 600;
            font-size: 13.5px;
            transition: all 0.18s ease;
            position: relative;
        }
        .sidebar-item:hover {
            color: #0f172a;
            background: #f1f5f9;
        }
        body.sidebar-pinned .sidebar-item,
        body:not(.sidebar-pinned) .app-sidebar:hover .sidebar-item {
            justify-content: space-between;
            padding: 9px 12px;
        }
        body.sidebar-pinned .sidebar-item.active,
        body:not(.sidebar-pinned) .app-sidebar:hover .sidebar-item.active {
            background: #f8fafc;
            color: #0f172a;
        }

        .sidebar-item-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
        }
        body.sidebar-pinned .sidebar-item-content,
        body:not(.sidebar-pinned) .app-sidebar:hover .sidebar-item-content {
            justify-content: flex-start;
            gap: 12px;
        }

        .sidebar-item-content .ms {
            font-size: 22px;
            color: #94a3b8;
            transition: color 0.18s ease, transform 0.18s ease;
            flex-shrink: 0;
        }
        .sidebar-item.active .sidebar-item-content .ms {
            color: #0f172a;
        }
        .sidebar-item-text {
            white-space: nowrap;
            overflow: hidden;
            opacity: 0;
            max-width: 0;
            transition: opacity 0.2s ease, max-width 0.24s ease;
        }
        body.sidebar-pinned .sidebar-item-text,
        body:not(.sidebar-pinned) .app-sidebar:hover .sidebar-item-text {
            opacity: 1;
            max-width: 170px;
        }

        .sidebar-item:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
        .sidebar-item:hover .sidebar-item-content .ms {
            color: #0f172a;
            transform: scale(1.08);
        }
        body.sidebar-pinned .sidebar-item.active,
        body:not(.sidebar-pinned) .app-sidebar:hover .sidebar-item.active {
            background: #f8fafc;
            color: #0f172a;
            border-left: 3.5px solid #0f172a;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .sidebar-item.active .sidebar-item-content .ms {
            color: #0f172a;
        }

        .sidebar-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 999px;
            background: #e2e8f0;
            color: #475569;
            white-space: nowrap;
            opacity: 0;
            max-width: 0;
            overflow: hidden;
            transition: opacity 0.2s ease, max-width 0.24s ease;
        }
        body.sidebar-pinned .sidebar-badge,
        body:not(.sidebar-pinned) .app-sidebar:hover .sidebar-badge {
            opacity: 1;
            max-width: 80px;
        }
        .sidebar-badge-gold {
            background: #dbeafe;
            color: #1e40af;
        }
        .sidebar-badge-green {
            background: #dcfce7;
            color: #166534;
        }
        .sidebar-badge-blue {
            background: #e0e7ff;
            color: #3730a3;
        }

        /* SLEEK FLOATING TOOLTIP WHEN COLLAPSED */
        body:not(.sidebar-pinned) .app-sidebar:not(:hover) .sidebar-item[data-tooltip]::after {
            content: attr(data-tooltip);
            position: absolute;
            left: calc(100% + 12px);
            top: 50%;
            transform: translateY(-50%) translateX(-4px);
            background: #0f172a;
            color: #ffffff;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.15s ease, transform 0.15s ease;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            z-index: 1200;
        }
        body:not(.sidebar-pinned) .app-sidebar:not(:hover) .sidebar-item:hover::after {
            opacity: 1;
            transform: translateY(-50%) translateX(0);
        }

        /* SIDEBAR FOOTER / USER CARD */
        .sidebar-footer {
            padding: 12px 10px;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
        }
        .sidebar-user-card {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
        }
        body.sidebar-pinned .sidebar-user-card,
        body:not(.sidebar-pinned) .app-sidebar:hover .sidebar-user-card {
            justify-content: flex-start;
            gap: 12px;
            padding: 2px 4px;
        }
        .sidebar-user-avatar {
            width: 38px; height: 38px;
            min-width: 38px;
            border-radius: 10px;
            background: #f1f5f9;
            border: 1.5px solid #cbd5e1;
            display: grid; place-items: center;
            font-size: 18px; color: #64748b;
            flex-shrink: 0;
        }
        .sidebar-user-info {
            overflow: hidden;
            white-space: nowrap;
            opacity: 0;
            max-width: 0;
            transition: opacity 0.2s ease, max-width 0.24s ease;
        }
        body.sidebar-pinned .sidebar-user-info,
        body:not(.sidebar-pinned) .app-sidebar:hover .sidebar-user-info {
            opacity: 1;
            max-width: 180px;
            flex: 1;
        }
        .sidebar-user-name {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }
        .sidebar-user-role {
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 1px;
        }
        .dot-pulse {
            width: 6.5px; height: 6.5px;
            background: #4CAF50;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 6px #4CAF50;
        }
        .sidebar-version-info {
            font-size: 10.5px;
            color: #94a3b8;
            margin-top: 8px;
            justify-content: space-between;
            display: none;
            overflow: hidden;
            white-space: nowrap;
        }
        body.sidebar-pinned .sidebar-version-info,
        body:not(.sidebar-pinned) .app-sidebar:hover .sidebar-version-info {
            display: flex;
        }

        /* TOPBAR IN SIDEBAR LAYOUT */
        .app-topbar {
            height: 64px;
            background: #fff;
            border-bottom: 1px solid var(--line);
            padding: 0 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }
        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .btn-sidebar-toggle {
            background: transparent;
            border: 1px solid var(--line);
            border-radius: 8px;
            width: 36px; height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--o800);
            transition: all 0.2s;
        }
        .btn-sidebar-toggle:hover {
            background: var(--o50);
            border-color: var(--o600);
            color: var(--gold);
        }
        .breadcrumb-trail {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--muted);
        }
        .breadcrumb-trail .current {
            color: var(--o800);
            font-weight: 800;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .lan-badge {
            background: #E8F5E9;
            color: #1B5E20;
            border: 1px solid #A5D6A7;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* BACKDROP FOR MOBILE */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(11,20,11,0.6);
            backdrop-filter: blur(4px);
            z-index: 1040;
        }

        /* RESPONSIVE BREAKPOINTS */
        @media (max-width: 992px) {
            .app-sidebar {
                width: 275px !important;
                min-width: 275px !important;
                transform: translateX(-100%);
            }
            .app-sidebar.open {
                transform: translateX(0);
                box-shadow: 10px 0 40px rgba(0,0,0,0.5);
            }
            .sidebar-brand-text,
            .sidebar-wbk-badge,
            .sidebar-item-text,
            .sidebar-badge,
            .sidebar-user-info,
            .sidebar-version-info {
                opacity: 1 !important;
                max-width: none !important;
                display: flex !important;
            }
            .sidebar-section-title {
                font-size: 10px !important;
                height: auto !important;
                padding: 10px 12px 4px !important;
                background: transparent !important;
            }
            .sidebar-item {
                justify-content: space-between !important;
                padding: 10px 14px !important;
            }
            .sidebar-item-content {
                justify-content: flex-start !important;
                gap: 12px !important;
            }
            .sidebar-logo {
                margin: 0 !important;
            }
            .sidebar-pin-btn {
                display: none !important;
            }
            .sidebar-backdrop.active {
                display: block;
            }
            .main-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }

        @media (max-width: 768px) {
            .container { padding: 18px 14px 40px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .header-banner { flex-direction: column; align-items: flex-start; gap: 14px; }
            .lan-badge { display: none; }
        }
    </style>
</head><body class="bg-slate-50 text-slate-800 font-sans antialiased overflow-hidden" 
      x-data="{ 
          sidebarPinned: localStorage.getItem('sipandu_sidebar_pinned') !== 'false',
          sidebarHovered: false,
          mobileSidebarOpen: false,
          
          togglePin() {
              this.sidebarPinned = !this.sidebarPinned;
              localStorage.setItem('sipandu_sidebar_pinned', this.sidebarPinned);
          },
          
          get isSidebarExpanded() {
              return this.sidebarPinned || this.sidebarHovered;
          }
      }">

<div class="flex h-screen w-full">
    
    <!-- BACKDROP MOBILE -->
    <div x-show="mobileSidebarOpen" 
         x-transition.opacity.duration.300ms
         @click="mobileSidebarOpen = false"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 lg:hidden" style="display: none;"></div>

    <!-- SIDEBAR -->
    <aside @mouseenter="sidebarHovered = true" 
           @mouseleave="sidebarHovered = false"
           :class="{ 
               'w-72': isSidebarExpanded, 
               'w-20': !isSidebarExpanded,
               'translate-x-0': mobileSidebarOpen,
               '-translate-x-full lg:translate-x-0': !mobileSidebarOpen
           }"
           class="fixed inset-y-0 left-0 z-50 flex flex-col bg-white border-r border-slate-200 transition-all duration-300 ease-in-out lg:static lg:h-screen lg:shrink-0 shadow-[4px_0_24px_rgba(0,0,0,0.02)]">
        
        <!-- BRAND HEADER -->
        <div class="flex items-center justify-between h-20 px-4 border-b border-slate-100 shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 overflow-hidden whitespace-nowrap outline-none">
                <div class="flex items-center justify-center w-12 h-12 shrink-0 transition-transform duration-300" :class="!isSidebarExpanded ? 'mx-auto' : ''">
                    <!-- USING THE NEW LOGO -->
                    <img src="{{ asset('img/rindam-logo.png') }}" alt="Logo" class="w-11 h-11 object-contain drop-shadow-sm" onerror="this.outerHTML='<span class=\'ms text-[28px] text-slate-800\'>shield_person</span>'">
                </div>
                <div class="flex flex-col transition-opacity duration-300" :class="isSidebarExpanded ? 'opacity-100 w-auto' : 'opacity-0 w-0'">
                    <div class="font-black text-lg text-slate-900 tracking-tight leading-tight font-['Montserrat']">
                        SIPANDU<span class="text-blue-600">-WBK</span>
                    </div>
                    <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-0.5">
                        Rindam III/Slw
                    </div>
                </div>
            </a>
            
            <button @click="togglePin()" 
                    class="hidden lg:flex items-center justify-center w-8 h-8 rounded-lg transition-colors duration-200"
                    :class="sidebarPinned ? 'bg-slate-100 text-slate-900' : 'bg-transparent text-slate-400 hover:bg-slate-50 hover:text-slate-600'"
                    x-show="isSidebarExpanded"
                    :title="sidebarPinned ? 'Lepas Pin (Otomatis Ciut)' : 'Pin Sidebar (Tetap Lebar)'">
                <span class="ms text-[18px]" x-text="sidebarPinned ? 'keep' : 'keep_off'"></span>
            </button>
        </div>

        <div class="px-4 py-3 shrink-0" x-show="isSidebarExpanded" x-transition.opacity.duration.300ms>
            <div class="flex items-center justify-between bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 text-amber-700">
                <div class="flex items-center gap-2">
                    <span class="ms text-[16px]">verified</span>
                    <span class="text-[11px] font-bold uppercase tracking-wider">Zona Integritas</span>
                </div>
                <span class="text-[11px] font-black">WBK</span>
            </div>
        </div>

        <!-- NAVIGATION MENU -->
        <nav class="flex-1 overflow-y-auto overflow-x-hidden p-3 flex flex-col gap-1 scrollbar-hide">
            
            <!-- SECTION TITLE -->
            <div class="mt-4 mb-2 first:mt-0 transition-all duration-300" :class="isSidebarExpanded ? 'px-3' : 'px-0 text-center'">
                <div x-show="!isSidebarExpanded" class="h-0.5 w-8 mx-auto bg-slate-200 rounded-full"></div>
                <span x-show="isSidebarExpanded" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pusat Komando</span>
            </div>

            <!-- MENU ITEM 1 -->
            <a href="{{ route('dashboard') }}" 
               class="group relative flex items-center p-3 rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-slate-900 text-white shadow-md' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}"
               :class="!isSidebarExpanded ? 'justify-center' : 'justify-between'">
                
                <div class="flex items-center gap-3">
                    <span class="ms text-[24px] transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-slate-600' }}">dashboard</span>
                    <span x-show="isSidebarExpanded" class="font-semibold text-[13.5px] whitespace-nowrap">Dashboard Utama</span>
                </div>
                
                <span x-show="isSidebarExpanded" class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-blue-100 text-blue-700">Live</span>
                
                <!-- TOOLTIP FOR COLLAPSED -->
                <div x-show="!isSidebarExpanded" class="absolute left-full ml-3 px-3 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50">
                    Dashboard Utama
                </div>
            </a>

            <!-- SECTION TITLE -->
            <div class="mt-4 mb-2 transition-all duration-300" :class="isSidebarExpanded ? 'px-3' : 'px-0 text-center'">
                <div x-show="!isSidebarExpanded" class="h-0.5 w-8 mx-auto bg-slate-200 rounded-full"></div>
                <span x-show="isSidebarExpanded" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Data & Kesehatan</span>
            </div>
            
            <a href="{{ route('students.index') }}" 
               class="group relative flex items-center p-3 rounded-xl transition-all duration-200 {{ request()->routeIs('students.index') || request()->routeIs('students.show') ? 'bg-slate-900 text-white shadow-md' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}"
               :class="!isSidebarExpanded ? 'justify-center' : 'justify-between'">
                <div class="flex items-center gap-3">
                    <span class="ms text-[24px] transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('students.index') || request()->routeIs('students.show') ? 'text-white' : 'text-slate-400 group-hover:text-slate-600' }}">groups</span>
                    <span x-show="isSidebarExpanded" class="font-semibold text-[13.5px] whitespace-nowrap">Data Serdik</span>
                </div>
                <span x-show="isSidebarExpanded" class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-slate-100 text-slate-600 {{ request()->routeIs('students.index') || request()->routeIs('students.show') ? 'bg-slate-700 text-slate-200' : '' }}">{{ \App\Models\Student::count() }}</span>
                <div x-show="!isSidebarExpanded" class="absolute left-full ml-3 px-3 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50">Data Serdik</div>
            </a>

            <a href="{{ route('programs.index') }}" 
               class="group relative flex items-center p-3 rounded-xl transition-all duration-200 {{ request()->routeIs('programs.*') ? 'bg-slate-900 text-white shadow-md' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}"
               :class="!isSidebarExpanded ? 'justify-center' : 'justify-between'">
                <div class="flex items-center gap-3">
                    <span class="ms text-[24px] transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('programs.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-600' }}">school</span>
                    <span x-show="isSidebarExpanded" class="font-semibold text-[13.5px] whitespace-nowrap">Program Satdik</span>
                </div>
                <div x-show="!isSidebarExpanded" class="absolute left-full ml-3 px-3 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50">Program Satdik</div>
            </a>

            <a href="{{ route('health.index') }}" 
               class="group relative flex items-center p-3 rounded-xl transition-all duration-200 {{ request()->routeIs('health.*') ? 'bg-slate-900 text-white shadow-md' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}"
               :class="!isSidebarExpanded ? 'justify-center' : 'justify-between'">
                <div class="flex items-center gap-3">
                    <span class="ms text-[24px] transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('health.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-600' }}">medical_services</span>
                    <span x-show="isSidebarExpanded" class="font-semibold text-[13.5px] whitespace-nowrap">Kesehatan Serdik</span>
                </div>
                <span x-show="isSidebarExpanded" class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-emerald-100 text-emerald-700">Medis</span>
                <div x-show="!isSidebarExpanded" class="absolute left-full ml-3 px-3 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50">Kesehatan Serdik</div>
            </a>

            <!-- SECTION TITLE -->
            <div class="mt-4 mb-2 transition-all duration-300" :class="isSidebarExpanded ? 'px-3' : 'px-0 text-center'">
                <div x-show="!isSidebarExpanded" class="h-0.5 w-8 mx-auto bg-slate-200 rounded-full"></div>
                <span x-show="isSidebarExpanded" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sistem</span>
            </div>
            
            @if(auth()->user()?->canModifyData())
            <a href="{{ route('students.import') }}" 
               class="group relative flex items-center p-3 rounded-xl transition-all duration-200 {{ request()->routeIs('students.import') ? 'bg-slate-900 text-white shadow-md' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}"
               :class="!isSidebarExpanded ? 'justify-center' : 'justify-between'">
                <div class="flex items-center gap-3">
                    <span class="ms text-[24px] transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('students.import') ? 'text-white' : 'text-slate-400 group-hover:text-slate-600' }}">upload_file</span>
                    <span x-show="isSidebarExpanded" class="font-semibold text-[13.5px] whitespace-nowrap">Input Data Excel</span>
                </div>
                <div x-show="!isSidebarExpanded" class="absolute left-full ml-3 px-3 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50">Input Data Excel</div>
            </a>
            @endif

            @if(auth()->user()?->isSuperAdmin())
            <a href="{{ route('settings.index') }}" 
               class="group relative flex items-center p-3 rounded-xl transition-all duration-200 {{ request()->routeIs('settings.*') ? 'bg-slate-900 text-white shadow-md' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}"
               :class="!isSidebarExpanded ? 'justify-center' : 'justify-between'">
                <div class="flex items-center gap-3">
                    <span class="ms text-[24px] transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('settings.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-600' }}">settings</span>
                    <span x-show="isSidebarExpanded" class="font-semibold text-[13.5px] whitespace-nowrap">Pengaturan</span>
                </div>
                <span x-show="isSidebarExpanded" class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-rose-100 text-rose-800">Admin</span>
                <div x-show="!isSidebarExpanded" class="absolute left-full ml-3 px-3 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50">Pengaturan</div>
            </a>

            <a href="{{ route('users.index') }}" 
               class="group relative flex items-center p-3 rounded-xl transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-slate-900 text-white shadow-md' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}"
               :class="!isSidebarExpanded ? 'justify-center' : 'justify-between'">
                <div class="flex items-center gap-3">
                    <span class="ms text-[24px] transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('users.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-600' }}">manage_accounts</span>
                    <span x-show="isSidebarExpanded" class="font-semibold text-[13.5px] whitespace-nowrap">Manajemen Akun</span>
                </div>
                <span x-show="isSidebarExpanded" class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-rose-100 text-rose-800">Admin</span>
                <div x-show="!isSidebarExpanded" class="absolute left-full ml-3 px-3 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50">Manajemen Akun</div>
            </a>
            @endif
            
            <a href="{{ route('students.audit-logs') }}" 
               class="group relative flex items-center p-3 rounded-xl transition-all duration-200 {{ request()->routeIs('students.audit-logs') ? 'bg-slate-900 text-white shadow-md' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}"
               :class="!isSidebarExpanded ? 'justify-center' : 'justify-between'">
                <div class="flex items-center gap-3">
                    <span class="ms text-[24px] transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('students.audit-logs') ? 'text-white' : 'text-slate-400 group-hover:text-slate-600' }}">policy</span>
                    <span x-show="isSidebarExpanded" class="font-semibold text-[13.5px] whitespace-nowrap">Audit Log</span>
                </div>
                <div x-show="!isSidebarExpanded" class="absolute left-full ml-3 px-3 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50">Audit Log</div>
            </a>

        </nav>

        <!-- USER FOOTER -->
        <div class="p-3 border-t border-slate-100 bg-slate-50 shrink-0">
            <div class="flex items-center justify-between" :class="!isSidebarExpanded ? 'justify-center' : ''">
                <div class="flex items-center gap-2.5 overflow-hidden">
                    <div class="w-10 h-10 rounded-xl {{ auth()->user()?->isSuperAdmin() ? 'bg-rose-100 text-rose-800' : (auth()->user()?->isDanrindam() ? 'bg-amber-100 text-amber-800' : (auth()->user()?->isOperatorDanrindam() ? 'bg-indigo-100 text-indigo-800' : (auth()->user()?->isOperatorSatdik() ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700'))) }} border border-white shadow-sm flex items-center justify-center shrink-0">
                        <span class="ms text-[20px]">{{ auth()->user()?->isSuperAdmin() ? 'admin_panel_settings' : (auth()->user()?->isDanrindam() ? 'military_tech' : (auth()->user()?->isOperatorDanrindam() ? 'stars' : (auth()->user()?->isOperatorSatdik() ? 'support_agent' : 'shield_person'))) }}</span>
                    </div>
                    <div class="flex flex-col overflow-hidden" x-show="isSidebarExpanded" x-transition.opacity.duration.300ms>
                        <span class="text-xs font-bold text-slate-800 truncate" title="{{ auth()->user()->name ?? 'Prajurit Rindam' }}">{{ auth()->user()->name ?? 'Prajurit Rindam' }}</span>
                        <span class="text-[10px] font-semibold text-slate-500 truncate">
                            @if(auth()->user()?->isSuperAdmin())
                                <span class="text-rose-700 font-bold">👑 SUPER ADMIN</span>
                            @elseif(auth()->user()?->isDanrindam())
                                <span class="text-amber-700 font-bold">⭐ DANRINDAM (VIEW ONLY)</span>
                            @elseif(auth()->user()?->isOperatorDanrindam())
                                <span class="text-indigo-700 font-bold">🎖️ OPERATOR PUSAT (5 SATDIK)</span>
                            @elseif(auth()->user()?->isOperatorSatdik() && auth()->user()?->satdik)
                                <span class="text-emerald-700 font-bold">🔒 {{ auth()->user()->satdik->code }}</span>
                            @else
                                {{ strtoupper(auth()->user()?->role_code ?? 'PENGGUNA') }}
                            @endif
                        </span>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}" x-show="isSidebarExpanded" x-transition.opacity.duration.300ms class="shrink-0">
                    @csrf
                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Keluar dari Sistem (Logout)">
                        <span class="ms text-[20px]">logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50">
        
        <!-- TOPBAR -->
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-4 lg:px-8 shrink-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                <!-- Mobile Menu Button -->
                <button @click="mobileSidebarOpen = true" class="lg:hidden p-2 -ml-2 rounded-lg text-slate-500 hover:bg-slate-100">
                    <span class="ms text-[26px]">menu</span>
                </button>
                
                <!-- BREADCRUMB -->
                <div class="hidden sm:flex items-center gap-2 text-sm font-medium">
                    <span class="text-slate-400">SIPANDU-WBK</span>
                    <span class="ms text-slate-300 text-[18px]">chevron_right</span>
                    <span class="text-slate-800 font-bold">
                        @if(request()->routeIs('dashboard'))
                            Dashboard Eksekutif
                        @elseif(request()->routeIs('settings.*'))
                            Pengaturan Sistem
                        @elseif(request()->routeIs('users.*'))
                            Manajemen Akun Pengguna
                        @elseif(request()->routeIs('health.*'))
                            Kesehatan Serdik
                        @elseif(request()->routeIs('students.import'))
                            Impor Data Serdik
                        @elseif(request()->routeIs('students.audit-logs'))
                            Audit Log
                        @else
                            Buku Induk Data Serdik
                        @endif
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3 lg:gap-5">
                <!-- User Scope Badge -->
                @if(auth()->check())
                    <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-bold {{ auth()->user()->isSuperAdmin() ? 'bg-rose-50 border-rose-200 text-rose-800' : (auth()->user()->isDanrindam() ? 'bg-amber-50 border-amber-200 text-amber-800' : (auth()->user()->isOperatorDanrindam() ? 'bg-indigo-50 border-indigo-200 text-indigo-800' : (auth()->user()->isOperatorSatdik() ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-slate-100 border-slate-200 text-slate-700'))) }}">
                        <span class="ms text-[16px]">{{ auth()->user()->isSuperAdmin() ? 'admin_panel_settings' : (auth()->user()->isDanrindam() ? 'visibility' : (auth()->user()->isOperatorDanrindam() ? 'stars' : (auth()->user()->isOperatorSatdik() ? 'lock' : 'verified_user'))) }}</span>
                        <span>{{ auth()->user()->isSuperAdmin() ? 'Super Admin (Sistem & Akun)' : (auth()->user()->isDanrindam() ? 'Danrindam (Monitoring 5 Satdik)' : (auth()->user()->isOperatorDanrindam() ? 'Operator Pusat (Kelola 5 Satdik)' : ('Satdik ' . (auth()->user()->satdik?->code ?? 'Terkunci')))) }}</span>
                    </div>
                @endif

                <!-- LAN Indicator -->
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-emerald-50 border border-emerald-100 rounded-full text-emerald-700">
                    <span class="relative flex h-2.5 w-2.5">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <span class="text-xs font-bold tracking-wide">LAN AKTIF</span>
                </div>
                
                <!-- Action Button -->
                <button onclick="if(typeof openCreateStudentModal === 'function') { openCreateStudentModal(); } else { window.location='{{ route('students.index', ['action' => 'create']) }}'; }" 
                        class="flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg font-semibold text-sm shadow-sm transition-colors duration-200">
                    <span class="ms text-[20px]">add</span>
                    <span class="hidden sm:block">Tambah Serdik</span>
                </button>

                <!-- Logout Button in Topbar -->
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Keluar / Logout">
                        <span class="ms text-[22px]">logout</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- MAIN SCROLLABLE AREA -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 lg:p-8">
            
            <!-- ALERTS -->
            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800">
                    <span class="ms text-[24px] text-emerald-500">check_circle</span>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800">
                    <span class="ms text-[24px] text-red-500">error</span>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <!-- YIELD CONTENT -->
            @yield('content')
            
        </main>
    </div>
</div>

<script src="{{ asset('js/chart.umd.min.js') }}"></script>
@yield('scripts')

</body>
</html>
