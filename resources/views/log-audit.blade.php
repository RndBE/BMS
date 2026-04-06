@extends('layouts.app')

@section('page-title', 'Log Audit')

@section('content')

@php
function auditBadge(string $action): array {
    return match($action) {
        'create'  => ['label' => 'Tambah',  'class' => 'text-green-700 bg-green-50'],
        'update'  => ['label' => 'Ubah',    'class' => 'text-blue-700 bg-blue-50'],
        'delete'  => ['label' => 'Hapus',   'class' => 'text-red-600 bg-red-50'],
        'login'   => ['label' => 'Login',   'class' => 'text-indigo-700 bg-indigo-50'],
        'logout'  => ['label' => 'Logout',  'class' => 'text-slate-600 bg-slate-100'],
        'export'  => ['label' => 'Ekspor',  'class' => 'text-orange-600 bg-orange-50'],
        default   => ['label' => ucfirst($action), 'class' => 'text-slate-600 bg-slate-100'],
    };
}
@endphp

<div class="bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-2xl shadow-sm border border-slate-200 overflow-visible">

    {{-- ── Filter Bar ── --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-[#2d2d2d] gap-3 flex-wrap">
        <form method="GET" action="{{ route('log-audit.index') }}" id="auditForm" class="flex items-center gap-2 flex-wrap">

            {{-- Action --}}
            <div class="relative custom-select-wrapper min-w-[140px]">
                <select name="action" onchange="document.getElementById('auditForm').submit()" class="hidden real-select">
                    <option value="">Aksi: Semua</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                            Aksi: {{ ucfirst($act) }}
                        </option>
                    @endforeach
                </select>
                <button type="button" class="select-btn flex items-center justify-between w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg pl-3 pr-2 py-[7px] text-[12.5px] text-slate-700 bg-white focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400 cursor-pointer">
                    <span class="select-text truncate text-left">Memuat...</span>
                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 pointer-events-none ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <ul class="select-dropdown absolute top-[100%] left-0 w-full mt-1 bg-white dark:bg-[#2a2a2a] border border-slate-200 dark:border-[#3d3d3d] rounded-lg shadow-lg hidden max-h-60 overflow-y-auto py-1 z-50 text-[12.5px] text-slate-700 dark:text-slate-200"></ul>
            </div>

            {{-- Pengguna --}}
            <div class="relative custom-select-wrapper min-w-[160px]">
                <select name="user_id" onchange="document.getElementById('auditForm').submit()" class="hidden real-select">
                    <option value="">Pengguna: Semua</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
                <button type="button" class="select-btn flex items-center justify-between w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg pl-3 pr-2 py-[7px] text-[12.5px] text-slate-700 bg-white focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400 cursor-pointer">
                    <span class="select-text truncate text-left">Memuat...</span>
                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 pointer-events-none ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <ul class="select-dropdown absolute top-[100%] left-0 w-full mt-1 bg-white dark:bg-[#2a2a2a] border border-slate-200 dark:border-[#3d3d3d] rounded-lg shadow-lg hidden max-h-60 overflow-y-auto py-1 z-50 text-[12.5px] text-slate-700 dark:text-slate-200"></ul>
            </div>

            {{-- Model --}}
            <div class="relative custom-select-wrapper min-w-[150px]">
                <select name="model_type" onchange="document.getElementById('auditForm').submit()" class="hidden real-select">
                    <option value="">Modul: Semua</option>
                    @foreach($models as $m)
                        <option value="{{ $m }}" {{ request('model_type') === $m ? 'selected' : '' }}>
                            {{ $m }}
                        </option>
                    @endforeach
                </select>
                <button type="button" class="select-btn flex items-center justify-between w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg pl-3 pr-2 py-[7px] text-[12.5px] text-slate-700 bg-white focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400 cursor-pointer">
                    <span class="select-text truncate text-left">Memuat...</span>
                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 pointer-events-none ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <ul class="select-dropdown absolute top-[100%] left-0 w-full mt-1 bg-white dark:bg-[#2a2a2a] border border-slate-200 dark:border-[#3d3d3d] rounded-lg shadow-lg hidden max-h-60 overflow-y-auto py-1 z-50 text-[12.5px] text-slate-700 dark:text-slate-200"></ul>
            </div>

        </form>

        {{-- Search --}}
        <form method="GET" action="{{ route('log-audit.index') }}" class="flex items-center gap-2">
            <input type="hidden" name="action"     value="{{ request('action') }}">
            <input type="hidden" name="user_id"    value="{{ request('user_id') }}">
            <input type="hidden" name="model_type" value="{{ request('model_type') }}">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari deskripsi ..."
                    class="pl-8 pr-3 py-[7px] border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg text-[12.5px] text-slate-700 focus:outline-none focus:border-red-400 bg-white w-48">
            </div>
        </form>
    </div>

    {{-- ── Table ── --}}
    <div class="overflow-x-auto">
        <table class="w-full text-[13px]">
            <thead>
                <tr class="text-left bg-red-100 text-slate-600 text-[12px] border-b border-slate-100 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200">
                    <th class="px-5 py-3 font-semibold">Waktu</th>
                    <th class="px-5 py-3 font-semibold">Pengguna</th>
                    <th class="px-5 py-3 font-semibold">Aksi</th>
                    <th class="px-5 py-3 font-semibold">Modul</th>
                    <th class="px-5 py-3 font-semibold">Deskripsi</th>
                    <th class="px-5 py-3 font-semibold">IP Address</th>
                    <th class="px-5 py-3 font-semibold text-center">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-[#2d2d2d]">
                @forelse($logs as $log)
                    @php $badge = auditBadge($log->action); @endphp
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-[#2a2a2a] transition-colors">

                        <td class="px-5 py-3 text-slate-500 dark:text-slate-400 whitespace-nowrap text-[12.5px]">
                            <div>{{ $log->created_at->format('H:i') }}</div>
                            <div class="text-[11px] text-slate-400">{{ $log->created_at->format('d/m/Y') }}</div>
                        </td>

                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-[#4f7dfc] flex items-center justify-center text-white text-[11px] font-semibold shrink-0">
                                    {{ strtoupper(substr($log->user?->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-[12.5px] font-medium text-slate-800 dark:text-slate-200">{{ $log->user?->name ?? '—' }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $log->user?->email }}</div>
                                </div>
                            </div>
                        </td>

                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $badge['class'] }}">
                                {{ $badge['label'] }}
                            </span>
                        </td>

                        <td class="px-5 py-3">
                            @if($log->model_type)
                                <span class="text-[12px] text-slate-700 dark:text-slate-300 font-medium">{{ $log->model_type }}</span>
                                @if($log->model_id)
                                    <span class="text-[11px] text-slate-400"> #{{ $log->model_id }}</span>
                                @endif
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>

                        <td class="px-5 py-3 text-slate-700 dark:text-slate-300 text-[12.5px] max-w-xs">
                            <span class="line-clamp-2">{{ $log->description }}</span>
                        </td>

                        <td class="px-5 py-3 text-slate-500 dark:text-slate-400 text-[12px] whitespace-nowrap font-mono">
                            {{ $log->ip_address ?? '—' }}
                        </td>

                        <td class="px-5 py-3 text-center">
                            @if($log->old_values || $log->new_values)
                                <button title="Lihat perubahan"
                                    onclick="openAuditDetail({{ $log->id }})"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-slate-100 dark:hover:bg-[#2a2a2a] text-slate-400 hover:text-slate-700 transition-colors cursor-pointer bg-white dark:bg-transparent">
                                    <img src="{{ asset('icons/detail.svg') }}" alt="Detail" class="w-5 h-5 shrink-0">
                                </button>
                            @else
                                <span class="text-slate-300 dark:text-slate-600 text-[12px]">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-14 text-center">
                            <div class="flex flex-col items-center text-slate-400">
                                <img src="{{ asset('icons/log.svg') }}" alt="Log" class="w-10 h-10 shrink-0 opacity-30 mb-2">
                                <p class="text-[13px] mt-1">Belum ada log audit</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Footer / Pagination ── --}}
    <div class="flex items-center justify-between px-5 py-4 border-t border-slate-50 dark:border-[#2d2d2d]">
        <span class="text-[12px] text-slate-400 dark:text-slate-500">
            @if($logs->total() > 0)
                Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ $logs->total() }} data
            @else
                Tidak ada data
            @endif
        </span>
        @if($logs->hasPages())
        <div class="flex items-center gap-1">
            @if(!$logs->onFirstPage())
                <a href="{{ $logs->previousPageUrl() }}"
                    class="w-7 h-7 flex items-center justify-center rounded border border-slate-200 dark:border-[#3d3d3d] text-slate-500 hover:bg-slate-100 dark:hover:bg-[#2a2a2a] no-underline transition-colors">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
            @endif

            @php
                $window = \Illuminate\Pagination\UrlWindow::make($logs);
                $elements = array_filter([
                    $window['first'],
                    is_array($window['slider']) ? '...' : null,
                    $window['slider'],
                    is_array($window['last']) ? '...' : null,
                    $window['last'],
                ]);
            @endphp
            @foreach($elements as $element)
                @if(is_string($element))
                    <span class="w-7 h-7 flex items-center justify-center rounded text-[12px] text-slate-400 dark:text-slate-500">{{ $element }}</span>
                @endif
                @if(is_array($element))
                    @foreach($element as $page => $url)
                        @if($page == $logs->currentPage())
                            <span class="w-7 h-7 flex items-center justify-center rounded border text-[12px] font-medium bg-red-700 text-white border-red-700">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="w-7 h-7 flex items-center justify-center rounded border border-slate-200 dark:border-[#3d3d3d] text-slate-500 hover:bg-slate-50 dark:hover:bg-[#2a2a2a] no-underline text-[12px] font-medium transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if($logs->hasMorePages())
                <a href="{{ $logs->nextPageUrl() }}"
                    class="w-7 h-7 flex items-center justify-center rounded border border-slate-200 dark:border-[#3d3d3d] text-slate-500 hover:bg-slate-100 dark:hover:bg-[#2a2a2a] no-underline transition-colors">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            @endif
        </div>
        @endif
    </div>

</div>

{{-- ══ DETAIL DRAWER ══ --}}
<div id="auditDrawerBackdrop" onclick="closeAuditDetail()"
    class="fixed inset-0 bg-black/20 z-[200] hidden"></div>

<div id="auditDrawer"
    class="fixed top-0 right-0 h-full w-[400px] max-w-[95vw] bg-white dark:bg-[#1e1e1e] shadow-2xl z-[201]
    translate-x-full transition-transform duration-300 ease-in-out flex flex-col">

    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 dark:border-[#2d2d2d] shrink-0">
        <h3 class="text-[15px] font-bold text-slate-800 dark:text-white">Detail Perubahan</h3>
        <button onclick="closeAuditDetail()"
            class="w-7 h-7 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors cursor-pointer bg-transparent border-none">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto px-6 py-5 flex flex-col gap-5">
        <div>
            <p class="text-[9.5px] font-bold uppercase tracking-widest text-slate-400 mb-1">Sebelum</p>
            <pre id="drawerOldValues" class="bg-slate-50 dark:bg-[#2a2a2a] border border-slate-200 dark:border-[#3d3d3d] rounded-lg p-3 text-[11.5px] text-slate-700 dark:text-slate-300 overflow-x-auto whitespace-pre-wrap break-words font-mono leading-relaxed">—</pre>
        </div>
        <div>
            <p class="text-[9.5px] font-bold uppercase tracking-widest text-slate-400 mb-1">Sesudah</p>
            <pre id="drawerNewValues" class="bg-slate-50 dark:bg-[#2a2a2a] border border-slate-200 dark:border-[#3d3d3d] rounded-lg p-3 text-[11.5px] text-slate-700 dark:text-slate-300 overflow-x-auto whitespace-pre-wrap break-words font-mono leading-relaxed">—</pre>
        </div>
    </div>
</div>

@push('modals')
<script>
// Encode log data for drawer
const AUDIT_LOGS = {
@foreach($logs as $log)
    {{ $log->id }}: {
        old: @json($log->old_values),
        new: @json($log->new_values),
    },
@endforeach
};

function openAuditDetail(id) {
    const a = AUDIT_LOGS[id];
    if (!a) return;
    document.getElementById('drawerOldValues').textContent = a.old ? JSON.stringify(a.old, null, 2) : '—';
    document.getElementById('drawerNewValues').textContent = a.new ? JSON.stringify(a.new, null, 2) : '—';
    document.getElementById('auditDrawerBackdrop').classList.remove('hidden');
    setTimeout(() => document.getElementById('auditDrawer').classList.remove('translate-x-full'), 10);
}

function closeAuditDetail() {
    document.getElementById('auditDrawer').classList.add('translate-x-full');
    document.getElementById('auditDrawerBackdrop').classList.add('hidden');
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAuditDetail(); });
</script>

<script>
// ── Custom Select Initialization ─────────────────────────────────────────
(function() {
    document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
        const realSelect = wrapper.querySelector('.real-select');
        const btn = wrapper.querySelector('.select-btn');
        const text = wrapper.querySelector('.select-text');
        const dropdown = wrapper.querySelector('.select-dropdown');

        const populateDropdown = () => {
            dropdown.innerHTML = '';
            const selectedOpt = realSelect.options[realSelect.selectedIndex];
            if (selectedOpt) text.textContent = selectedOpt.text;

            Array.from(realSelect.options).forEach((opt, index) => {
                const li = document.createElement('li');
                li.textContent = opt.text;
                li.className = 'px-3 py-1.5 cursor-pointer transition-colors ' +
                    (index === realSelect.selectedIndex
                        ? 'bg-red-50 text-red-700 font-medium dark:bg-red-900/30 dark:text-red-400'
                        : 'hover:bg-red-50 hover:text-red-700 dark:hover:bg-[#3d3d3d] dark:hover:text-red-400');

                li.addEventListener('click', (e) => {
                    e.stopPropagation();
                    realSelect.selectedIndex = index;
                    text.textContent = opt.text;
                    dropdown.classList.add('hidden');
                    populateDropdown();
                    if (typeof realSelect.onchange === 'function') {
                        realSelect.onchange({ target: realSelect });
                    } else if (realSelect.getAttribute('onchange')) {
                        eval(realSelect.getAttribute('onchange'));
                    } else {
                        realSelect.dispatchEvent(new Event('change'));
                    }
                });
                dropdown.appendChild(li);
            });
        };

        populateDropdown();

        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const isHidden = dropdown.classList.contains('hidden');
            document.querySelectorAll('.select-dropdown:not(.hidden)').forEach(d => {
                d.classList.add('hidden');
                d.parentElement.querySelector('.select-btn')?.classList.remove('ring-1', 'ring-red-400');
            });
            if (isHidden) {
                dropdown.classList.remove('hidden');
                btn.classList.add('ring-1', 'ring-red-400');
            } else {
                btn.classList.remove('ring-1', 'ring-red-400');
            }
        });
    });

    document.addEventListener('click', () => {
        document.querySelectorAll('.select-dropdown').forEach(d => {
            d.classList.add('hidden');
            d.parentElement.querySelector('.select-btn')?.classList.remove('ring-1', 'ring-red-400');
        });
    });
})();
</script>
@endpush

@endsection
