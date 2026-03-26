@extends('layouts.app')

@section('page-title', 'Log Peringatan')

@section('content')

@php
/* ── helpers ── */
$kategoriColor = [
    'Kenyamanan' => 'text-blue-600 bg-blue-50',
    'Energi'     => 'text-red-600 bg-red-50',
    'Perangkat'  => 'text-orange-500 bg-orange-50',
    'Sensor'     => 'text-slate-700 bg-slate-100',
];

function logBadge(string $type): string {
    return match($type) {
        'critical' => '<span class="inline-flex items-center gap-1 text-[11px] font-semibold text-red-600 bg-red-50 rounded-full px-2.5 py-0.5"><img src="' . asset('icons/poor.svg') . '" alt="Poor" class="w-4 h-4 shrink-0">Poor</span>',
        default    => '<span class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-600 bg-orange-50 rounded-full px-2.5 py-0.5"><img src="' . asset('icons/warning.svg') . '" alt="Warning" class="w-4 h-4 shrink-0">Warning</span>',
    };
}
@endphp

<div class="bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-2xl shadow-sm border border-slate-200 overflow-visible">

    {{-- ── Filter Bar ── --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-[#2d2d2d] gap-3 flex-wrap">
        <form method="GET" action="{{ route('log-peringatan.index') }}" id="logForm" class="flex items-center gap-2 flex-wrap">

            {{-- Waktu --}}
            <div class="relative">
                <select name="waktu" onchange="document.getElementById('logForm').submit()"
                    class="appearance-none border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg pl-3 pr-8 py-[7px] text-[12.5px] text-slate-700 focus:outline-none focus:border-red-400 bg-white cursor-pointer">
                    <option value="hari_ini" {{ (request('waktu','hari_ini')==='hari_ini') ? 'selected':'' }}>Waktu: Hari Ini</option>
                    <option value="7hari"    {{ request('waktu')==='7hari'   ? 'selected':'' }}>Waktu: 7 Hari</option>
                    <option value="30hari"   {{ request('waktu')==='30hari'  ? 'selected':'' }}>Waktu: 30 Hari</option>
                    <option value="semua"    {{ request('waktu')==='semua'   ? 'selected':'' }}>Waktu: Semua</option>
                </select>
            </div>

            {{-- Kategori --}}
            <div class="relative">
                <select name="kategori" onchange="document.getElementById('logForm').submit()"
                    class="appearance-none border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg pl-3 pr-8 py-[7px] text-[12.5px] text-slate-700 focus:outline-none focus:border-red-400 bg-white cursor-pointer">
                    <option value="">Kategori: Semua</option>
                    @foreach($kategori as $kat)
                        <option value="{{ $kat }}" {{ request('kategori')===$kat ? 'selected':'' }}>Kategori: {{ $kat }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Ruangan --}}
            <div class="relative">
                <select name="room_id" onchange="document.getElementById('logForm').submit()"
                    class="appearance-none border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg pl-3 pr-8 py-[7px] text-[12.5px] text-slate-700 focus:outline-none focus:border-red-400 bg-white cursor-pointer">
                    <option value="">Ruangan: Semua</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ request('room_id')==$room->id ? 'selected':'' }}>Ruangan: {{ $room->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Status --}}
            <div class="relative">
                <select name="severity" onchange="document.getElementById('logForm').submit()"
                    class="appearance-none border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg pl-3 pr-8 py-[7px] text-[12.5px] text-slate-700 focus:outline-none focus:border-red-400 bg-white cursor-pointer">
                    <option value="">Status: Semua</option>
                    <option value="warning"  {{ request('severity')==='warning'  ? 'selected':'' }}>Status: Warning</option>
                    <option value="critical" {{ request('severity')==='critical' ? 'selected':'' }}>Status: Poor</option>
                </select>
            </div>

        </form>

        {{-- Search --}}
        <form method="GET" action="{{ route('log-peringatan.index') }}" class="flex items-center gap-2">
            <input type="hidden" name="waktu"    value="{{ request('waktu','hari_ini') }}">
            <input type="hidden" name="kategori" value="{{ request('kategori') }}">
            <input type="hidden" name="room_id"  value="{{ request('room_id') }}">
            <input type="hidden" name="severity" value="{{ request('severity') }}">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari ..."
                    class="pl-8 pr-3 py-[7px] border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg text-[12.5px] text-slate-700 focus:outline-none focus:border-red-400 bg-white w-44">
            </div>
        </form>
    </div>

    {{-- Action bar: tampil hanya jika ada yang belum dibaca --}}
    @if($alerts->getCollection()->contains(fn($a) => !$a->is_read))
    <div id="action-bar-unread" class="flex justify-end px-5 py-2 border-b border-slate-100 dark:border-[#2d2d2d]">
        <button type="button" onclick="markAllRead()"
            id="btn-mark-all-read"
            class="flex items-center gap-1.5 text-[12px] font-medium text-slate-500 dark:text-slate-400 hover:text-red-600 dark:hover:text-red-400 transition-colors cursor-pointer bg-transparent border-none px-0">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 12 4 10"/><line x1="4" y1="20" x2="20" y2="20"/></svg>
            Tandai Semua Dibaca
        </button>
    </div>
    @endif

    {{-- ── Table ── --}}
    <div class="overflow-x-auto">
        <table class="w-full text-[13px]">
            <thead>
                <tr class="text-left bg-red-100 text-slate-600 text-[12px] border-b border-slate-100 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200">
                    <th class="px-5 py-3 font-semibold">Waktu</th>
                    <th class="px-5 py-3 font-semibold">Nama Peringatan</th>
                    <th class="px-5 py-3 font-semibold">Kategori</th>
                    <th class="px-5 py-3 font-semibold">Ruangan</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="px-5 py-3 font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($alerts as $alert)
                    @php
                        $ruleName  = $alert->alertRule?->name  ?? '—';
                        $ruleKat   = $alert->alertRule?->kategori ?? '';
                        $katClass  = $kategoriColor[$ruleKat] ?? 'text-slate-500 bg-slate-100';
                    @endphp
                    <tr id="alert-row-{{ $alert->id }}" class="hover:bg-slate-50/60 dark:hover:bg-[#2a2a2a] transition-colors">

                        <td class="px-5 py-3 text-slate-500 dark:text-slate-400 whitespace-nowrap text-[12.5px]">
                            <div class="flex items-center gap-2">
                                @if(!$alert->is_read)
                                <span id="dot-{{ $alert->id }}" class="w-2 h-2 rounded-full bg-red-500 shrink-0"></span>
                                @else
                                <span id="dot-{{ $alert->id }}" class="w-2 h-2 shrink-0"></span>
                                @endif
                                {{ $alert->created_at->format('H:i') }}
                            </div>
                        </td>

                        <td class="px-5 py-3 text-slate-800 dark:text-slate-200">
                            {{ $ruleName }}
                        </td>

                        <td class="px-5 py-3">
                            @if($ruleKat)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11.5px] font-semibold {{ $katClass }}">{{ $ruleKat }}</span>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>

                        <td class="px-5 py-3 text-slate-700 dark:text-slate-300">{{ $alert->room?->name ?? '—' }}</td>

                        <td class="px-5 py-3">{!! logBadge($alert->type) !!}</td>

                        <td class="px-5 py-3 text-center">
                            <button title="Detail"
                                onclick="openLogDetail({{ $alert->id }})"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-slate-100 dark:hover:bg-[#2a2a2a] text-slate-400 hover:text-slate-700 transition-colors cursor-pointer bg-white dark:bg-transparent">
                                <img src="{{ asset('icons/detail.svg') }}" alt="Detail" class="w-6 h-6 shrink-0">
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-14 text-center">
                            <div class="flex flex-col items-center text-slate-400">
                                <img src="{{ asset('icons/detail.svg') }}" alt="Detail" class="w-6 h-6 shrink-0">
                                <p class="text-[13px] mt-1">Tidak ada log peringatan</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Footer / Pagination ── --}}
    <div class="flex items-center justify-between px-5 py-4 border-t border-slate-50">
        <span class="text-[12px] text-slate-400 dark:text-slate-500">
            @if($alerts->total() > 0)
                Menampilkan {{ $alerts->firstItem() }} dari {{ $alerts->total() }} data
            @else
                Tidak ada data
            @endif
        </span>
        @if($alerts->hasPages())
        <div class="flex items-center gap-1">
            @if(!$alerts->onFirstPage())
                <a href="{{ $alerts->previousPageUrl() }}"
                    class="w-7 h-7 flex items-center justify-center rounded border border-slate-200 text-slate-500 hover:bg-slate-100 no-underline">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
            @endif
            @foreach($alerts->getUrlRange(1, $alerts->lastPage()) as $page => $url)
                <a href="{{ $url }}"
                    class="w-7 h-7 flex items-center justify-center rounded border text-[12px] font-medium no-underline
                    {{ $page == $alerts->currentPage() ? 'bg-red-700 text-white border-red-700' : 'border-slate-200 text-slate-500 hover:bg-slate-50' }}">
                    {{ $page }}
                </a>
            @endforeach
            @if($alerts->hasMorePages())
                <a href="{{ $alerts->nextPageUrl() }}"
                    class="w-7 h-7 flex items-center justify-center rounded border border-slate-200 text-slate-500 hover:bg-slate-100 no-underline">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            @endif
        </div>
        @endif
    </div>

</div>

{{-- ══ DETAIL DRAWER (right side) ══════════════════════════════════════════════ --}}
{{-- Backdrop --}}
<div id="logDrawerBackdrop"
    onclick="closeLogDetail()"
    class="fixed inset-0 bg-black/20 z-[200] hidden"></div>

{{-- Drawer Panel --}}
<div id="logDrawer"
    class="fixed top-0 right-0 h-full w-[340px] max-w-[95vw] bg-white dark:bg-[#1e1e1e] shadow-2xl z-[201]
    translate-x-full transition-transform duration-300 ease-in-out flex flex-col">

    {{-- Drawer Header --}}
    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 dark:border-[#2d2d2d] shrink-0">
        <h3 class="text-[15px] font-bold text-slate-800 dark:text-white">Detail Peringatan</h3>
        <button onclick="closeLogDetail()"
            class="w-7 h-7 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors cursor-pointer bg-transparent border-none">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    {{-- Drawer Body --}}
    <div class="flex-1 overflow-y-auto px-6 py-5 flex flex-col gap-5">

        {{-- Severity + Name --}}
        <div>
            <div class="flex items-center justify-between mb-1">
                <span class="text-[9.5px] font-bold uppercase tracking-widest text-slate-400">Peringatan</span>
                <div id="drawerBadge"></div>
            </div>
            <div id="drawerName" class="text-[22px] font-bold text-slate-800 dark:text-white leading-tight mt-1">—</div>
            <div id="drawerKategori" class="mt-1.5"></div>
        </div>

        {{-- Informasi Umum --}}
        <div>
            <p class="text-[12px] font-bold text-slate-800 dark:text-white mb-3">Informasi Umum</p>
            <div class="flex flex-col gap-2.5">
                <div class="flex justify-between text-[12.5px]">
                    <span class="text-slate-500 dark:text-slate-400">Ruangan</span>
                    <span id="drawerRoom" class="text-slate-800 dark:text-slate-200 font-medium text-right">—</span>
                </div>
                <div class="flex justify-between text-[12.5px]">
                    <span class="text-slate-500 dark:text-slate-400">Waktu Mulai</span>
                    <span id="drawerTime" class="text-slate-800 dark:text-slate-200 font-medium">—</span>
                </div>
                <div class="flex justify-between text-[12.5px]">
                    <span class="text-slate-500 dark:text-slate-400">Waktu Selesai</span>
                    <span class="text-green-600 font-semibold text-[12px]">Masih Aktif</span>
                </div>
                <div class="flex justify-between text-[12.5px]">
                    <span class="text-slate-500 dark:text-slate-400">Durasi</span>
                    <span id="drawerDurasi" class="text-slate-800 dark:text-slate-200 font-medium">—</span>
                </div>
            </div>
        </div>

        {{-- Metrik Sensor --}}
        <div>
            <p class="text-[12px] font-bold text-slate-800 dark:text-white mb-3">Metrik Sensor</p>
            <div class="flex flex-col gap-2.5">
                <div class="flex justify-between text-[12.5px]">
                    <span class="text-slate-500 dark:text-slate-400">Nilai Saat Trigger</span>
                    <span id="drawerNilai" class="text-slate-800 dark:text-slate-200 font-medium">—</span>
                </div>
                <div class="flex justify-between text-[12.5px]">
                    <span class="text-slate-500 dark:text-slate-400">Ambang Batas</span>
                    <span id="drawerThreshold" class="text-slate-800 dark:text-slate-200 font-medium">—</span>
                </div>
                <div class="flex justify-between text-[12.5px]">
                    <span class="text-slate-500 dark:text-slate-400">Kondisi</span>
                    <span id="drawerCondition" class="text-slate-800 dark:text-slate-200 font-medium font-mono">—</span>
                </div>
            </div>
        </div>

    </div>
</div>

@push('modals')
<script>
// Encode all alerts as JSON for the drawer
const LOG_ALERTS = {
@foreach($alerts as $alert)
    {{ $alert->id }}: {
        name:      @json($alert->alertRule?->name ?? '—'),
        kategori:  @json($alert->alertRule?->kategori ?? ''),
        room:      @json($alert->room?->name ?? '—'),
        type:      @json($alert->type),
        nilai:     @json($alert->nilai),
        unit:      @json($alert->alertRule?->parameter_key ?? ''),
        threshold: @json($alert->alertRule?->threshold),
        condition: @json($alert->alertRule?->condition ?? ''),
        waktu:     @json($alert->created_at->format('H:i, d/m/Y')),
        durasi:    @json($alert->created_at->diffForHumans(null, true)),
        is_read:   {{ $alert->is_read ? 'true' : 'false' }},
    },
@endforeach
};

const KAT_COLORS = {
    'Kenyamanan': 'text-blue-600 bg-blue-50',
    'Energi':     'text-red-600 bg-red-50',
    'Perangkat':  'text-orange-500 bg-orange-50',
    'Sensor':     'text-slate-700 bg-slate-100',
};

function openLogDetail(id) {
    const a = LOG_ALERTS[id];
    if (!a) return;

    // Mark as read (dot hilang)
    if (!a.is_read) {
        a.is_read = true;
        const dot = document.getElementById('dot-' + id);
        if (dot) {
            dot.className = 'w-2 h-2 shrink-0'; // hapus warna merah
        }
        // AJAX mark read
        fetch(`/log-peringatan/${id}/read`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Accept': 'application/json'
            }
        }).then(r => {
            if (r.ok && typeof window.decrementAlertBadge === 'function') {
                window.decrementAlertBadge();
            }
        }).catch(() => {});
    }

    // Badge drawer
    document.getElementById('drawerBadge').innerHTML = a.type === 'critical'
        ? '<span class="inline-flex items-center gap-1 text-[11px] font-semibold text-red-600 bg-red-50 rounded-full px-2.5 py-0.5"><img src="{{ asset('icons/poor.svg') }}" class="w-4 h-4 shrink-0">Poor</span>'
        : '<span class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-600 bg-orange-50 rounded-full px-2.5 py-0.5"><img src="{{ asset('icons/warning.svg') }}" class="w-4 h-4 shrink-0">Warning</span>';

    document.getElementById('drawerName').textContent = a.name;

    const katColor = KAT_COLORS[a.kategori] ?? 'text-slate-600 bg-slate-100';
    document.getElementById('drawerKategori').innerHTML = a.kategori
        ? `<span class="inline-flex items-center px-2.5 py-0.5 rounded text-[12px] font-semibold ${katColor}">${a.kategori}</span>`
        : '';

    document.getElementById('drawerRoom').textContent      = a.room;
    document.getElementById('drawerTime').textContent      = a.waktu;
    document.getElementById('drawerDurasi').textContent    = a.durasi;
    document.getElementById('drawerNilai').textContent     = a.nilai !== null ? a.nilai + (a.unit ? ' ' + a.unit : '') : '—';
    document.getElementById('drawerThreshold').textContent = a.threshold !== null ? a.threshold + (a.unit ? ' ' + a.unit : '') : '—';
    document.getElementById('drawerCondition').textContent = a.condition ? (a.unit + ' ' + a.condition + ' ' + a.threshold) : '—';

    document.getElementById('logDrawerBackdrop').classList.remove('hidden');
    setTimeout(() => {
        document.getElementById('logDrawer').classList.remove('translate-x-full');
    }, 10);
}

function closeLogDetail() {
    document.getElementById('logDrawer').classList.add('translate-x-full');
    document.getElementById('logDrawerBackdrop').classList.add('hidden');
}

// ESC key closes drawer
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLogDetail(); });

function markAllRead() {
    fetch('/log-peringatan/read-all', {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            'Accept': 'application/json'
        }
    }).then(r => {
        if (!r.ok) return;
        // Semua dot hilang
        document.querySelectorAll('[id^="dot-"]').forEach(dot => {
            dot.className = 'w-2 h-2 shrink-0';
        });
        // Update LOG_ALERTS is_read
        Object.values(LOG_ALERTS).forEach(a => { a.is_read = true; });
        // Badge topbar → 0
        const badge = document.getElementById('alert-badge');
        if (badge) badge.classList.add('hidden');
        // Sembunyikan tombol
        const btn = document.getElementById('btn-mark-all-read');
        if (btn) btn.closest('div').classList.add('hidden');
    }).catch(() => {});
}
</script>
@endpush

@endsection
