@extends('layouts.app')

@section('content')
<!-- SUMMARY CARDS -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3.5 mb-5">
    <div class="bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-xl px-4 py-3.5 shadow-[0_1px_4px_rgba(0,0,0,.07)]">
        <div class="text-[13px] text-slate-900 dark:text-slate-200 font-medium mb-2 flex items-center gap-1.5">
            <img src="{{ asset('icons/status.svg') }}" alt="Normal" class="w-7 h-7">
            Status Ruangan
        </div>
        <div class="flex items-center gap-1 text-[18px] font-bold ">
            <img src="{{ asset('icons/normal.svg') }}" alt="Normal" class="w-7 h-7">
            <span data-status-count="normal" class="text-[22px] font-bold text-slate-800 dark:text-white">{{ $statusCounts['normal'] }}</span>
            <img src="{{ asset('icons/warning.svg') }}" alt="Warning" class="w-7 h-7">
            <span data-status-count="warning" class="text-[22px] font-bold text-slate-800 dark:text-white">{{ $statusCounts['warning'] }}</span>
            <img src="{{ asset('icons/poor.svg') }}" alt="Poor" class="w-7 h-7">
            <span data-status-count="poor" class="text-[22px] font-bold text-slate-800 dark:text-white">{{ $statusCounts['poor'] }}</span>
        </div>  
    </div>
    <div class="bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-xl px-4 py-3.5 shadow-[0_1px_4px_rgba(0,0,0,.07)]">
        <div class="text-[13px] text-slate-900 dark:text-slate-200 font-medium mb-2 flex items-center gap-1.5">
            <img src="{{ asset('icons/suhu-rerata.svg') }}" alt="Normal" class="w-7 h-7">
            Rerata Suhu
        </div>
        <div><span class="text-[22px] font-bold text-slate-800 dark:text-white">{{ number_format($avgTemp ?? 0, 1) }}</span> <span class="text-[22px] font-bold text-slate-800 dark:text-white">°C</span></div>
    </div>
    <div class="bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-xl px-4 py-3.5 shadow-[0_1px_4px_rgba(0,0,0,.07)]">
        <div class="text-[13px] text-slate-900 dark:text-slate-200 font-medium mb-2 flex items-center gap-1.5">
            <img src="{{ asset('icons/kelembapan.svg') }}" alt="Normal" class="w-7 h-7">
            Rerata Kelembaban
        </div>
        <div><span class="text-[22px] font-bold text-slate-800 dark:text-white">{{ number_format($avgHumidity ?? 0, 0) }}</span> <span class="text-[22px] font-bold text-slate-800 dark:text-white">%</span></div>
    </div>
    <div class="bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-xl px-4 py-3.5 shadow-[0_1px_4px_rgba(0,0,0,.07)]">
        <div class="text-[13px] text-slate-900 dark:text-slate-200 font-medium mb-2 flex items-center gap-1.5">
            <img src="{{ asset('icons/daya.svg') }}" alt="Normal" class="w-7 h-7">
            Daya Saat Ini
        </div>
        <div><span class="text-[22px] font-bold text-slate-800 dark:text-white">{{ number_format($currentPower, 1) }}</span> <span class="text-[22px] font-bold text-slate-800 dark:text-white">kW</span></div>
    </div>
    <div class="bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-xl px-4 py-3.5 shadow-[0_1px_4px_rgba(0,0,0,.07)]">
        <div class="text-[13px] text-slate-900 dark:text-slate-200 font-medium mb-2 flex items-center gap-1.5">
            <img src="{{ asset('icons/energi.svg') }}" alt="Normal" class="w-7 h-7">
            Energi Hari Ini
        </div>
        <div><span class="text-[22px] font-bold text-slate-800 dark:text-white">{{ number_format($energyToday, 0) }}</span> <span class="text-[22px] font-bold text-slate-800 dark:text-white">kWh</span></div>
    </div>
    <div class="bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-xl px-4 py-3.5 shadow-[0_1px_4px_rgba(0,0,0,.07)]">
        <div class="text-[13px] text-slate-900 dark:text-slate-200 font-medium mb-2 flex items-center gap-1.5">
            <img src="{{ asset('icons/unit_ac.svg') }}" alt="Normal" class="w-7 h-7">
            Unit AC Aktif
        </div>
        <div><span class="text-[22px] font-bold text-slate-800 dark:text-white">{{ $activeAc }}/{{ $totalAc }}</span></div>
    </div>
</div>

<!-- ROOM HOVER TOOLTIP -->
<div id="room-tooltip" class="hidden fixed z-[9999] bg-white rounded-xl shadow-[0_8px_24px_rgba(0,0,0,.18)] px-3.5 py-3 min-w-[170px] pointer-events-none border border-slate-200 font-['Inter']">
    <div id="tt-name" class="text-[14px] font-bold text-slate-800 mb-2"></div>
    <div id="tt-status" class="text-[11px] font-semibold px-2.5 py-1 rounded-full inline-flex items-center gap-1.5 mb-2.5"></div>
    <div class="flex items-center gap-2 text-[13px] text-slate-700 py-0.5">
        <img src="{{ asset('icons/dashboard/suhu.svg') }}" class="w-4 h-4" alt="Suhu">
        <span id="tt-temp"></span>
    </div>
    <div class="flex items-center gap-2 text-[13px] text-slate-700 py-0.5">
        <img src="{{ asset('icons/dashboard/kelembaban.svg') }}" class="w-4 h-4" alt="Hum">
        <span id="tt-hum"></span>
    </div>
    <div class="flex items-center gap-2 text-[13px] text-slate-700 py-0.5">
        <img id="tt-ac-icon" src="{{ asset('icons/dashboard/ac_off.svg') }}" class="w-4 h-4" alt="AC">
        <span id="tt-ac"></span>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-4">

    <!-- FLOOR PLAN — Fabric.js canvas (read-only, from manajemen denah) -->
    <div class="min-w-0 bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-xl p-4 shadow-[0_1px_4px_rgba(0,0,0,.07)]">
        <div class="flex items-center justify-between mb-3">
            <div class="text-[14px] font-semibold text-slate-800 dark:text-white">
                Denah
                @if($displayFloor)
                    — {{ $displayFloor->building->name ?? '' }} · {{ $displayFloor->name }}
                @endif
            </div>
        </div>

        @if($displayFloor)
            <!-- Canvas rendered from editor canvas_data + room markers -->
            <div class="w-full rounded-lg overflow-hidden bg-slate-100 dark:bg-[#1a1a1a] relative" id="dashCanvasWrapper" style="min-height: 400px;">
                <canvas id="dash-canvas"></canvas>
                <div id="dashCanvasHint" class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 text-[13px]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2 opacity-40 animate-spin" style="animation-duration:2s"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg>
                    Memuat denah...
                </div>

                {{-- Status legend overlay (bottom-left) --}}
                <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm rounded-xl shadow-sm border border-slate-100 px-3.5 py-3 z-10">
                    <div class="text-[12px] font-semibold text-slate-600 mb-1.5">Status Ruangan</div>
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-1.5 text-[12px] text-slate-900">
                            <img src="{{ asset('icons/normal.svg') }}" alt="Normal" class="w-4 h-4"> Normal
                        </div>
                        <div class="flex items-center gap-1.5 text-[12px] text-slate-900">
                            <img src="{{ asset('icons/warning.svg') }}" alt="Warning" class="w-4 h-4"> Warning
                        </div>
                        <div class="flex items-center gap-1.5 text-[12px] text-slate-900">
                            <img src="{{ asset('icons/poor.svg') }}" alt="Poor" class="w-4 h-4"> Poor
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="flex flex-col items-center justify-center h-[420px] text-slate-400 text-[13px]">
                <i data-feather="map" class="w-12 h-12 mb-3 opacity-30"></i>
                <p class="font-medium">Belum ada denah tersedia</p>
                <p class="text-[12px] mt-1">Upload denah di <a href="{{ route('admin.buildings.index') }}" class="text-[#4f7dfc] no-underline hover:underline">Manajemen Denah</a></p>
            </div>
        @endif
    </div>

    <!-- RIGHT PANEL -->
    <div class="flex flex-col gap-4">
        <!-- ROOM DETAIL — instant from pre-loaded data -->
        <div class="bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-xl p-[18px] shadow-[0_1px_4px_rgba(0,0,0,.07)]">
            <div class="text-[14px] font-semibold text-slate-800 dark:text-white mb-3.5">Detail Ruangan</div>
            <div id="room-detail-content">
                <div class="text-center py-8 text-slate-400 text-[13px]">
                    <i data-feather="mouse-pointer" class="w-8 h-8 block mx-auto mb-2 opacity-40"></i>
                    Klik marker ruangan pada denah
                </div>
            </div>
        </div>

        <!-- RECENT ALERTS -->
        <div class="bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-xl p-[18px] shadow-[0_1px_4px_rgba(0,0,0,.07)]">
            <div class="text-[14px] font-semibold text-slate-800 dark:text-white mb-3.5">Peringatan Terbaru</div>
            @forelse($recentAlerts as $alert)
                @php
                    $paramKey = $alert->alertRule?->parameter_key ?? '';
                    $kateg    = $alert->alertRule?->kategori ?? '';

                    // Ikon berdasarkan parameter_key lalu kategori
                    $alertIcon = match(true) {
                        str_contains($paramKey, 'suhu') || str_contains($paramKey, 'temp')  => 'suhu-tinggi',
                        str_contains($paramKey, 'co2')                                       => 'co2_tinggi',
                        str_contains($paramKey, 'daya') || str_contains($paramKey, 'power')
                            || str_contains($paramKey, 'tegangan')                           => 'daya-tinggi',
                        $kateg === 'Perangkat'                                               => 'freeze',
                        $kateg === 'Sensor' || str_contains($paramKey, 'sensor')             => 'sensor-offline',
                        // legacy types
                        $alert->type === 'sensor_offline'                                   => 'sensor-offline',
                        $alert->type === 'co2_tinggi' || $alert->type === 'co2_rendah'      => 'co2_tinggi',
                        $alert->type === 'high_temp'                                        => 'suhu-tinggi',
                        $alert->type === 'ac_off'                                           => 'freeze',
                        $alert->type === 'high_power'                                       => 'daya-tinggi',
                        default                                                             => 'alert-triangle',
                    };

                    $isCo2Alert = in_array($alert->type, ['co2_tinggi', 'co2_rendah'])
                        || str_contains($paramKey, 'co2');

                    $alertTitle = $alert->alertRule?->name
                        ?? ($alert->message ? \Str::limit($alert->message, 35) : ucfirst(str_replace('_', ' ', $alert->type)));
                @endphp
                <div class="flex items-center gap-2.5 py-2.5 border-b border-slate-50 last:border-0">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0">
                        @if($isCo2Alert)
                            <div style="
                                width:20px; height:20px;
                                background-color: #92400e;
                                -webkit-mask-image: url('{{ asset('icons/co2_tinggi.svg') }}');
                                mask-image: url('{{ asset('icons/co2_tinggi.svg') }}');
                                -webkit-mask-size: contain; mask-size: contain;
                                -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat;
                                -webkit-mask-position: center; mask-position: center;
                            "></div>
                        @else
                            <img src="{{ asset('icons/' . $alertIcon . '.svg') }}"
                                onerror="this.src='{{ asset('icons/status.svg') }}'"
                                alt="{{ $alertTitle }}" class="w-5 h-5">
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-[13px] font-semibold text-slate-800 dark:text-slate-200 truncate">{{ $alertTitle }}</div>
                        <div class="text-[11px] text-slate-400">{{ $alert->room?->name ?? '-' }}</div>
                    </div>
                    <div class="text-[11px] text-slate-400 whitespace-nowrap">{{ $alert->created_at->format('H:i') }}</div>
                </div>

            @empty
                <div class="text-center text-slate-400 text-[12px] py-4">Tidak ada peringatan</div>
            @endforelse
            <a href="{{ route('log-peringatan.index') }}"
                class="flex items-center justify-end gap-1 text-[12px] text-[#4f7dfc] mt-2.5 cursor-pointer font-medium no-underline hover:text-blue-700 transition-colors dark:text-[#FFFFFF] dark:hover:text-[#FFFFFF]">
                Lihat Semua
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
    </div>
</div>

@php
    // Pre-load all room data for instant detail display
    $floorPlanUrl  = $displayFloor?->plan_url;
    $canvasData    = $displayFloor?->canvas_data;

    // Room markers from the display floor
    // Terapkan effective status: poor jika sensor offline > 60 menit
    $floorOfflineThreshold = now()->subMinutes(60);
    $floorLatestReadings   = $displayFloor
        ? \App\Models\SensorReadingLatest::whereIn('room_id', $displayFloor->rooms->pluck('id'))
            ->get()->keyBy('room_id')
        : collect([]);

    $floorRooms = $displayFloor ? $displayFloor->rooms->map(function ($r) use ($floorOfflineThreshold, $floorLatestReadings) {
        $lat        = $floorLatestReadings->get($r->id);
        $isOffline  = $lat !== null && ($lat->waktu === null || $lat->waktu->lt($floorOfflineThreshold));
        $effStatus  = $isOffline ? 'poor' : $r->status;

        // Langsung update DB jika perlu (supaya refresh juga konsisten)
        if ($isOffline && $r->status !== 'poor') {
            \App\Models\Room::where('id', $r->id)->update(['status' => 'poor', 'updated_at' => now()]);
        }

        return [
            'id'       => $r->id,
            'name'     => $r->name,
            'status'   => $effStatus,
            'marker_x' => $r->marker_x ?? 50,
            'marker_y' => $r->marker_y ?? 50,
        ];
    })->values() : collect([]);
@endphp

<script>
// Pre-loaded room detail map — no AJAX needed
const ROOM_DETAIL_MAP = {!! $roomDetailMap->toJson() !!};
const FLOOR_PLAN_URL  = {!! json_encode($floorPlanUrl) !!};
const CANVAS_DATA     = {!! json_encode($canvasData) !!};
const FLOOR_ROOMS     = {!! $floorRooms->toJson() !!};
const STATUS_COLORS   = { normal: '#22c55e', warning: '#f59e0b', poor: '#ef4444' };
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
<script>
/* ═══ DASHBOARD FABRIC.JS CANVAS (Read-Only, Scaled to Fit) ═══ */
let dashCanvas;
let _natW = 900, _natH = 560;
let _zoom = 1;
const _iconCache = {}; // icon URL → fabric.Image, avoids re-fetch on resize

window.addEventListener('load', initDashCanvas);

function initDashCanvas() {
    const wrapper = document.getElementById('dashCanvasWrapper');
    if (!wrapper) return;

    // ── Read natural canvas size saved by editor ──
    if (CANVAS_DATA) {
        try {
            const parsed = JSON.parse(CANVAS_DATA);
            if (parsed._canvasWidth)  _natW = parsed._canvasWidth;
            if (parsed._canvasHeight) _natH = parsed._canvasHeight;
        } catch(e) {}
    }

    // ── Display size: full wrapper width, height follows aspect ratio ──
    const dispW = wrapper.clientWidth || 900;
    _zoom       = dispW / _natW;
    const dispH = Math.round(_natH * _zoom);

    // ── Canvas dibuat di display size ──
    dashCanvas = new fabric.Canvas('dash-canvas', {
        width:  dispW,
        height: dispH,
        selection: false,
        backgroundColor: '#f1f5f9',
    });
    wrapper.style.height = dispH + 'px';

    // ── Viewport transform: semua objek di-render seolah di natural coords ──
    dashCanvas.setViewportTransform([_zoom, 0, 0, _zoom, 0, 0]);

    // ── Load drawing shapes (natural coords, viewport transform handles scaling) ──
    if (CANVAS_DATA) {
        const cleanJson = CANVAS_DATA.replaceAll('\"alphabetical\"', '\"alphabetic\"');
        dashCanvas.loadFromJSON(cleanJson, () => {
            dashCanvas.getObjects().forEach(obj => {
                obj.set({ selectable: false, evented: false, hoverCursor: 'default' });
            });
            dashCanvas.renderAll();
            loadBgAndMarkers();
        });
    } else {
        loadBgAndMarkers();
    }

    document.getElementById('dashCanvasHint')?.remove();
    feather.replace();

    // ResizeObserver: debounce 50ms agar canvas hanya di-redraw sekali
    // setelah ukuran wrapper stabil (bukan di setiap frame animasi sidebar)
    let _roTimer;
    const _ro = new ResizeObserver(() => {
        clearTimeout(_roTimer);
        _roTimer = setTimeout(() => resizeCanvas(), 50);
    });
    _ro.observe(wrapper);
}

function resizeCanvas() {
    const wrapper = document.getElementById('dashCanvasWrapper');
    if (!wrapper || !dashCanvas) return;
    const newW = wrapper.clientWidth;
    if (Math.abs(newW - dashCanvas.getWidth()) < 1) return; // skip sub-pixel changes

    // 1. Update canvas dimensions + viewport transform (fast, no JSON parse)
    _zoom = newW / _natW;
    const newH = Math.round(_natH * _zoom);
    dashCanvas.setDimensions({ width: newW, height: newH });
    dashCanvas.setViewportTransform([_zoom, 0, 0, _zoom, 0, 0]);
    wrapper.style.height = newH + 'px';

    // 2. Remove old marker groups
    dashCanvas.getObjects('group')
        .filter(o => o.data?.roomId)
        .forEach(o => dashCanvas.remove(o));

    // 3. Re-add markers with new _zoom (icons from cache — no network request)
    addRoomMarkers();
}

function loadBgAndMarkers() {
    if (FLOOR_PLAN_URL) {
        fabric.Image.fromURL(FLOOR_PLAN_URL, function(img) {
            // Scale to natural size; viewport transform handles the rest
            const scaleX = _natW / img.width;
            const scaleY = _natH / img.height;
            const scale  = Math.min(scaleX, scaleY);
            img.set({
                scaleX: scale, scaleY: scale,
                selectable: false, evented: false,
                originX: 'left', originY: 'top',
                left: 0, top: 0,
            });
            dashCanvas.setBackgroundImage(img, () => {
                dashCanvas.renderAll();
                addRoomMarkers();
            });
        }, { crossOrigin: 'anonymous' });
    } else {
        addRoomMarkers();
    }
}


// Icon cache helper: fetch once, clone on re-use (resize doesn't hit network)
function getIcon(url, callback) {
    if (_iconCache[url]) {
        _iconCache[url].clone(cloned => callback(cloned));
    } else {
        fabric.Image.fromURL(url, img => {
            _iconCache[url] = img;
            img.clone(cloned => callback(cloned));
        }, { crossOrigin: 'anonymous' });
    }
}

const DASH_STATUS_ICONS = {
    normal:  '{{ asset('icons/normal.svg') }}',
    warning: '{{ asset('icons/warning.svg') }}',
    poor:    '{{ asset('icons/poor.svg') }}',
};
const DASH_STATUS_BG = {
    normal:  '#dcfce7',
    warning: '#fef3c7',
    poor:    '#fee2e2',
};

function addRoomMarkers() {
    // Ukuran marker adaptif: lebih besar di layar lebar, proporsional ke zoom
    const inv      = 1 / _zoom;

    // Base size ditentukan dari display width (semakin lebar canvas, sedikit lebih besar)
    const dispW    = document.getElementById('dashCanvasWrapper')?.clientWidth || 900;
    const baseSq   = dispW >= 1024 ? 40 : (dispW >= 768 ? 36 : 30);
    const baseIcon = dispW >= 1024 ? 26 : (dispW >= 768 ? 22 : 18);
    const baseFont = dispW >= 1024 ? 11 : 10;

    const sqSize   = baseSq   * inv;
    const iconSize = baseIcon * inv;
    const fSize    = baseFont * inv;
    const labelTop = (baseSq - 2) * inv;

    FLOOR_ROOMS.forEach(room => {
        // Posisi dalam natural coords (viewport transform scale ke display)
        const x = (room.marker_x / 100) * _natW;
        const y = (room.marker_y / 100) * _natH;
        const bg      = DASH_STATUS_BG[room.status]    || DASH_STATUS_BG.normal;
        const iconUrl = DASH_STATUS_ICONS[room.status] || DASH_STATUS_ICONS.normal;

        const square = new fabric.Rect({
            width: sqSize, height: sqSize,
            fill: bg, stroke: 'white', strokeWidth: 2 * inv,
            rx: 8 * inv, ry: 8 * inv,
            shadow: new fabric.Shadow({ color: 'rgba(0,0,0,0.20)', blur: 8 * inv, offsetX: 0, offsetY: 3 * inv }),
            originX: 'center', originY: 'center',
        });
        const label = new fabric.Text(room.name, {
            fontSize: fSize, fill: '#1e293b', fontFamily: 'Inter, sans-serif', fontWeight: '700',
            backgroundColor: 'rgba(255,255,255,0.88)', padding: 2 * inv,
            originX: 'center', originY: 'center',
            top: labelTop,
        });

        getIcon(iconUrl, function(icon) {
            icon.set({
                scaleX: iconSize / icon.width,
                scaleY: iconSize / icon.height,
                originX: 'center', originY: 'center',
                top: 0,
            });

            const group = new fabric.Group([square, icon, label], {
                left: x, top: y,
                originX: 'center', originY: 'center',
                selectable: false,
                evented: true,
                hoverCursor: 'pointer',
                data: { roomId: room.id },
            });

            group.on('mousedown', function() { selectRoom(this.data.roomId); });
            group.on('mouseover', function(opt) { showMarkerTooltip(opt.e, this.data.roomId); });
            group.on('mouseout',  function() {
                const t = document.getElementById('room-tooltip');
                t.classList.add('hidden');
                t.style.display = 'none';
            });
            group.on('mousemove', function(opt) { moveTooltip(opt.e); });

            dashCanvas.add(group);
            dashCanvas.renderAll();
        });  // end getIcon
    });
}


/* ═══ INSTANT ROOM DETAIL (from pre-loaded data) ═══ */
let selectedRoomId = null;

function selectRoom(roomId) {
    selectedRoomId = roomId;
    const room = ROOM_DETAIL_MAP[roomId];
    if (!room) {
        document.getElementById('room-detail-content').innerHTML =
            '<div class="text-center py-8 text-slate-400 text-[12px]">Data ruangan tidak ditemukan.</div>';
        return;
    }
    renderRoomDetail(room);
}

function fmtVal(v) {
    if (v === null || v === undefined) return '-';
    const n = parseFloat(v);
    if (isNaN(n)) return v;
    // Jika punya desimal bermakna, tampilkan maks 2 angka di belakang koma; jika tidak, tampil bulat
    return n % 1 === 0 ? n.toString() : parseFloat(n.toFixed(2)).toString();
}

function renderRoomDetail(room) {
    const statusColors = {
        normal:  'bg-green-100 text-green-700',
        warning: 'bg-amber-100 text-amber-700',
        poor:    'bg-red-100 text-red-700',
    };
    const statusLabel = { normal: 'Normal', warning: 'Warning', poor: 'Poor' }[room.status] || room.status;
    const temp = room.temperature ? `${fmtVal(room.temperature.value)} ${room.temperature.unit}` : '-';
    const hum  = room.humidity    ? `${fmtVal(room.humidity.value)} ${room.humidity.unit}`    : '-';
    const co2  = room.co2         ? `${fmtVal(room.co2.value)} ${room.co2.unit}`              : '-';
    const acColor   = room.ac_status === 'ON' ? 'text-green-500' : 'text-red-500';
    const sensorTxt = room.sensor_connected ? '<span class="text-green-500 font-semibold">Terhubung</span>' : '<span class="text-red-500 font-semibold">Offline</span>';

    document.getElementById('room-detail-content').innerHTML = `
        <div class="flex justify-between items-start mb-1">
            <div class="text-[15px] font-bold text-slate-800 dark:text-slate-200">${room.name}</div>
            <span class="text-[11px] px-2 py-0.5 rounded-full font-semibold ${statusColors[room.status] || statusColors.normal}">
                ${room.status === 'warning' ? '▲' : '●'} ${statusLabel}
            </span>
        </div>
        <div class="text-[11px] text-slate-400 flex items-center gap-1.5 mb-3.5">
            <span class="w-2 h-2 rounded-full bg-green-400 inline-block"></span>
            Terakhir diperbarui: ${new Date().toLocaleString('id-ID', {day:'2-digit',month:'2-digit',year:'numeric',hour:'2-digit',minute:'2-digit',hour12:false}).replace(/\./g,':')}
        </div>
        <div class="space-y-0">
            ${detailRow('Suhu', temp)}
            ${detailRow('Kelembaban', hum)}
            ${detailRow('Level CO₂', co2)}
            ${detailRow('Status AC', `<span class="${acColor} font-bold">${room.ac_status}</span>`)}
            ${detailRow('Sensor', sensorTxt)}
        </div>
        <button onclick="window.location.href='{{ route('analisa-data.index') }}?room_id=' + ${room.id}"
            class="block w-full py-2.5 bg-[#B40404] hover:bg-[#B40404] text-white border-none rounded-lg text-[13px] font-semibold cursor-pointer text-center mt-4 transition-colors">
            Analisa Data
        </button>
    `;
}

function detailRow(label, value) {
    return `
        <div class="flex justify-between items-center py-2 border-b border-slate-50 last:border-0 text-[13px]">
            <span class="text-slate-500 dark:text-slate-200">${label}</span>
            <span class="font-semibold text-slate-800 dark:text-slate-200">${value}</span>
        </div>`;
}

/* ═══ TOOLTIP ═══ */
function showMarkerTooltip(event, roomId) {
    const room = ROOM_DETAIL_MAP[roomId];
    if (!room) return;
    const statusIcons = {
        normal:  { img: '{{ asset('icons/normal.svg') }}',  label: 'Normal',  bg: '#dcfce7', color: '#15803d' },
        warning: { img: '{{ asset('icons/warning.svg') }}', label: 'Warning', bg: '#fff7ed', color: '#c2410c' },
        poor:    { img: '{{ asset('icons/poor.svg') }}',    label: 'Poor',    bg: '#fee2e2', color: '#b91c1c' },
    };
    const cfg = statusIcons[room.status] || statusIcons.normal;
    const temp = room.temperature ? `${fmtVal(room.temperature.value)} ${room.temperature.unit}` : '-';
    const hum  = room.humidity    ? `${fmtVal(room.humidity.value)} ${room.humidity.unit}`       : '-';
    const ac   = room.ac_status || '-';

    document.getElementById('tt-name').textContent = room.name;
    const statusEl = document.getElementById('tt-status');
    statusEl.innerHTML = `<img src="${cfg.img}" style="width:16px;height:16px;flex-shrink:0"> ${cfg.label}`;
    statusEl.style.background = cfg.bg;
    statusEl.style.color = cfg.color;
    document.getElementById('tt-temp').textContent = temp;
    document.getElementById('tt-hum').textContent  = hum;
    document.getElementById('tt-ac').textContent   = ac;

    const acIconEl = document.getElementById('tt-ac-icon');
    if (acIconEl) {
        acIconEl.src = (ac === 'ON') 
            ? "{{ asset('icons/dashboard/ac_on.svg') }}" 
            : "{{ asset('icons/dashboard/ac_off.svg') }}";
    }

    const tooltip = document.getElementById('room-tooltip');
    tooltip.classList.remove('hidden');
    tooltip.style.display = 'block';
    moveTooltip(event);
}

function moveTooltip(event) {
    const tooltip = document.getElementById('room-tooltip');
    let x = event.clientX + 14;
    let y = event.clientY + 14;
    if (x + 190 > window.innerWidth)  x = event.clientX - 190;
    if (y + 160 > window.innerHeight) y = event.clientY - 160;
    tooltip.style.left = x + 'px';
    tooltip.style.top  = y + 'px';
}
</script>

<script>
/* ═══ LIVE STATUS POLLING — update marker & detail tanpa reload ═══ */
(function () {
    const ROOMS_STATUS_URL = '/api/dashboard/rooms-status';

    // Elemen summary count (3 angka di summary card pertama)
    const _summarySpans = document.querySelectorAll(
        '.grid.grid-cols-2 > div:first-child span.text-\\[22px\\]'
    );

    async function pollRoomsStatus() {
        try {
            const res  = await fetch(ROOMS_STATUS_URL, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const data = await res.json();   // { rooms: {id: {...}}, status_counts: {...} }

            // ── 1. Update ROOM_DETAIL_MAP in-memory ──────────────────────────
            if (data.rooms) {
                Object.keys(data.rooms).forEach(id => {
                    const fresh = data.rooms[id];
                    if (ROOM_DETAIL_MAP[id]) {
                        // Pertahankan name & ac_status (tidak berubah dari sensor)
                        ROOM_DETAIL_MAP[id].status           = fresh.status;
                        ROOM_DETAIL_MAP[id].sensor_connected = fresh.sensor_connected;
                        ROOM_DETAIL_MAP[id].updated_at       = fresh.updated_at;
                        ROOM_DETAIL_MAP[id].temperature      = fresh.temperature;
                        ROOM_DETAIL_MAP[id].humidity         = fresh.humidity;
                        ROOM_DETAIL_MAP[id].co2              = fresh.co2;
                        ROOM_DETAIL_MAP[id].energy           = fresh.energy;
                        ROOM_DETAIL_MAP[id].power            = fresh.power;
                    }
                    // Update FLOOR_ROOMS status juga (dipakai saat redraw marker)
                    const fr = FLOOR_ROOMS.find(r => String(r.id) === String(id));
                    if (fr) fr.status = fresh.status;
                });
            }

            // ── 2. Redraw semua marker di canvas (warna + icon berubah) ──────
            if (dashCanvas) {
                // Hapus semua marker lama
                dashCanvas.getObjects('group')
                    .filter(o => o.data?.roomId)
                    .forEach(o => dashCanvas.remove(o));
                // Gambar ulang dengan status terbaru
                addRoomMarkers();
            }

            // ── 3. Update summary count card (Normal / Warning / Poor) ───────
            if (data.status_counts) {
                const sc = data.status_counts;
                // Cari span angka di summary card pertama via data-status attribute
                document.querySelectorAll('[data-status-count]').forEach(el => {
                    const key = el.dataset.statusCount;
                    if (sc[key] !== undefined) el.textContent = sc[key];
                });
            }

            // ── 4. Re-render detail panel jika ada room yang sedang dipilih ──
            if (selectedRoomId && ROOM_DETAIL_MAP[selectedRoomId]) {
                renderRoomDetail(ROOM_DETAIL_MAP[selectedRoomId]);
            }

        } catch (e) { /* silent fail */ }
    }

    // Mulai polling setelah 10 detik (beri waktu canvas selesai load)
    setTimeout(() => {
        setInterval(pollRoomsStatus, 10_000);   // update tiap 10 detik
    }, 10_000);
})();
</script>

@if($refreshInterval > 0)
<!-- <div id="refresh-badge"
     style="position:fixed;bottom:16px;right:16px;background:#1e293b;color:#fff;font-size:11px;font-family:Inter,sans-serif;
            padding:5px 10px;border-radius:20px;opacity:.75;z-index:200;display:flex;align-items:center;gap:5px;">
    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/>
        <path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/>
    </svg>
    Refresh dalam <strong id="refresh-count">{{ $refreshInterval }}</strong>s
</div> -->
<script>
(function () {
    const total = {{ $refreshInterval }};
    let left    = total;
    const el    = document.getElementById('refresh-count');
    setInterval(() => {
        left--;
        if (el) el.textContent = left;
        if (left <= 0) window.location.reload();
    }, 1000);
})();
</script>
@endif
@endsection
