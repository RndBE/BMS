<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    {{-- Anti-flash: deteksi tema sebelum content dirender --}}
    <script>
        (function() {
            var saved = localStorage.getItem('bms_theme');
            if (saved === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BMS – {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>

    <style>
        /* ── Base sidebar & layout transition ── */
        #sidebar       { transition: width .25s ease, transform .25s ease; width: 220px; }
        #topbar        { transition: left .25s ease; left: 220px; }
        #main-content  { transition: margin-left .25s ease; margin-left: 220px; }
        #sidebar-labels { transition: opacity .15s ease, width .25s ease; }
        #sidebar-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.35); z-index: 99; }

        /* Desktop collapse */
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

        /* ── Tablet (768–1023px): auto-collapse to icons ── */
        @media (min-width: 768px) and (max-width: 1023px) {
            #sidebar      { width: 64px; }
            #topbar       { left: 64px !important; }
            #main-content { margin-left: 64px !important; }
            .sidebar-label, .sidebar-logo-text, .sidebar-section-label { display: none !important; }
            .nav-item { justify-content: center !important; padding-left: 0 !important; padding-right: 0 !important; }
        }

        /* ── Mobile (< 768px): sidebar overlay ── */
        @media (max-width: 767px) {
            #sidebar      { width: 220px; transform: translateX(-100%); }
            #topbar       { left: 0 !important; right: 0; }
            #main-content { margin-left: 0 !important; padding: 1rem; }
            body.sidebar-open #sidebar { transform: translateX(0); }
            body.sidebar-open #sidebar-backdrop { display: block; }
            body.sidebar-collapsed #sidebar { width: 220px; transform: translateX(-100%); }
        }

        /* Icon putih saat nav-item aktif */
        .nav-item.bg-red-700 img,
        #pengaturan-sub a.bg-red-700 img,
        #sidebar a.bg-red-700 img { filter: brightness(0) invert(1); }

        /* Dark mode icon inversion for sidebar icons */
        .dark #sidebar .nav-item:not(.bg-red-700) img,
        .dark #sidebar button:not(.bg-red-700) img,
        .dark #pengaturan-sub a:not(.bg-red-700) img { filter: brightness(0) invert(0.65); }
        .dark #sidebar .nav-item.bg-red-700 img,
        .dark #sidebar a.bg-red-700 img,
        .dark #pengaturan-sub a.bg-red-700 img { filter: brightness(0) invert(1); }

        /* Dark scrollbar */
        .dark ::-webkit-scrollbar { width: 6px; height: 6px; }
        .dark ::-webkit-scrollbar-track { background: #1a1a1a; }
        .dark ::-webkit-scrollbar-thumb { background: #3d3d3d; border-radius: 3px; }
        .dark ::-webkit-scrollbar-thumb:hover { background: #555; }
    </style>
</head>
<body class="font-['Inter'] bg-slate-100 dark:bg-[#3C3D3F] flex min-h-screen transition-colors duration-300">

<!-- MOBILE BACKDROP -->
<div id="sidebar-backdrop" onclick="closeSidebar()"></div>

<!-- SIDEBAR -->
<aside id="sidebar" class="h-screen bg-white dark:bg-[#1D1D1D] flex flex-col fixed top-0 left-0 z-[100] overflow-hidden border-r border-slate-100 dark:border-[#222]">
    <!-- Logo -->
    <div class="px-4 py-[18px] border-b border-slate-100 dark:border-[#222] flex items-center gap-2.5 shrink-0">
        <img src="{{ asset('images/beacon-logo.png') }}" alt="Beacon Logo" class="w-[86px] h-[34px] shrink-0 object-contain">
    </div>

    <nav class="py-4 flex-1 overflow-y-auto">
        {{-- Dashboard: semua user login --}}
        <a href="{{ route('dashboard') }}" class="nav-item flex items-center gap-3 px-5 py-[11px] no-underline text-[13.5px] font-medium relative transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-red-700 text-white' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1e1e1e] hover:text-slate-900 dark:hover:text-white' }}">
            <img src="{{ asset('icons/dashboard.svg') }}" alt="Dashboard" class="w-[18px] h-[18px] shrink-0">
            <span class="sidebar-label">Dashboard</span>
        </a>

        @can('lihat_analisa')
        <a href="{{ route('analisa-data.index') }}" class="nav-item flex items-center gap-3 px-5 py-[11px] no-underline text-[13.5px] font-medium relative transition-all duration-200 {{ request()->routeIs('analisa-data.*') ? 'bg-red-700 text-white' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1e1e1e] hover:text-slate-900 dark:hover:text-white' }}">
            <img src="{{ asset('icons/analisa-data.svg') }}" alt="Analisa Data" class="w-[18px] h-[18px] shrink-0">
            <span class="sidebar-label">Analisa Data</span>
        </a>
        @endcan

        @can('lihat_energi')
        <a href="{{ route('energi.index') }}" class="nav-item flex items-center gap-3 px-5 py-[11px] no-underline text-[13.5px] font-medium relative transition-all duration-200 {{ request()->routeIs('energi.*') ? 'bg-red-700 text-white' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1e1e1e] hover:text-slate-900 dark:hover:text-white' }}">
            <img src="{{ asset('icons/energi-bar.svg') }}" alt="Energi" class="w-[18px] h-[18px] shrink-0">
            <span class="sidebar-label">Energi</span>
        </a>
        @endcan

        @can('lihat_log')
        <a href="{{ route('log-peringatan.index') }}" class="nav-item flex items-center gap-3 px-5 py-[11px] no-underline text-[13.5px] font-medium relative transition-all duration-200 {{ request()->routeIs('log-peringatan.*') ? 'bg-red-700 text-white' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1e1e1e] hover:text-slate-900 dark:hover:text-white' }}">
            <img src="{{ asset('icons/log.svg') }}" alt="Log Peringatan" class="w-[18px] h-[18px] shrink-0">
            <span class="sidebar-label">Log Peringatan</span>
        </a>
        @endcan

        {{-- Accordion Pengaturan: tampil hanya jika punya >= 1 sub-permission --}}
        @if(auth()->user()->canAny(['kelola_pengaturan','kelola_konfigurasi','kelola_peringatan','kelola_pengguna']))
        <div id="nav-pengaturan">
            <button onclick="togglePengaturan()"
                class="nav-item w-full flex items-center gap-3 px-5 py-[11px] text-slate-500 dark:text-slate-400 text-[13.5px] font-medium transition-all duration-200 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-[#1e1e1e] cursor-pointer bg-transparent border-0">
                <img src="{{ asset('icons/pengaturan.svg') }}" alt="Pengaturan" class="w-[18px] h-[18px] shrink-0">
                <span class="sidebar-label flex-1 text-left">Pengaturan</span>
                <svg id="pengaturan-chevron" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="sidebar-label shrink-0 transition-transform duration-200"><polyline points="6 9 12 15 18 9"/></svg>
            </button>

            <div id="pengaturan-sub" class="overflow-hidden transition-all duration-200" style="max-height:0;">
                @can('kelola_pengaturan')
                @php $isActive = request()->routeIs('pengaturan.umum'); @endphp
                <a href="{{ route('pengaturan.umum') }}"
                    class="flex items-center gap-3 pl-12 pr-5 py-[9px] no-underline text-[13px] font-medium transition-all duration-150
                            {{ $isActive ? 'bg-red-700 text-white' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-[#1e1e1e]' }}">
                    <img src="{{ asset('icons/umum.svg') }}" alt="Umum" class="w-[15px] h-[15px] shrink-0 {{ $isActive ? 'opacity-90' : 'opacity-70' }}">
                    <span class="sidebar-label">Umum</span>
                </a>
                @endcan

                @can('kelola_konfigurasi')
                @php $isActive = request()->routeIs('pengaturan.konfigurasi'); @endphp
                <a href="{{ route('pengaturan.konfigurasi') }}"
                    class="flex items-center gap-3 pl-12 pr-5 py-[9px] no-underline text-[13px] font-medium transition-all duration-150
                            {{ $isActive ? 'bg-red-700 text-white' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-[#1e1e1e]' }}">
                    <img src="{{ asset('icons/konfig.svg') }}" alt="Konfigurasi" class="w-[15px] h-[15px] shrink-0 {{ $isActive ? 'opacity-90' : 'opacity-70' }}">
                    <span class="sidebar-label">Konfigurasi</span>
                </a>
                @endcan

                @can('kelola_peringatan')
                @php $isActive = request()->routeIs('pengaturan.peringatan'); @endphp
                <a href="{{ route('pengaturan.peringatan') }}"
                    class="flex items-center gap-3 pl-12 pr-5 py-[9px] no-underline text-[13px] font-medium transition-all duration-150
                            {{ $isActive ? 'bg-red-700 text-white' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-[#1e1e1e]' }}">
                    <img src="{{ asset('icons/peringatan.svg') }}" alt="Peringatan" class="w-[15px] h-[15px] shrink-0 {{ $isActive ? 'opacity-90' : 'opacity-70' }}">
                    <span class="sidebar-label">Peringatan</span>
                </a>
                @endcan

                @can('kelola_pengguna')
                @php $isActive = request()->routeIs('pengaturan.pengguna'); @endphp
                <a href="{{ route('pengaturan.pengguna') }}"
                    class="flex items-center gap-3 pl-12 pr-5 py-[9px] no-underline text-[13px] font-medium transition-all duration-150
                            {{ $isActive ? 'bg-red-700 text-white' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-[#1e1e1e]' }}">
                    <img src="{{ asset('icons/pengguna.svg') }}" alt="Pengguna" class="w-[15px] h-[15px] shrink-0 {{ $isActive ? 'opacity-90' : 'opacity-70' }}">
                    <span class="sidebar-label">Pengguna</span>
                </a>
                @endcan
            </div>
        </div>
        @endif

        @can('kelola_denah')
        <div class="sidebar-section-label mx-5 mt-3 mb-1.5 text-[10px] font-semibold text-slate-400 tracking-[.8px] uppercase whitespace-nowrap overflow-hidden">Admin</div>
        <a href="{{ route('admin.buildings.index') }}" class="nav-item flex items-center gap-3 px-5 py-[11px] no-underline text-[13.5px] font-medium relative transition-all duration-200 {{ request()->routeIs('admin.*') ? 'bg-red-700 text-white' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1e1e1e] hover:text-slate-900 dark:hover:text-white' }}">
            <img src="{{ asset('icons/pengaturan.svg') }}" alt="Manajemen Denah" class="w-[18px] h-[18px] shrink-0">
            <span class="sidebar-label">Manajemen Denah</span>
        </a>
        @endcan
    </nav>
</aside>

<!-- TOPBAR -->
<header id="topbar" class="fixed top-0 right-0 h-[60px] bg-white dark:bg-[#1D1D1D] dark:border-b dark:border-[#2a2a2a] flex items-center justify-between px-6 shadow-[0_1px_3px_rgba(0,0,0,.08)] dark:shadow-[0_1px_3px_rgba(0,0,0,.3)] z-[99]">
    <div class="flex items-center gap-3">
        <!-- Sidebar Toggle Button -->
        <button id="sidebarToggle" onclick="toggleSidebar()"
            class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-[#2a2a2a] border border-slate-200 dark:border-[#333] flex items-center justify-center hover:bg-slate-200 dark:hover:bg-[#333] transition-colors cursor-pointer"
            title="Toggle Sidebar">
            <!-- Hamburger / arrow icon -->
            <svg id="toggleIconOpen" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-600 dark:text-slate-300"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            <svg id="toggleIconClose" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hidden text-slate-600 dark:text-slate-300"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="15" y2="6"/><line x1="3" y1="18" x2="15" y2="18"/></svg>
        </button>

        <div class="flex items-center gap-2.5 text-[17px] font-semibold text-slate-800 dark:text-white">
            @yield('page-title', 'Dashboard')
        </div>
    </div>

    <div class="flex items-center gap-4">
        <div class="hidden sm:flex items-center gap-2 bg-slate-100 dark:bg-[#2a2a2a] rounded-lg px-4 py-[1px] border border-slate-200 dark:border-[#333]">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-400"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="Cari ..." class="border-none bg-transparent outline-none text-[13px] text-slate-500 dark:text-slate-300 placeholder-slate-400 w-40">
        </div>

        <!-- Dark Mode Toggle -->
        <button id="themeToggle" onclick="toggleTheme()"
            class="w-9 h-9 rounded-full bg-slate-100 dark:bg-[#2a2a2a] border border-slate-200 dark:border-[#333] flex items-center justify-center hover:bg-slate-200 dark:hover:bg-[#333] transition-colors cursor-pointer"
            title="Toggle Tema">
            <!-- Sun icon (tampil di dark mode) -->
            <svg id="themeIconSun" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hidden text-yellow-400"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
            <!-- Moon icon (tampil di light mode) -->
            <svg id="themeIconMoon" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-500"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
        </button>

        @can('lihat_log')
        <a href="{{ route('log-peringatan.index') }}"
            title="Log Peringatan"
            class="relative w-9 h-9 bg-slate-100 dark:bg-[#2a2a2a] rounded-full border border-slate-200 dark:border-[#333] flex items-center justify-center hover:bg-slate-200 dark:hover:bg-[#333] transition-colors no-underline">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-500 dark:text-slate-400"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            {{-- Badge selalu ada di DOM supaya JS bisa update tanpa refresh --}}
            <span id="alert-badge"
                class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-[3px] bg-red-500 rounded-full border-2 border-white dark:border-[#1D1D1D] flex items-center justify-center text-white text-[9px] font-bold leading-none {{ $unreadAlertCount > 0 ? '' : 'hidden' }}">
                {{ $unreadAlertCount > 99 ? '99+' : $unreadAlertCount }}
            </span>
        </a>
        @else
        <div class="relative w-9 h-9 bg-slate-100 dark:bg-[#2a2a2a] rounded-full border border-slate-200 dark:border-[#333] flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-500 dark:text-slate-400"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        </div>
        @endcan
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-2.5 cursor-pointer bg-transparent border-none p-0">
                <div class="w-[34px] h-[34px] rounded-full bg-[#4f7dfc] flex items-center justify-center text-white text-[13px] font-semibold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="hidden sm:block text-left">
                    <div class="text-[13px] font-medium text-slate-700 dark:text-slate-200">{{ auth()->user()->name }}</div>
                    <div class="text-[11px] text-slate-400">{{ auth()->user()->email }}</div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <!-- Dropdown Menu -->
            <div x-show="open" @click.outside="open = false" x-cloak
                class="absolute right-0 top-[calc(100%+8px)] w-48 bg-white dark:bg-[#232323] rounded-xl shadow-[0_8px_24px_rgba(0,0,0,.12)] dark:shadow-[0_8px_24px_rgba(0,0,0,.4)] border border-slate-100 dark:border-[#333] z-[200] overflow-hidden">
                <a href="{{ route('profile.edit') }}"
                class="flex items-center gap-2.5 px-4 py-2.5 text-[13px] text-slate-700 dark:text-slate-300 no-underline hover:bg-slate-50 dark:hover:bg-[#2a2a2a] transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-400"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Profil Saya
                </a>
                <div class="border-t border-slate-100 dark:border-[#333]"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-2.5 px-4 py-2.5 text-[13px] text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors bg-transparent border-none cursor-pointer">
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

    // ── Theme Toggle ──
    const THEME_KEY = 'bms_theme';

    function applyTheme(dark) {
        document.documentElement.classList.toggle('dark', dark);
        document.getElementById('themeIconSun').classList.toggle('hidden', !dark);
        document.getElementById('themeIconMoon').classList.toggle('hidden', dark);
    }

    function toggleTheme() {
        const isDark = document.documentElement.classList.contains('dark');
        localStorage.setItem(THEME_KEY, isDark ? 'light' : 'dark');
        applyTheme(!isDark);
    }

    // Apply saved theme on load
    (function () {
        const saved = localStorage.getItem(THEME_KEY);
        applyTheme(saved === 'dark');
    })();

    // ── Sidebar Toggle ──
    const SIDEBAR_KEY = 'bms_sidebar_collapsed';

    function isMobile() { return window.innerWidth < 768; }
    function isTablet() { return window.innerWidth >= 768 && window.innerWidth < 1024; }

    function toggleSidebar() {
        if (isMobile()) {
            // Mobile: overlay toggle (no persist, no icon change)
            document.body.classList.toggle('sidebar-open');
        } else if (isTablet()) {
            // Tablet: we don't persist, just do nothing special
            // Tablet sidebar is always icon-only via CSS
        } else {
            // Desktop: collapse/expand with persist
            const collapsed = document.body.classList.toggle('sidebar-collapsed');
            localStorage.setItem(SIDEBAR_KEY, collapsed ? '1' : '0');
            updateToggleIcon(collapsed);
        }
    }

    function closeSidebar() {
        document.body.classList.remove('sidebar-open');
    }

    function updateToggleIcon(collapsed) {
        document.getElementById('toggleIconOpen').classList.toggle('hidden', collapsed);
        document.getElementById('toggleIconClose').classList.toggle('hidden', !collapsed);
    }

    // Close sidebar on resize to desktop
    window.addEventListener('resize', () => {
        if (!isMobile()) document.body.classList.remove('sidebar-open');
    });

    // Restore desktop collapse state on page load
    if (!isMobile() && !isTablet() && localStorage.getItem(SIDEBAR_KEY) === '1') {
        document.body.classList.add('sidebar-collapsed');
        updateToggleIcon(true);
    }

    // ── Pengaturan Accordion ──
    function togglePengaturan() {
        const sub     = document.getElementById('pengaturan-sub');
        const chevron = document.getElementById('pengaturan-chevron');
        const isOpen  = sub.style.maxHeight !== '0px' && sub.style.maxHeight !== '';

        if (isOpen) {
            sub.style.maxHeight = '0';
            chevron.style.transform = 'rotate(0deg)';
        } else {
            sub.style.maxHeight = sub.scrollHeight + 'px';
            chevron.style.transform = 'rotate(180deg)';
        }
    }

    // Buka accordion otomatis hanya jika sedang di halaman pengaturan
    const isPengaturanPage = window.location.pathname.startsWith('/pengaturan');
    if (isPengaturanPage) {
        const sub     = document.getElementById('pengaturan-sub');
        const chevron = document.getElementById('pengaturan-chevron');
        if (sub) {
            sub.style.maxHeight = sub.scrollHeight + 'px';
            chevron.style.transform = 'rotate(180deg)';
        }
    }
</script>
<script>
    // Global: dipanggil dari halaman log-peringatan saat alert ditandai dibaca
    window.decrementAlertBadge = function() {
        const badge = document.getElementById('alert-badge');
        if (!badge) return;
        let count = parseInt(badge.textContent.trim()) || 0;
        count = Math.max(0, count - 1);
        if (count === 0) {
            badge.classList.add('hidden');
        } else {
            badge.textContent = count > 99 ? '99+' : count;
        }
    };
</script>
@stack('modals')
@stack('scripts')
</body>
</html>
