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
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
    </style>
</head>
<body class="font-['Inter'] bg-slate-100 flex min-h-screen">

<!-- MOBILE BACKDROP -->
<div id="sidebar-backdrop" onclick="closeSidebar()"></div>

<!-- SIDEBAR -->
<aside id="sidebar" class="h-screen bg-[#FFFFFF] flex flex-col fixed top-0 left-0 z-[100] overflow-hidden">
    <!-- Logo -->
    <div class="px-4 py-[18px] border-b border-slate-100 flex items-center gap-2.5 shrink-0">
        <img src="{{ asset('images/beacon-logo.png') }}" alt="Beacon Logo" class="w-[34px] h-[34px] shrink-0 object-contain">
        <div class="sidebar-logo-text overflow-hidden">
            <div class="text-slate-800 font-bold text-[13px] whitespace-nowrap">BMS</div>
            <div class="text-[10px] font-semibold tracking-[1px] text-slate-400 uppercase whitespace-nowrap">Beacon Engineering</div>
        </div>
    </div>

    <nav class="py-4 flex-1 overflow-y-auto">
        <a href="{{ route('dashboard') }}" class="nav-item flex items-center gap-3 px-5 py-[11px] no-underline text-[13.5px] font-medium relative transition-all duration-200 hover:bg-slate-100 hover:text-slate-900 {{ request()->routeIs('dashboard') ? 'bg-red-700 text-white' : 'text-slate-500' }}">
            <img src="{{ asset('icons/dashboard.svg') }}" alt="Dashboard" class="w-[18px] h-[18px] shrink-0">
            <span class="sidebar-label">Dashboard</span>
        </a>
        <a href="{{ route('analisa-data.index') }}" class="nav-item flex items-center gap-3 px-5 py-[11px] no-underline text-[13.5px] font-medium relative transition-all duration-200 hover:bg-slate-100 hover:text-slate-900 {{ request()->routeIs('analisa-data.*') ? 'bg-red-700 text-white' : 'text-slate-500' }}">
            <img src="{{ asset('icons/analisa-data.svg') }}" alt="Analisa Data" class="w-[18px] h-[18px] shrink-0">
            <span class="sidebar-label">Analisa Data</span>
        </a>
        <a href="{{ route('energi.index') }}" class="nav-item flex items-center gap-3 px-5 py-[11px] no-underline text-[13.5px] font-medium relative transition-all duration-200 hover:bg-slate-100 hover:text-slate-900 {{ request()->routeIs('energi.*') ? 'bg-red-700 text-white' : 'text-slate-500' }}">
            <img src="{{ asset('icons/energi-bar.svg') }}" alt="Energi" class="w-[18px] h-[18px] shrink-0">
            <span class="sidebar-label">Energi</span>
        </a>
        <a href="{{ route('log-peringatan.index') }}" class="nav-item flex items-center gap-3 px-5 py-[11px] no-underline text-[13.5px] font-medium relative transition-all duration-200 hover:bg-slate-100 hover:text-slate-900 {{ request()->routeIs('log-peringatan.*') ? 'bg-red-700 text-white' : 'text-slate-500' }}">
            <img src="{{ asset('icons/log.svg') }}" alt="Log Peringatan" class="w-[18px] h-[18px] shrink-0">
            <span class="sidebar-label">Log Peringatan</span>
        </a>
        {{-- Pengaturan Accordion --}}
        <div id="nav-pengaturan">
            {{-- Trigger --}}
            <button onclick="togglePengaturan()"
                class="nav-item w-full flex items-center gap-3 px-5 py-[11px] text-slate-500 text-[13.5px] font-medium transition-all duration-200 hover:text-slate-900 hover:bg-slate-100 cursor-pointer bg-transparent border-0">
                {{-- pengaturan icon --}}
                <img src="{{ asset('icons/pengaturan.svg') }}" alt="Pengaturan" class="w-[18px] h-[18px] shrink-0">
                <span class="sidebar-label flex-1 text-left">Pengaturan</span>
                {{-- chevron --}}
                <svg id="pengaturan-chevron" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="sidebar-label shrink-0 transition-transform duration-200"><polyline points="6 9 12 15 18 9"/></svg>
            </button>

            {{-- Sub-menu --}}
            <div id="pengaturan-sub" class="overflow-hidden transition-all duration-200" style="max-height:0;">
                @php
                    $subMenus = [
                        ['label' => 'Umum',        'route' => 'pengaturan.umum',        'img' => 'umum.svg'],
                        ['label' => 'Konfigurasi', 'route' => 'pengaturan.konfigurasi', 'img' => 'konfig.svg'],
                        ['label' => 'Peringatan',  'route' => 'pengaturan.peringatan',   'img' => 'peringatan.svg'],
                        ['label' => 'Pengguna',    'route' => 'pengaturan.pengguna',    'img' => 'pengguna.svg'],
                    ];
                @endphp
                @foreach($subMenus as $sub)
                    @php
                        $isActive = $sub['route'] && request()->routeIs($sub['route']);
                        $href     = $sub['route'] ? route($sub['route']) : '#';
                    @endphp
                    <a href="{{ $href }}"
                        class="flex items-center gap-3 pl-12 pr-5 py-[9px] no-underline text-[13px] font-medium transition-all duration-150
                                {{ $isActive
                                    ? 'bg-red-700 text-white'
                                    : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
                        <img src="{{ asset('icons/' . $sub['img']) }}" alt="{{ $sub['label'] }}"
                             class="w-[15px] h-[15px] shrink-0 {{ $isActive ? 'opacity-90' : 'opacity-70' }}">
                        <span class="sidebar-label">{{ $sub['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        @can('kelola_denah')
        <div class="sidebar-section-label mx-5 mt-3 mb-1.5 text-[10px] font-semibold text-slate-400 tracking-[.8px] uppercase whitespace-nowrap overflow-hidden">Admin</div>
        <a href="{{ route('admin.buildings.index') }}" class="nav-item flex items-center gap-3 px-5 py-[11px] no-underline text-[13.5px] font-medium relative transition-all duration-200 hover:bg-slate-100 hover:text-slate-900 {{ request()->routeIs('admin.*') ? 'bg-red-700 text-white' : 'text-slate-500' }}">
            <img src="{{ asset('icons/pengaturan.svg') }}" alt="Manajemen Denah" class="w-[18px] h-[18px] shrink-0">
            <span class="sidebar-label">Manajemen Denah</span>
        </a>
        @endcan
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
        <div class="hidden sm:flex items-center gap-2 bg-slate-100 rounded-lg px-3.5 py-[7px] border border-slate-200">
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
                <div class="hidden sm:block text-left">
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
    const PENG_KEY = 'bms_pengaturan_open';

    function togglePengaturan() {
        const sub     = document.getElementById('pengaturan-sub');
        const chevron = document.getElementById('pengaturan-chevron');
        const isOpen  = sub.style.maxHeight !== '0px' && sub.style.maxHeight !== '';

        if (isOpen) {
            sub.style.maxHeight = '0';
            chevron.style.transform = 'rotate(0deg)';
            localStorage.setItem(PENG_KEY, '0');
        } else {
            sub.style.maxHeight = sub.scrollHeight + 'px';
            chevron.style.transform = 'rotate(180deg)';
            localStorage.setItem(PENG_KEY, '1');
        }
    }

    // Restore accordion state — dari localStorage ATAU jika sedang di halaman pengaturan
    const isPengaturanPage = window.location.pathname.startsWith('/pengaturan');
    if (localStorage.getItem(PENG_KEY) === '1' || isPengaturanPage) {
        const sub     = document.getElementById('pengaturan-sub');
        const chevron = document.getElementById('pengaturan-chevron');
        if (sub) {
            sub.style.maxHeight = sub.scrollHeight + 'px';
            chevron.style.transform = 'rotate(180deg)';
            if (isPengaturanPage) localStorage.setItem(PENG_KEY, '1');
        }
    }
</script>
@stack('modals')
@stack('scripts')
</body>
</html>
