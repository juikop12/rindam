<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pengolahan Data Siswa per Satdik') — SIPANDU-WBK</title>

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
        .topbar {
            background: var(--o900);
            color: #fff;
            height: 68px;
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid var(--gold);
            position: sticky; top: 0; z-index: 100;
            box-shadow: 0 4px 18px rgba(16,23,12,0.25);
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
            background: linear-gradient(180deg, #0B140B 0%, #142211 40%, #0E180E 100%);
            color: #fff;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(201,162,39,0.22);
            box-shadow: 4px 0 20px rgba(0,0,0,0.25);
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
            border-bottom: 1px solid rgba(255,255,255,0.08);
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
            color: #fff;
            overflow: hidden;
            flex: 1;
        }
        .sidebar-logo {
            width: 44px; height: 44px;
            min-width: 44px;
            background: linear-gradient(135deg, var(--gold2), #9E7D17);
            border-radius: 12px;
            display: grid; place-items: center;
            color: var(--o900);
            box-shadow: 0 4px 12px rgba(201,162,39,0.35);
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
        .sidebar-brand-title span { color: var(--gold2); }
        .sidebar-brand-sub {
            font-size: 10px;
            color: var(--o200);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 600;
            margin-top: 2px;
        }

        .sidebar-pin-btn {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            color: rgba(255,255,255,0.6);
            border-radius: 8px;
            width: 28px; height: 28px;
            display: none;
            place-items: center;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
        }
        .sidebar-pin-btn:hover {
            background: rgba(201,162,39,0.25);
            color: var(--gold2);
            border-color: var(--gold);
        }
        body.sidebar-pinned .sidebar-pin-btn {
            color: var(--gold2);
            background: rgba(201,162,39,0.2);
            border-color: var(--gold);
        }
        body.sidebar-pinned .sidebar-pin-btn,
        body:not(.sidebar-pinned) .app-sidebar:hover .sidebar-pin-btn {
            display: grid;
        }

        .sidebar-wbk-badge {
            background: rgba(201,162,39,0.12);
            border: 1px solid rgba(201,162,39,0.3);
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 10.5px;
            font-weight: 700;
            color: var(--gold2);
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
            background: rgba(255,255,255,0.08);
            overflow: hidden;
            transition: all 0.2s ease;
        }
        body.sidebar-pinned .sidebar-section-title,
        body:not(.sidebar-pinned) .app-sidebar:hover .sidebar-section-title {
            font-size: 10px;
            font-weight: 800;
            color: rgba(255,255,255,0.4);
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
            color: rgba(255,255,255,0.78);
            text-decoration: none;
            font-weight: 600;
            font-size: 13.5px;
            transition: all 0.18s ease;
            position: relative;
        }
        body.sidebar-pinned .sidebar-item,
        body:not(.sidebar-pinned) .app-sidebar:hover .sidebar-item {
            justify-content: space-between;
            padding: 9px 12px;
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
            color: rgba(255,255,255,0.7);
            transition: color 0.18s ease, transform 0.18s ease;
            flex-shrink: 0;
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
            background: rgba(255,255,255,0.08);
            color: #fff;
        }
        .sidebar-item:hover .sidebar-item-content .ms {
            color: var(--gold2);
            transform: scale(1.08);
        }
        .sidebar-item.active {
            background: linear-gradient(90deg, rgba(201,162,39,0.25) 0%, rgba(201,162,39,0.08) 100%);
            color: #fff;
            border-left: 3.5px solid var(--gold);
            box-shadow: 0 2px 10px rgba(0,0,0,0.18);
        }
        .sidebar-item.active .sidebar-item-content .ms {
            color: var(--gold2);
        }

        .sidebar-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 999px;
            background: rgba(255,255,255,0.12);
            color: #fff;
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
            background: var(--gold);
            color: var(--o900);
        }
        .sidebar-badge-green {
            background: #2E7D32;
            color: #fff;
        }
        .sidebar-badge-blue {
            background: #1565C0;
            color: #fff;
        }

        /* SLEEK FLOATING TOOLTIP WHEN COLLAPSED */
        body:not(.sidebar-pinned) .app-sidebar:not(:hover) .sidebar-item[data-tooltip]::after {
            content: attr(data-tooltip);
            position: absolute;
            left: calc(100% + 12px);
            top: 50%;
            transform: translateY(-50%) translateX(-4px);
            background: #0B140B;
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.15s ease, transform 0.15s ease;
            border: 1px solid rgba(201,162,39,0.35);
            box-shadow: 0 4px 16px rgba(0,0,0,0.4);
            z-index: 1200;
        }
        body:not(.sidebar-pinned) .app-sidebar:not(:hover) .sidebar-item:hover::after {
            opacity: 1;
            transform: translateY(-50%) translateX(0);
        }

        /* SIDEBAR FOOTER / USER CARD */
        .sidebar-footer {
            padding: 12px 10px;
            border-top: 1px solid rgba(255,255,255,0.08);
            background: rgba(0,0,0,0.2);
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
            background: linear-gradient(135deg, var(--o700), var(--o500));
            border: 1.5px solid var(--gold);
            display: grid; place-items: center;
            font-size: 18px; color: #fff;
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
            color: #fff;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }
        .sidebar-user-role {
            font-size: 11px;
            color: var(--gold2);
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
            color: rgba(255,255,255,0.4);
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
</head>
<body>

<div class="app-layout">
    <!-- BACKDROP MOBILE -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

    <!-- SIDEBAR -->
    <aside class="app-sidebar" id="appSidebar">
        <!-- BRAND -->
        <div class="sidebar-header">
            <div class="sidebar-header-row">
                <a href="{{ route('dashboard') }}" class="sidebar-brand">
                    <div class="sidebar-logo">
                        <span class="ms">shield_person</span>
                    </div>
                    <div class="sidebar-brand-text">
                        <div class="sidebar-brand-title">SIPANDU<span>-WBK</span></div>
                        <div class="sidebar-brand-sub">RINDAM III / SILIWANGI</div>
                    </div>
                </a>
                <button type="button" class="sidebar-pin-btn" id="sidebarPinBtn" onclick="toggleSidebarPin(event)" title="Kunci Sidebar Terbuka / Mode Otomatis Ciut">
                    <span class="ms" id="pinIcon" style="font-size:16px;">keep_off</span>
                </button>
            </div>
            <div class="sidebar-wbk-badge">
                <span><span class="ms" style="font-size:14px; vertical-align:text-top;">verified</span> ZONA INTEGRITAS</span>
                <span>WBK</span>
            </div>
        </div>

        <!-- NAVIGATION -->
        <nav class="sidebar-nav">
            <div class="sidebar-section-title">PUSAT KOMANDO EKSEKUTIF</div>

            <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" data-tooltip="Dashboard Utama">
                <div class="sidebar-item-content">
                    <span class="ms">dashboard</span>
                    <span class="sidebar-item-text">Dashboard Utama</span>
                </div>
                <span class="sidebar-badge sidebar-badge-gold">Live</span>
            </a>

            <div class="sidebar-section-title" style="margin-top:10px;">PENGELOLAAN DATA & KESEHATAN</div>
            
            <a href="{{ route('students.index') }}" class="sidebar-item {{ request()->routeIs('students.index') || request()->routeIs('students.show') ? 'active' : '' }}" data-tooltip="Data Serdik">
                <div class="sidebar-item-content">
                    <span class="ms">groups</span>
                    <span class="sidebar-item-text">Data Serdik</span>
                </div>
                <span class="sidebar-badge sidebar-badge-gold">{{ \App\Models\Student::count() }}</span>
            </a>

            <a href="{{ route('programs.index') }}" class="sidebar-item {{ request()->routeIs('programs.*') ? 'active' : '' }}" data-tooltip="Program Satdik">
                <div class="sidebar-item-content">
                    <span class="ms">school</span>
                    <span class="sidebar-item-text">Program Satdik</span>
                </div>
                <span class="sidebar-badge sidebar-badge-gold">TA 2026</span>
            </a>

            <a href="{{ route('health.index') }}" class="sidebar-item {{ request()->routeIs('health.*') ? 'active' : '' }}" data-tooltip="Kesehatan Serdik">
                <div class="sidebar-item-content">
                    <span class="ms">medical_services</span>
                    <span class="sidebar-item-text">Kesehatan Serdik</span>
                </div>
                <span class="sidebar-badge sidebar-badge-green">Poliklinik</span>
            </a>

            <a href="{{ route('students.import') }}" class="sidebar-item {{ request()->routeIs('students.import') ? 'active' : '' }}" data-tooltip="Input Format Excel">
                <div class="sidebar-item-content">
                    <span class="ms">upload_file</span>
                    <span class="sidebar-item-text">Input Format Excel</span>
                </div>
                <span class="sidebar-badge sidebar-badge-gold">Excel</span>
            </a>

            <div class="sidebar-section-title" style="margin-top:10px;">PENGAWASAN & ZI (AREA 5)</div>

            <a href="{{ route('students.audit-logs') }}" class="sidebar-item {{ request()->routeIs('students.audit-logs') ? 'active' : '' }}" data-tooltip="Audit Trail SIPANDU">
                <div class="sidebar-item-content">
                    <span class="ms">policy</span>
                    <span class="sidebar-item-text">Audit Trail Keamanan</span>
                </div>
                <span class="sidebar-badge sidebar-badge-blue">SIPANDU</span>
            </a>

            <div class="sidebar-section-title" style="margin-top:10px;">KONFIGURASI SISTEM</div>

            <a href="{{ route('settings.index') }}" class="sidebar-item {{ request()->routeIs('settings.*') ? 'active' : '' }}" data-tooltip="Pejabat & Pengaturan">
                <div class="sidebar-item-content">
                    <span class="ms">manage_accounts</span>
                    <span class="sidebar-item-text">Pejabat & Pengaturan</span>
                </div>
                <span class="sidebar-badge sidebar-badge-gold">Pimpinan</span>
            </a>
        </nav>

        <!-- USER PROFILE FOOTER -->
        <div class="sidebar-footer">
            <div class="sidebar-user-card">
                <div class="sidebar-user-avatar">
                    <span class="ms">military_tech</span>
                </div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name">{{ auth()->user()->name ?? \App\Models\SystemSetting::get('danrindam_name', 'Danrindam III/Siliwangi') }}</div>
                    <div class="sidebar-user-role">
                        <span class="dot-pulse"></span>
                        <span>{{ strtoupper(auth()->user()->role_code ?? 'PIMPINAN') }}</span>
                    </div>
                </div>
            </div>
            <div class="sidebar-version-info">
                <span>SIPANDU-WBK v2.4</span>
                <span>TNI-AD</span>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT WRAPPER -->
    <div class="main-wrapper">
        <!-- TOPBAR -->
        <header class="app-topbar">
            <div class="topbar-left">
                <button class="btn-sidebar-toggle" onclick="toggleSidebar()" aria-label="Toggle Sidebar" title="Buka / Ciutkan Sidebar (Auto-Collapse)">
                    <span class="ms">menu</span>
                </button>
                <div class="breadcrumb-trail">
                    <span>SIPANDU-WBK</span>
                    <span class="ms" style="font-size:16px;">chevron_right</span>
                    <span class="current">
                        @if(request()->routeIs('dashboard'))
                            Pusat Komando & Dashboard Eksekutif
                        @elseif(request()->routeIs('settings.*'))
                            Pengaturan Sistem & Pejabat Pimpinan
                        @elseif(request()->routeIs('health.*'))
                            Kesehatan & Rekam Medis Serdik
                        @elseif(request()->routeIs('students.import'))
                            Penginputan Data Serdik Format Excel
                        @elseif(request()->routeIs('students.audit-logs'))
                            Audit Log Akses Data Pribadi (SIPANDU-WBK)
                        @else
                            Buku Induk & Data Serdik
                        @endif
                    </span>
                </div>
            </div>

            <div class="topbar-right">
                <div class="lan-badge" title="Tersedia di Jaringan Lokal Wi-Fi">
                    <span class="dot-pulse"></span>
                    <span>Wi-Fi LAN: 192.168.100.20</span>
                </div>
                <button type="button" class="btn btn-gold btn-sm" onclick="if(typeof openCreateStudentModal === 'function') { openCreateStudentModal(); } else { window.location='{{ route('students.index', ['action' => 'create']) }}'; }">
                    <span class="ms" style="font-size:16px;">add</span> Tambah Siswa
                </button>
            </div>
        </header>

        <!-- MAIN CONTAINER -->
        <main class="container">
            @if(session('success'))
                <div class="alert alert-success">
                    <span class="ms" style="font-size:22px;">check_circle</span>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger" style="background:#FFEBEE; color:var(--red); border:1px solid #FFCDD2;">
                    <span class="ms" style="font-size:22px;">error</span>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script src="{{ asset('js/chart.umd.min.js') }}"></script>

<script>
    // State management for Sidebar Auto-Collapse
    const SIDEBAR_STORAGE_KEY = 'sipandu_sidebar_pinned';

    function initSidebar() {
        // Default is AUTO-COLLAPSE (not pinned)
        const isPinned = localStorage.getItem(SIDEBAR_STORAGE_KEY) === 'true';
        if (isPinned) {
            document.body.classList.add('sidebar-pinned');
        } else {
            document.body.classList.remove('sidebar-pinned');
        }
        updatePinIcon(isPinned);
    }

    function toggleSidebarPin(e) {
        if (e) {
            e.stopPropagation();
            e.preventDefault();
        }
        const currentlyPinned = document.body.classList.contains('sidebar-pinned');
        const nextState = !currentlyPinned;
        
        if (nextState) {
            document.body.classList.add('sidebar-pinned');
            localStorage.setItem(SIDEBAR_STORAGE_KEY, 'true');
        } else {
            document.body.classList.remove('sidebar-pinned');
            localStorage.setItem(SIDEBAR_STORAGE_KEY, 'false');
        }
        updatePinIcon(nextState);
    }

    function updatePinIcon(isPinned) {
        const pinIcon = document.getElementById('pinIcon');
        const pinBtn = document.getElementById('sidebarPinBtn');
        if (pinIcon && pinBtn) {
            if (isPinned) {
                pinIcon.textContent = 'keep';
                pinBtn.title = 'Sidebar Terkunci Terbuka. Klik untuk beralih ke Mode Otomatis Ciut (Auto-Collapse)';
                pinBtn.style.color = 'var(--gold2)';
                pinBtn.style.background = 'rgba(201,162,39,0.2)';
            } else {
                pinIcon.textContent = 'keep_off';
                pinBtn.title = 'Mode Otomatis Ciut Aktif (Menciut jika kursor menjauh). Klik untuk Mengunci Terbuka';
                pinBtn.style.color = 'rgba(255,255,255,0.6)';
                pinBtn.style.background = 'rgba(255,255,255,0.08)';
            }
        }
    }

    function toggleSidebar() {
        if (window.innerWidth <= 992) {
            const sidebar = document.getElementById('appSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) sidebar.classList.toggle('open');
            if (backdrop) backdrop.classList.toggle('active');
        } else {
            // Pada desktop, klik tombol menu topbar beralih antara Pinned dan Auto-Collapse
            toggleSidebarPin();
        }
    }

    // Listener otomatis saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        initSidebar();

        // Di mobile: tutup sidebar otomatis saat mengklik link menu apa pun
        const sidebarItems = document.querySelectorAll('.sidebar-item');
        sidebarItems.forEach(item => {
            item.addEventListener('click', function() {
                if (window.innerWidth <= 992) {
                    const sidebar = document.getElementById('appSidebar');
                    const backdrop = document.getElementById('sidebarBackdrop');
                    if (sidebar) sidebar.classList.remove('open');
                    if (backdrop) backdrop.classList.remove('active');
                }
            });
        });

        // Di desktop: jika sidebar tidak dipin dan kursor meninggalkan area sidebar, pastikan menciut
        const sidebar = document.getElementById('appSidebar');
        const mainWrapper = document.querySelector('.main-wrapper');
        if (mainWrapper && sidebar) {
            mainWrapper.addEventListener('mouseenter', function() {
                if (!document.body.classList.contains('sidebar-pinned')) {
                    sidebar.classList.remove('is-hovered');
                }
            });
        }
    });
</script>

@yield('scripts')

</body>
</html>
