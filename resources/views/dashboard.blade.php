@extends('layouts.app')

@section('content')
<!-- SUMMARY CARDS -->
<div class="grid grid-cols-6 gap-3.5 mb-5">
    <div class="bg-white rounded-xl px-4 py-3.5 shadow-[0_1px_4px_rgba(0,0,0,.07)]">
        <div class="text-[13px] text-slate-900 font-medium mb-2 flex items-center gap-1.5">
            <img src="{{ asset('icons/status.svg') }}" alt="Normal" class="w-7 h-7">
            Status Ruangan
        </div>
        <div class="flex items-center gap-1 text-[18px] font-bold">
            <img src="{{ asset('icons/normal.svg') }}" alt="Normal" class="w-7 h-7">
            {{ $statusCounts['normal'] }}
            <img src="{{ asset('icons/warning.svg') }}" alt="Warning" class="w-7 h-7">
            {{ $statusCounts['warning'] }}
            <img src="{{ asset('icons/poor.svg') }}" alt="Poor" class="w-7 h-7">
            {{ $statusCounts['poor'] }}
        </div>  
    </div>
    <div class="bg-white rounded-xl px-4 py-3.5 shadow-[0_1px_4px_rgba(0,0,0,.07)]">
        <div class="text-[13px] text-slate-900 font-medium mb-2 flex items-center gap-1.5">
            <img src="{{ asset('icons/suhu.svg') }}" alt="Normal" class="w-7 h-7">
            Rerata Suhu
        </div>
        <div><span class="text-[22px] font-bold text-slate-800">{{ number_format($avgTemp ?? 0, 1) }}</span> <span class="text-[22px] font-bold text-slate-800">°C</span></div>
    </div>
    <div class="bg-white rounded-xl px-4 py-3.5 shadow-[0_1px_4px_rgba(0,0,0,.07)]">
        <div class="text-[13px] text-slate-900 font-medium mb-2 flex items-center gap-1.5">
            <img src="{{ asset('icons/kelembapan.svg') }}" alt="Normal" class="w-7 h-7">
            Rerata Kelembaban
        </div>
        <div><span class="text-[22px] font-bold text-slate-800">{{ number_format($avgHumidity ?? 0, 0) }}</span> <span class="text-[22px] font-bold text-slate-800">%</span></div>
    </div>
    <div class="bg-white rounded-xl px-4 py-3.5 shadow-[0_1px_4px_rgba(0,0,0,.07)]">
        <div class="text-[13px] text-slate-900 font-medium mb-2 flex items-center gap-1.5">
            <img src="{{ asset('icons/daya.svg') }}" alt="Normal" class="w-7 h-7">
            Daya Saat Ini
        </div>
        <div><span class="text-[22px] font-bold text-slate-800">{{ number_format($currentPower, 1) }}</span> <span class="text-[22px] font-bold text-slate-800">kW</span></div>
    </div>
    <div class="bg-white rounded-xl px-4 py-3.5 shadow-[0_1px_4px_rgba(0,0,0,.07)]">
        <div class="text-[13px] text-slate-900 font-medium mb-2 flex items-center gap-1.5">
            <img src="{{ asset('icons/energi.svg') }}" alt="Normal" class="w-7 h-7">
            Energi Hari Ini
        </div>
        <div><span class="text-[22px] font-bold text-slate-800">{{ number_format($energyToday, 0) }}</span> <span class="text-[22px] font-bold text-slate-800">kWh</span></div>
    </div>
    <div class="bg-white rounded-xl px-4 py-3.5 shadow-[0_1px_4px_rgba(0,0,0,.07)]">
        <div class="text-[13px] text-slate-900 font-medium mb-2 flex items-center gap-1.5">
            <img src="{{ asset('icons/unit_ac.svg') }}" alt="Normal" class="w-7 h-7">
            Unit AC Aktif
        </div>
        <div><span class="text-[22px] font-bold text-slate-800">{{ $activeAc }}/{{ $totalAc }}</span></div>
    </div>
</div>

<!-- ROOM HOVER TOOLTIP -->
<div id="room-tooltip" class="hidden fixed z-[9999] bg-white rounded-xl shadow-[0_8px_24px_rgba(0,0,0,.18)] px-3.5 py-3 min-w-[170px] pointer-events-none border border-slate-200 font-['Inter']">
    <div id="tt-name" class="text-[14px] font-bold text-slate-800 mb-2"></div>
    <div id="tt-status" class="text-[11px] font-semibold px-2 py-0.5 rounded-full inline-flex items-center gap-1 mb-2.5"></div>
    <div class="flex items-center gap-2 text-[13px] text-slate-700 py-0.5"><span>🌡️</span><span id="tt-temp"></span></div>
    <div class="flex items-center gap-2 text-[13px] text-slate-700 py-0.5"><span>💧</span><span id="tt-hum"></span></div>
    <div class="flex items-center gap-2 text-[13px] text-slate-700 py-0.5"><span>❄️</span><span id="tt-ac"></span></div>
</div>

<!-- MAIN CONTENT -->
<div class="grid grid-cols-[1fr_280px] gap-4">

    <!-- FLOOR PLAN — Fabric.js canvas (read-only, from manajemen denah) -->
    <div class="bg-white rounded-xl p-4 shadow-[0_1px_4px_rgba(0,0,0,.07)]">
        <div class="flex items-center justify-between mb-3">
            <div class="text-[14px] font-semibold text-slate-800">
                🗺️ Denah
                @if($displayFloor)
                    — {{ $displayFloor->building->name ?? '' }} · {{ $displayFloor->name }}
                @endif
            </div>
        </div>

        @if($displayFloor)
            <!-- Canvas rendered from editor canvas_data + room markers -->
            <div class="w-full rounded-lg overflow-hidden bg-slate-100 relative" id="dashCanvasWrapper" style="min-height: 200px;">
                <canvas id="dash-canvas"></canvas>
                <div id="dashCanvasHint" class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 text-[13px]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2 opacity-40 animate-spin" style="animation-duration:2s"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg>
                    Memuat denah...
                </div>

                {{-- Status legend overlay (bottom-left) --}}
                <div class="absolute bottom-3 left-3 bg-white/90 backdrop-blur-sm rounded-xl shadow-sm border border-slate-100 px-5 py-5 z-10">
                    <div class="text-[16px] font-semibold text-slate-600 mb-1.5">Status Ruangan</div>
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-1.5 text-[16px] text-slate-900">
                            <img src="{{ asset('icons/normal.svg') }}" alt="Normal" class="w-5 h-5"> Normal
                        </div>
                        <div class="flex items-center gap-1.5 text-[16px] text-slate-900">
                            <img src="{{ asset('icons/warning.svg') }}" alt="Warning" class="w-5 h-5"> Warning
                        </div>
                        <div class="flex items-center gap-1.5 text-[16px] text-slate-900">
                            <img src="{{ asset('icons/poor.svg') }}" alt="Poor" class="w-5 h-5"> Poor
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
        <div class="bg-white rounded-xl p-[18px] shadow-[0_1px_4px_rgba(0,0,0,.07)]">
            <div class="text-[14px] font-semibold text-slate-800 mb-3.5">Detail Ruangan</div>
            <div id="room-detail-content">
                <div class="text-center py-8 text-slate-400 text-[13px]">
                    <i data-feather="mouse-pointer" class="w-8 h-8 block mx-auto mb-2 opacity-40"></i>
                    Klik marker ruangan pada denah
                </div>
            </div>
        </div>

        <!-- RECENT ALERTS -->
        <div class="bg-white rounded-xl p-[18px] shadow-[0_1px_4px_rgba(0,0,0,.07)]">
            <div class="text-[14px] font-semibold text-slate-800 mb-3.5">Peringatan Terbaru</div>
            @forelse($recentAlerts as $alert)
                @php
                    $alertIcon = match($alert->type) {
                        'sensor_offline' => 'sensor-offline',
                        'high_temp'      => 'suhu-tinggi',
                        'ac_off'         => 'freeze',
                        'high_power'     => 'daya-tinggi',
                        default          => 'alert-triangle',
                    };
                    $alertBg = match($alert->type) {
                        'sensor_offline' => 'bg-slate-100 text-slate-500',
                        'high_temp'      => 'bg-amber-50 text-amber-600',
                        'ac_off'         => 'bg-blue-50 text-blue-500',
                        'high_power'     => 'bg-yellow-50 text-yellow-600',
                        default          => 'bg-slate-100 text-slate-500',
                    };
                @endphp
                <div class="flex items-center gap-2.5 py-2.5 border-b border-slate-50 last:border-0">
                    <!-- <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ $alertBg }}"> -->
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0">
                        <img src="{{ asset('icons/' . $alertIcon . '.svg') }}"
                            onerror="this.src='{{ asset('icons/status.svg') }}'"
                            alt="{{ $alert->type }}" class="w-5 h-5">
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-[13px] font-semibold text-slate-800 truncate">{{ $alert->message }}</div>
                        <div class="text-[11px] text-slate-400">{{ $alert->room->name ?? '-' }}</div>
                    </div>
                    <div class="text-[11px] text-slate-400 whitespace-nowrap">{{ $alert->created_at->format('H:i') }}</div>
                </div>
            @empty
                <div class="text-center text-slate-400 text-[12px] py-4">Tidak ada peringatan</div>
            @endforelse
            <div class="flex items-center justify-end gap-1 text-[12px] text-[#4f7dfc] mt-2.5 cursor-pointer font-medium">
                Lihat Semua
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
        </div>
    </div>
</div>

@php
    // Pre-load all room data for instant detail display
    $floorPlanUrl  = $displayFloor?->plan_url;
    $canvasData    = $displayFloor?->canvas_data;

    // Room markers from the display floor
    $floorRooms = $displayFloor ? $displayFloor->rooms->map(fn($r) => [
        'id'       => $r->id,
        'name'     => $r->name,
        'status'   => $r->status,
        'marker_x' => $r->marker_x ?? 50,
        'marker_y' => $r->marker_y ?? 50,
    ])->values() : collect([]);
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
let _natW = 900, _natH = 560; // natural canvas dimensions from editor

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

    // ── Canvas fills full wrapper width, height follows natural aspect ratio ──
    const wrapperW = wrapper.clientWidth || 900;
    const zoom     = wrapperW / _natW;          // scale based on width only → always full width
    const displayW = wrapperW;
    const displayH = Math.round(_natH * zoom);

    // ── Create canvas at DISPLAY size ──
    dashCanvas = new fabric.Canvas('dash-canvas', {
        width:  displayW,
        height: displayH,
        selection: false,
        backgroundColor: '#f1f5f9',
    });

    // Set wrapper height to match scaled canvas
    wrapper.style.height = displayH + 'px';

    // ── Apply Fabric viewport transform (zoom) ──
    // All objects and background image will render at the scaled size,
    // but their internal coordinates stay at natural size → markers match editor perfectly
    dashCanvas.setViewportTransform([zoom, 0, 0, zoom, 0, 0]);

    // ── Load drawing shapes from canvas_data ──
    if (CANVAS_DATA) {
        // Fix Fabric.js bug: 'alphabetical' → 'alphabetic' (invalid CanvasTextBaseline)
        const cleanJson = CANVAS_DATA.replace(/"textBaseline"\s*:\s*"alphabetical"/g, '"textBaseline":"alphabetic"');
        dashCanvas.loadFromJSON(cleanJson, () => {
            // Drawings are read-only
            dashCanvas.getObjects().forEach(obj => {
                obj.set({ selectable: false, evented: false, hoverCursor: 'default' });
            });
            dashCanvas.renderAll();
            loadBgAndMarkers(zoom);
        });
    } else {
        loadBgAndMarkers(zoom);
    }

    document.getElementById('dashCanvasHint')?.remove();
    feather.replace();
}

function loadBgAndMarkers(zoom) {
    if (FLOOR_PLAN_URL) {
        fabric.Image.fromURL(FLOOR_PLAN_URL, function(img) {
            // Scale image to fill natural canvas size; viewport zoom handles the rest
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
    FLOOR_ROOMS.forEach(room => {
        // Position in NATURAL coordinates — viewport transform scales them to display
        const x = (room.marker_x / 100) * _natW;
        const y = (room.marker_y / 100) * _natH;
        const bg      = DASH_STATUS_BG[room.status]    || DASH_STATUS_BG.normal;
        const iconUrl = DASH_STATUS_ICONS[room.status] || DASH_STATUS_ICONS.normal;

        const square = new fabric.Rect({
            width: 36, height: 36,
            fill: bg, stroke: 'white', strokeWidth: 2,
            rx: 8, ry: 8,
            shadow: new fabric.Shadow({ color: 'rgba(0,0,0,0.20)', blur: 8, offsetX: 0, offsetY: 3 }),
            originX: 'center', originY: 'center',
        });
        const label = new fabric.Text(room.name, {
            fontSize: 11, fill: '#1e293b', fontFamily: 'Inter, sans-serif', fontWeight: '700',
            backgroundColor: 'rgba(255,255,255,0.88)', padding: 2,
            originX: 'center', originY: 'center',
            top: 28,
        });

        fabric.Image.fromURL(iconUrl, function(icon) {
            icon.set({
                scaleX: 24 / icon.width,
                scaleY: 24 / icon.height,
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
        }, { crossOrigin: 'anonymous' });
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

function renderRoomDetail(room) {
    const statusColors = {
        normal:  'bg-green-100 text-green-700',
        warning: 'bg-amber-100 text-amber-700',
        poor:    'bg-red-100 text-red-700',
    };
    const statusLabel = { normal: 'Normal', warning: 'Warning', poor: 'Poor' }[room.status] || room.status;
    const temp = room.temperature ? `${room.temperature.value} ${room.temperature.unit}` : '-';
    const hum  = room.humidity    ? `${room.humidity.value} ${room.humidity.unit}` : '-';
    const co2  = room.co2         ? `${room.co2.value} ${room.co2.unit}` : '-';
    const acColor   = room.ac_status === 'ON' ? 'text-green-500' : 'text-red-500';
    const sensorTxt = room.sensor_connected ? '<span class="text-green-500 font-semibold">Terhubung</span>' : '<span class="text-red-500 font-semibold">Offline</span>';

    document.getElementById('room-detail-content').innerHTML = `
        <div class="flex justify-between items-start mb-1">
            <div class="text-[15px] font-bold text-slate-800">${room.name}</div>
            <span class="text-[11px] px-2 py-0.5 rounded-full font-semibold ${statusColors[room.status] || statusColors.normal}">
                ${room.status === 'warning' ? '▲' : '●'} ${statusLabel}
            </span>
        </div>
        <div class="text-[11px] text-slate-400 flex items-center gap-1.5 mb-3.5">
            <span class="w-2 h-2 rounded-full bg-green-400 inline-block"></span>
            Terakhir diperbarui: ${room.updated_at}
        </div>
        <div class="space-y-0">
            ${detailRow('🌡️ Suhu', temp)}
            ${detailRow('💧 Kelembaban', hum)}
            ${detailRow('🌿 Level CO₂', co2)}
            ${detailRow('❄️ Status AC', `<span class="${acColor} font-bold">${room.ac_status}</span>`)}
            ${detailRow('📡 Sensor', sensorTxt)}
        </div>
        <button class="block w-full py-2.5 bg-slate-800 hover:bg-[#4f7dfc] text-white border-none rounded-lg text-[13px] font-semibold cursor-pointer text-center mt-4 transition-colors">
            Analisa Data
        </button>
    `;
}

function detailRow(label, value) {
    return `
        <div class="flex justify-between items-center py-2 border-b border-slate-50 last:border-0 text-[13px]">
            <span class="text-slate-500">${label}</span>
            <span class="font-semibold text-slate-800">${value}</span>
        </div>`;
}

/* ═══ TOOLTIP ═══ */
function showMarkerTooltip(event, roomId) {
    const room = ROOM_DETAIL_MAP[roomId];
    if (!room) return;
    const statusConfig = {
        normal:  { bg: '#dcfce7', color: '#15803d', icon: '✓ Normal' },
        warning: { bg: '#fef3c7', color: '#b45309', icon: '▲ Warning' },
        poor:    { bg: '#fee2e2', color: '#b91c1c', icon: '● Poor' },
    };
    const cfg = statusConfig[room.status] || statusConfig.normal;
    const temp = room.temperature ? `${room.temperature.value} ${room.temperature.unit}` : '-';
    const hum  = room.humidity    ? `${room.humidity.value} ${room.humidity.unit}` : '-';
    const ac   = room.ac_status || '-';

    document.getElementById('tt-name').textContent = room.name;
    const statusEl = document.getElementById('tt-status');
    statusEl.textContent = cfg.icon;
    statusEl.style.background = cfg.bg;
    statusEl.style.color = cfg.color;
    document.getElementById('tt-temp').textContent = temp;
    document.getElementById('tt-hum').textContent  = hum;
    document.getElementById('tt-ac').textContent   = ac;

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
@endsection
