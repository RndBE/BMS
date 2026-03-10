<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BMS – {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        :root {
            --sidebar-w: 220px;
            --topbar-h: 60px;
            --primary: #1e2a45;
            --primary-light: #273554;
            --accent: #4f7dfc;
            --accent-hover: #3a65e0;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --text-muted: #94a3b8;
            --bg-main: #f0f4f8;
            --card-bg: #ffffff;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-main); display: flex; min-height: 100vh; }

        /* ── SIDEBAR ── */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--primary);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
        }
        .sidebar-logo {
            padding: 18px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: center; gap: 10px;
            color: white;
        }
        .sidebar-logo svg { width: 38px; height: 38px; }
        .logo-text { font-size: 11px; font-weight: 600; letter-spacing: 1px; color: #93a5c4; text-transform: uppercase; }
        .nav-section { padding: 16px 0; flex: 1; }
        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 20px;
            color: #93a5c4;
            text-decoration: none;
            font-size: 13.5px; font-weight: 500;
            border-radius: 0; cursor: pointer;
            transition: all .2s;
            position: relative;
        }
        .nav-item:hover { color: #fff; background: var(--primary-light); }
        .nav-item.active {
            color: #fff;
            background: var(--primary-light);
        }
        .nav-item.active::before {
            content: '';
            position: absolute; left: 0; top: 0; bottom: 0;
            width: 3px; background: var(--accent);
            border-radius: 0 2px 2px 0;
        }
        .nav-item i { width: 18px; height: 18px; }

        /* ── TOPBAR ── */
        .topbar {
            position: fixed;
            top: 0; left: var(--sidebar-w); right: 0;
            height: var(--topbar-h);
            background: white;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            z-index: 99;
        }
        .page-title { display: flex; align-items: center; gap: 10px; font-size: 17px; font-weight: 600; color: #1e293b; }
        .page-title i { color: var(--accent); width: 20px; height: 20px; }
        .topbar-right { display: flex; align-items: center; gap: 16px; }
        .search-bar {
            display: flex; align-items: center; gap: 8px;
            background: var(--bg-main); border-radius: 8px;
            padding: 7px 14px; border: 1px solid #e2e8f0;
        }
        .search-bar input { border: none; background: none; outline: none; font-size: 13px; color: #64748b; width: 160px; }
        .search-bar i { width: 15px; color: #94a3b8; }
        .notif-btn {
            position: relative; width: 36px; height: 36px;
            background: var(--bg-main); border-radius: 50%; border: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: center; cursor: pointer;
        }
        .notif-btn i { width: 16px; color: #64748b; }
        .notif-dot {
            position: absolute; top: 7px; right: 7px;
            width: 8px; height: 8px; background: var(--danger);
            border-radius: 50%; border: 1.5px solid white;
        }
        .user-info { display: flex; align-items: center; gap: 10px; cursor: pointer; }
        .user-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: var(--accent); display: flex; align-items: center; justify-content: center;
            color: white; font-size: 13px; font-weight: 600;
        }
        .user-name { font-size: 13px; font-weight: 500; color: #334155; }
        .user-email { font-size: 11px; color: var(--text-muted); }

        /* ── CONTENT AREA ── */
        .content {
            margin-left: var(--sidebar-w);
            margin-top: var(--topbar-h);
            flex: 1; padding: 24px;
            min-height: calc(100vh - var(--topbar-h));
        }

        /* ── SUMMARY CARDS ── */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 14px; margin-bottom: 20px;
        }
        .sum-card {
            background: white; border-radius: 10px;
            padding: 14px 16px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
        }
        .sum-card-label { font-size: 11px; color: var(--text-muted); font-weight: 500; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
        .sum-card-label i { width: 14px; height: 14px; }
        .sum-value { font-size: 22px; font-weight: 700; color: #1e293b; }
        .sum-unit { font-size: 13px; color: #64748b; }
        .status-badges { display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 700; }
        .badge-n { color: var(--success); }
        .badge-w { color: var(--warning); }
        .badge-p { color: var(--danger); }

        /* ── MAIN GRID (floor plan + right panel) ── */
        .main-grid { display: grid; grid-template-columns: 1fr 280px; gap: 16px; }

        /* ── FLOOR PLAN ── */
        .floor-plan-card {
            background: white; border-radius: 10px;
            padding: 18px; box-shadow: 0 1px 4px rgba(0,0,0,0.07);
        }
        .section-title { font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 14px; }
        .svg-container { width: 100%; overflow: hidden; }
        svg.floorplan { width: 100%; height: auto; border-radius: 6px; }

        /* Room rects */
        .room-rect {
            fill: #f8fafc; stroke: #334155; stroke-width: 1.5;
            cursor: pointer; transition: fill .2s;
        }
        .room-rect:hover { fill: #eff6ff; }
        .room-rect.selected { fill: #fef9c3; stroke: #f59e0b; stroke-width: 2; }
        .room-rect.status-warning { stroke: #f59e0b; }
        .room-rect.status-poor { stroke: #ef4444; }

        .room-label { font-size: 8px; fill: #334155; pointer-events: none; font-family: 'Inter', sans-serif; font-weight: 500; }

        /* Legend */
        .legend { display: flex; gap: 16px; margin-top: 12px; font-size: 12px; color: #64748b; }
        .legend-item { display: flex; align-items: center; gap: 5px; }
        .legend-dot { width: 10px; height: 10px; border-radius: 50%; }
        .legend-dot.n { background: var(--success); }
        .legend-dot.w { background: var(--warning); }
        .legend-dot.p { background: var(--danger); }

        /* ── RIGHT PANEL ── */
        .right-panel { display: flex; flex-direction: column; gap: 16px; }
        .detail-card, .alerts-card {
            background: white; border-radius: 10px;
            padding: 18px; box-shadow: 0 1px 4px rgba(0,0,0,0.07);
        }

        .detail-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px; }
        .detail-room-name { font-size: 15px; font-weight: 700; color: #1e293b; }
        .detail-status { font-size: 11px; padding: 3px 8px; border-radius: 20px; font-weight: 600; }
        .detail-status.warning { background: #fef3c7; color: #b45309; }
        .detail-status.poor { background: #fee2e2; color: #b91c1c; }
        .detail-status.normal { background: #dcfce7; color: #15803d; }
        .detail-updated { font-size: 11px; color: var(--text-muted); display: flex; align-items: center; gap: 5px; margin-bottom: 14px; }
        .detail-updated i { width: 11px; color: var(--success); }

        .detail-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
        .detail-row:last-child { border-bottom: none; }
        .detail-row-label { font-size: 13px; color: #64748b; }
        .detail-row-value { font-size: 13px; font-weight: 600; color: #1e293b; }
        .detail-row-value.connected { color: var(--success); }
        .detail-row-value.disconnected { color: var(--danger); }

        .btn-analisa {
            display: block; width: 100%; padding: 10px;
            background: #1e293b; color: white; border: none;
            border-radius: 8px; font-size: 13px; font-weight: 600;
            cursor: pointer; text-align: center; margin-top: 14px;
            transition: background .2s;
        }
        .btn-analisa:hover { background: var(--accent); }

        /* Alerts */
        .alert-item { display: flex; align-items: center; gap: 10px; padding: 9px 0; border-bottom: 1px solid #f1f5f9; }
        .alert-item:last-child { border-bottom: none; }
        .alert-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .alert-icon i { width: 15px; height: 15px; }
        .alert-icon.sensor_offline { background: #f1f5f9; color: #64748b; }
        .alert-icon.high_temp { background: #fef3c7; color: #b45309; }
        .alert-icon.ac_off { background: #eff6ff; color: #3b82f6; }
        .alert-icon.high_power { background: #fef9c3; color: #ca8a04; }
        .alert-text { flex: 1; }
        .alert-msg { font-size: 13px; font-weight: 600; color: #1e293b; }
        .alert-room { font-size: 11px; color: var(--text-muted); }
        .alert-time { font-size: 11px; color: var(--text-muted); white-space: nowrap; }
        .see-all { display: flex; align-items: center; justify-content: flex-end; gap: 4px; font-size: 12px; color: var(--accent); margin-top: 10px; cursor: pointer; font-weight: 500; }
        .see-all i { width: 14px; }

        /* Loading skeleton for detail panel */
        .detail-placeholder { text-align: center; padding: 30px 0; color: var(--text-muted); font-size: 13px; }
        .detail-placeholder i { width: 32px; height: 32px; display: block; margin: 0 auto 8px; opacity: .4; }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="40" height="40" rx="8" fill="#4f7dfc"/>
            <text x="7" y="27" font-size="18" font-weight="900" fill="white" font-family="Inter">BE</text>
        </svg>
        <div>
            <div style="color:white;font-weight:700;font-size:13px;">BMS</div>
            <div class="logo-text">Beacon Engineering</div>
        </div>
    </div>
    <nav class="nav-section">
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i data-feather="grid"></i> Dashboard
        </a>
        <a href="#" class="nav-item">
            <i data-feather="bar-chart-2"></i> Analisa Data
        </a>
        <a href="#" class="nav-item">
            <i data-feather="zap"></i> Energi
        </a>
        <a href="#" class="nav-item">
            <i data-feather="bell"></i> Peringatan
        </a>
        <a href="#" class="nav-item">
            <i data-feather="settings"></i> Pengaturan
        </a>
    </nav>
</aside>

<!-- TOPBAR -->
<header class="topbar">
    <div class="page-title">
        <i data-feather="grid"></i>
        Dashboard
    </div>
    <div class="topbar-right">
        <div class="search-bar">
            <i data-feather="search"></i>
            <input type="text" placeholder="Cari ...">
        </div>
        <div class="notif-btn">
            <i data-feather="bell"></i>
            <span class="notif-dot"></span>
        </div>
        <div class="user-info">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-email">{{ auth()->user()->email }}</div>
            </div>
            <i data-feather="chevron-down" style="width:14px;color:#94a3b8;"></i>
        </div>
    </div>
</header>

<!-- CONTENT -->
<main class="content">
    @yield('content')
</main>

<script>
    feather.replace();
</script>
</body>
</html>
