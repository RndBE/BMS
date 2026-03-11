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
        /* Sidebar & layout transition */
        #sidebar       { transition: width .25s ease, transform .25s ease; width: 220px; }
        #topbar        { transition: left .25s ease; left: 220px; }
        #main-content  { transition: margin-left .25s ease; margin-left: 220px; }
        #sidebar-labels { transition: opacity .15s ease, width .25s ease; }

        body.sidebar-collapsed #sidebar        { width: 64px; }
        body.sidebar-collapsed #topbar         { left: 64px; }
        body.sidebar-collapsed #main-content   { margin-left: 64px; }
        body.sidebar-collapsed #sidebar-labels { opacity: 0; width: 0; overflow: hidden; white-space: nowrap; }
        body.sidebar-collapsed .sidebar-label  { display: none; }
        body.sidebar-collapsed .sidebar-section-label { display: none; }
        body.sidebar-collapsed .sidebar-logo-text { display: none; }
        body.sidebar-collapsed .nav-item       { justify-content: center; padding-left: 0; padding-right: 0; }
        body.sidebar-collapsed .nav-item svg,
        body.sidebar-collapsed .nav-item i     { margin: 0 auto; }

        /* Active nav indicator */
        .nav-item.active::before {
            content: ''; position: absolute; left: 0; top: 20%; height: 60%;
            width: 3px; background: #4f7dfc; border-radius: 0 3px 3px 0;
        }
    </style>
</head>
<body class="font-['Inter'] bg-slate-100 flex min-h-screen">

<!-- SIDEBAR -->
<aside id="sidebar" class="min-h-screen bg-[#1e2a45] flex flex-col fixed top-0 left-0 z-[100] overflow-hidden">
    <!-- Logo -->
    <div class="px-4 py-[18px] border-b border-white/[.08] flex items-center gap-2.5 text-white shrink-0">
        <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-[34px] h-[34px] shrink-0">
            <rect width="40" height="40" rx="8" fill="#4f7dfc"/>
            <text x="7" y="27" font-size="18" font-weight="900" fill="white" font-family="Inter">BE</text>
        </svg>
        <div class="sidebar-logo-text overflow-hidden">
            <div class="text-white font-bold text-[13px] whitespace-nowrap">BMS</div>
            <div class="text-[10px] font-semibold tracking-[1px] text-[#93a5c4] uppercase whitespace-nowrap">Beacon Engineering</div>
        </div>
    </div>

    <nav class="py-4 flex-1">
        <a href="{{ route('dashboard') }}" class="nav-item flex items-center gap-3 px-5 py-[11px] text-[#93a5c4] no-underline text-[13.5px] font-medium relative transition-all duration-200 hover:text-white hover:bg-[#273554] {{ request()->routeIs('dashboard') ? 'active text-white bg-[#273554]' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            <span class="sidebar-label">Dashboard</span>
        </a>
        <a href="#" class="nav-item flex items-center gap-3 px-5 py-[11px] text-[#93a5c4] no-underline text-[13.5px] font-medium relative transition-all duration-200 hover:text-white hover:bg-[#273554]">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            <span class="sidebar-label">Analisa Data</span>
        </a>
        <a href="#" class="nav-item flex items-center gap-3 px-5 py-[11px] text-[#93a5c4] no-underline text-[13.5px] font-medium relative transition-all duration-200 hover:text-white hover:bg-[#273554]">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            <span class="sidebar-label">Energi</span>
        </a>
        <a href="#" class="nav-item flex items-center gap-3 px-5 py-[11px] text-[#93a5c4] no-underline text-[13.5px] font-medium relative transition-all duration-200 hover:text-white hover:bg-[#273554]">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <span class="sidebar-label">Peringatan</span>
        </a>
        <a href="#" class="nav-item flex items-center gap-3 px-5 py-[11px] text-[#93a5c4] no-underline text-[13.5px] font-medium relative transition-all duration-200 hover:text-white hover:bg-[#273554]">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M4.93 4.93l1.41 1.41M19.07 19.07l-1.41-1.41M4.93 19.07l1.41-1.41M12 2v2M12 20v2M2 12h2M20 12h2"/></svg>
            <span class="sidebar-label">Pengaturan</span>
        </a>

        @if(auth()->user()->hasRole('superadmin'))
        <div class="sidebar-section-label mx-5 mt-3 mb-1.5 text-[10px] font-semibold text-[#4a5568] tracking-[.8px] uppercase whitespace-nowrap overflow-hidden">Admin</div>
        <a href="{{ route('admin.buildings.index') }}" class="nav-item flex items-center gap-3 px-5 py-[11px] text-[#93a5c4] no-underline text-[13.5px] font-medium relative transition-all duration-200 hover:text-white hover:bg-[#273554] {{ request()->routeIs('admin.*') ? 'active text-white bg-[#273554]' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
            <span class="sidebar-label">Manajemen Denah</span>
        </a>
        @endif
    </nav>
</aside>

<!-- TOPBAR -->
<header id="topbar" class="fixed top-0 right-0 h-[60px] bg-white flex items-center justify-between px-6 shadow-[0_1px_3px_rgba(0,0,0,.08)] z-[99]">
    <div class="flex items-center gap-3">
        <!-- Sidebar Toggle Button -->
        <button id="sidebarToggle" onclick="toggleSidebar()"
            class="w-8 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center hover:bg-slate-200 transition-colors cursor-pointer"
            title="Toggle Sidebar">
            <!-- Hamburger / arrow icon -->
            <svg id="toggleIconOpen" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            <svg id="toggleIconClose" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hidden"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="15" y2="6"/><line x1="3" y1="18" x2="15" y2="18"/></svg>
        </button>

        <div class="flex items-center gap-2.5 text-[17px] font-semibold text-slate-800">
            @yield('page-title', 'Dashboard')
        </div>
    </div>

    <div class="flex items-center gap-4">
        <div class="flex items-center gap-2 bg-slate-100 rounded-lg px-3.5 py-[7px] border border-slate-200">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-400"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="Cari ..." class="border-none bg-transparent outline-none text-[13px] text-slate-500 w-40">
        </div>
        <div class="relative w-9 h-9 bg-slate-100 rounded-full border border-slate-200 flex items-center justify-center cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-500"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <span class="absolute top-[7px] right-[7px] w-2 h-2 bg-red-500 rounded-full border-[1.5px] border-white"></span>
        </div>
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-2.5 cursor-pointer bg-transparent border-none p-0">
                <div class="w-[34px] h-[34px] rounded-full bg-[#4f7dfc] flex items-center justify-center text-white text-[13px] font-semibold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="text-left">
                    <div class="text-[13px] font-medium text-slate-700">{{ auth()->user()->name }}</div>
                    <div class="text-[11px] text-slate-400">{{ auth()->user()->email }}</div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <!-- Dropdown Menu -->
            <div x-show="open" @click.outside="open = false" x-cloak
                class="absolute right-0 top-[calc(100%+8px)] w-48 bg-white rounded-xl shadow-[0_8px_24px_rgba(0,0,0,.12)] border border-slate-100 z-[200] overflow-hidden">
                <a href="{{ route('profile.edit') }}"
                class="flex items-center gap-2.5 px-4 py-2.5 text-[13px] text-slate-700 no-underline hover:bg-slate-50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-400"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Profil Saya
                </a>
                <div class="border-t border-slate-100"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-2.5 px-4 py-2.5 text-[13px] text-red-500 hover:bg-red-50 transition-colors bg-transparent border-none cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg> Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

<!-- CONTENT -->
<main id="main-content" class="mt-[60px] flex-1 p-6 min-h-[calc(100vh-60px)]">
    @yield('content')
</main>

<script>
    feather.replace();

    // ── Sidebar Toggle ──
    const SIDEBAR_KEY = 'bms_sidebar_collapsed';

    function toggleSidebar() {
        const collapsed = document.body.classList.toggle('sidebar-collapsed');
        localStorage.setItem(SIDEBAR_KEY, collapsed ? '1' : '0');
        updateToggleIcon(collapsed);
    }

    function updateToggleIcon(collapsed) {
        document.getElementById('toggleIconOpen').classList.toggle('hidden', collapsed);
        document.getElementById('toggleIconClose').classList.toggle('hidden', !collapsed);
    }

    // Restore state on page load
    if (localStorage.getItem(SIDEBAR_KEY) === '1') {
        document.body.classList.add('sidebar-collapsed');
        updateToggleIcon(true);
    }
</script>
</body>
</html>
