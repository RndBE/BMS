@extends('layouts.app')

@section('content')
<!-- SUMMARY CARDS -->
<div class="summary-grid">
    {{-- Status Ruangan --}}
    <div class="sum-card">
        <div class="sum-card-label" style="color:#64748b;">
            <i data-feather="home" style="color:#4f7dfc"></i> Status Ruangan
        </div>
        <div class="status-badges">
            <span class="badge-n">●&nbsp;{{ $statusCounts['normal'] }}</span>
            <span class="badge-w">▲&nbsp;{{ $statusCounts['warning'] }}</span>
            <span class="badge-p">●&nbsp;{{ $statusCounts['poor'] }}</span>
        </div>
    </div>
    {{-- Rerata Suhu --}}
    <div class="sum-card">
        <div class="sum-card-label"><i data-feather="thermometer" style="color:#f59e0b"></i> Rerata Suhu</div>
        <div><span class="sum-value">{{ number_format($avgTemp, 1) }}</span> <span class="sum-unit">°C</span></div>
    </div>
    {{-- Rerata Kelembaban --}}
    <div class="sum-card">
        <div class="sum-card-label"><i data-feather="droplet" style="color:#22c55e"></i> Rerata Kelembaban</div>
        <div><span class="sum-value">{{ number_format($avgHumidity, 0) }}</span> <span class="sum-unit">%</span></div>
    </div>
    {{-- Daya Saat Ini --}}
    <div class="sum-card">
        <div class="sum-card-label"><i data-feather="zap" style="color:#f59e0b"></i> Daya Saat Ini</div>
        <div><span class="sum-value">{{ number_format($currentPower, 1) }}</span> <span class="sum-unit">kW</span></div>
    </div>
    {{-- Energi Hari Ini --}}
    <div class="sum-card">
        <div class="sum-card-label"><i data-feather="battery-charging" style="color:#4f7dfc"></i> Energi Hari Ini</div>
        <div><span class="sum-value">{{ number_format($energyToday, 0) }}</span> <span class="sum-unit">kWh</span></div>
    </div>
    {{-- Unit AC Aktif --}}
    <div class="sum-card">
        <div class="sum-card-label"><i data-feather="wind" style="color:#22c55e"></i> Unit AC Aktif</div>
        <div><span class="sum-value">{{ $activeAc }}/{{ $totalAc }}</span></div>
    </div>
</div>

<!-- HOVER TOOLTIP -->
<div id="room-tooltip" style="
    display:none;
    position:fixed;
    z-index:9999;
    background:white;
    border-radius:10px;
    box-shadow:0 8px 24px rgba(0,0,0,0.18);
    padding:12px 15px;
    min-width:170px;
    pointer-events:none;
    border:1px solid #e2e8f0;
    font-family:'Inter',sans-serif;
">
    <div id="tt-name" style="font-size:14px;font-weight:700;color:#1e293b;margin-bottom:8px;"></div>
    <div id="tt-status" style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:20px;display:inline-flex;align-items:center;gap:4px;margin-bottom:10px;"></div>
    <div class="tt-row" style="display:flex;align-items:center;gap:8px;font-size:13px;color:#334155;padding:3px 0;">
        <span style="font-size:14px;">🌡️</span>
        <span id="tt-temp"></span>
    </div>
    <div class="tt-row" style="display:flex;align-items:center;gap:8px;font-size:13px;color:#334155;padding:3px 0;">
        <span style="font-size:14px;">💧</span>
        <span id="tt-hum"></span>
    </div>
    <div class="tt-row" style="display:flex;align-items:center;gap:8px;font-size:13px;color:#334155;padding:3px 0;">
        <span style="font-size:14px;">❄️</span>
        <span id="tt-ac"></span>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-grid">
    <!-- FLOOR PLAN -->
    <div class="floor-plan-card">
        <div class="section-title">Denah Kantor</div>
        <div class="svg-container">
            <svg class="floorplan" viewBox="0 0 800 530" xmlns="http://www.w3.org/2000/svg" id="floorplan-svg">
                <!-- Background -->
                <rect width="800" height="530" fill="#f8fafc"/>

                <!-- Arrow decorations (corridors) -->
                <defs>
                    <marker id="arrowhead" markerWidth="6" markerHeight="4" refX="3" refY="2" orient="auto">
                        <polygon points="0 0, 6 2, 0 4" fill="#94a3b8"/>
                    </marker>
                </defs>
                <line x1="170" y1="380" x2="170" y2="440" stroke="#94a3b8" stroke-width="1" marker-end="url(#arrowhead)" stroke-dasharray="4,2"/>
                <line x1="340" y1="380" x2="340" y2="440" stroke="#94a3b8" stroke-width="1" marker-end="url(#arrowhead)" stroke-dasharray="4,2"/>

                @foreach($rooms as $room)
                    @php
                        $statusClass = 'status-' . $room->status;
                        $cx = $room->svg_x + $room->svg_width / 2;
                        $cy = $room->svg_y + $room->svg_height / 2;
                        $iconColor = match($room->status) {
                            'warning' => '#f59e0b',
                            'poor'    => '#ef4444',
                            default   => '#22c55e',
                        };
                        $shortName = strlen($room->name) > 10 ? wordwrap($room->name, 10, "\n", true) : $room->name;
                        // Pre-load latest sensor readings for tooltip
                        $readings = $room->getLatestReadings();
                        $tempVal  = isset($readings['temperature']) ? number_format($readings['temperature']['value'], 1) . $readings['temperature']['unit'] : '-';
                        $humVal   = isset($readings['humidity'])    ? number_format($readings['humidity']['value'], 0) . $readings['humidity']['unit']       : '-';
                        $ac       = $room->acUnits->first();
                        $acVal    = $ac ? ($ac->is_active ? 'ON' : 'OFF') : '-';
                    @endphp
                    <g class="room-group"
                        data-room-id="{{ $room->id }}"
                        data-name="{{ $room->name }}"
                        data-status="{{ $room->status }}"
                        data-temp="{{ $tempVal }}"
                        data-hum="{{ $humVal }}"
                        data-ac="{{ $acVal }}"
                        onmouseenter="showTooltip(event, this)"
                        onmousemove="moveTooltip(event)"
                        onmouseleave="hideTooltip()"
                        onclick="selectRoom({{ $room->id }})"
                    >
                        <rect
                            id="room-{{ $room->id }}"
                            class="room-rect {{ $statusClass }}"
                            x="{{ $room->svg_x }}"
                            y="{{ $room->svg_y }}"
                            width="{{ $room->svg_width }}"
                            height="{{ $room->svg_height }}"
                            rx="4"
                        />
                        {{-- Status icon --}}
                        <circle cx="{{ $cx }}" cy="{{ $cy - 14 }}" r="9" fill="{{ $iconColor }}" opacity="0.9"/>
                        <text x="{{ $cx }}" y="{{ $cy - 10 }}" text-anchor="middle" font-size="9" fill="white" style="pointer-events:none;">
                            {{ $room->status === 'warning' ? '!' : ($room->status === 'poor' ? '✕' : '✓') }}
                        </text>
                        {{-- Room name --}}
                        @foreach(explode("\n", $shortName) as $lineIdx => $line)
                            <text
                                class="room-label"
                                x="{{ $cx }}"
                                y="{{ $cy + 4 + ($lineIdx * 10) }}"
                                text-anchor="middle"
                            >{{ $line }}</text>
                        @endforeach
                    </g>
                @endforeach
            </svg>
        </div>
        <div class="legend">
            <div class="legend-item"><span class="legend-dot n"></span> Normal</div>
            <div class="legend-item"><span class="legend-dot w"></span> Warning</div>
            <div class="legend-item"><span class="legend-dot p"></span> Poor</div>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="right-panel">
        <!-- ROOM DETAIL -->
        <div class="detail-card">
            <div class="section-title">Detail Ruangan</div>
            <div id="room-detail-content">
                <div class="detail-placeholder">
                    <i data-feather="mouse-pointer"></i>
                    Klik ruangan pada denah untuk melihat detail
                </div>
            </div>
        </div>

        <!-- RECENT ALERTS -->
        <div class="alerts-card">
            <div class="section-title">Peringatan Terbaru</div>
            @foreach($recentAlerts as $alert)
                <div class="alert-item">
                    <div class="alert-icon {{ $alert->type }}">
                        @php
                            $alertIcon = match($alert->type) {
                                'sensor_offline' => 'wifi-off',
                                'high_temp'      => 'thermometer',
                                'ac_off'         => 'wind',
                                'high_power'     => 'zap',
                                default          => 'alert-triangle',
                            };
                        @endphp
                        <i data-feather="{{ $alertIcon }}"></i>
                    </div>
                    <div class="alert-text">
                        <div class="alert-msg">{{ $alert->message }}</div>
                        <div class="alert-room">{{ $alert->room->name }}</div>
                    </div>
                    <div class="alert-time">{{ $alert->created_at->format('H:i') }}</div>
                </div>
            @endforeach
            <div class="see-all">
                Lihat Semua <i data-feather="arrow-right"></i>
            </div>
        </div>
    </div>
</div>

<script>
const tooltip = document.getElementById('room-tooltip');

function showTooltip(event, el) {
    const name   = el.dataset.name;
    const status = el.dataset.status;
    const temp   = el.dataset.temp;
    const hum    = el.dataset.hum;
    const ac     = el.dataset.ac;

    const statusConfig = {
        normal:  { label: 'Normal',  bg: '#dcfce7', color: '#15803d', icon: '✓' },
        warning: { label: 'Warning', bg: '#fef3c7', color: '#b45309', icon: '▲' },
        poor:    { label: 'Poor',    bg: '#fee2e2', color: '#b91c1c', icon: '●' },
    };
    const cfg = statusConfig[status] || statusConfig.normal;

    document.getElementById('tt-name').textContent   = name;
    document.getElementById('tt-status').textContent = cfg.icon + ' ' + cfg.label;
    document.getElementById('tt-status').style.background = cfg.bg;
    document.getElementById('tt-status').style.color      = cfg.color;
    document.getElementById('tt-temp').textContent   = temp;
    document.getElementById('tt-hum').textContent    = hum;
    document.getElementById('tt-ac').textContent     = ac;

    tooltip.style.display = 'block';
    moveTooltip(event);
}

function moveTooltip(event) {
    const offset = 14;
    let x = event.clientX + offset;
    let y = event.clientY + offset;
    // Keep tooltip inside viewport
    if (x + 190 > window.innerWidth)  x = event.clientX - 190;
    if (y + 160 > window.innerHeight) y = event.clientY - 160;
    tooltip.style.left = x + 'px';
    tooltip.style.top  = y + 'px';
}

function hideTooltip() {
    tooltip.style.display = 'none';
}

let selectedRoomId = null;

function selectRoom(roomId) {
    // Deselect previous
    document.querySelectorAll('.room-rect').forEach(r => r.classList.remove('selected'));

    // Select new
    const rect = document.getElementById('room-' + roomId);
    if (rect) rect.classList.add('selected');

    selectedRoomId = roomId;

    // Show loading
    document.getElementById('room-detail-content').innerHTML = `
        <div class="detail-placeholder">
            <i data-feather="loader"></i>
            Memuat data...
        </div>
    `;
    feather.replace();

    // Fetch room detail
    fetch('/api/rooms/' + roomId, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(res => res.json())
    .then(room => {
        const statusLabel = { normal: 'Normal', warning: 'Warning', poor: 'Poor' }[room.status] || room.status;
        const acStatus = room.ac_status === 'ON'
            ? '<span style="color:#22c55e;font-weight:700;">ON</span>'
            : '<span style="color:#ef4444;font-weight:700;">OFF</span>';
        const sensorStatus = room.sensor_connected
            ? '<span class="detail-row-value connected">Terhubung</span>'
            : '<span class="detail-row-value disconnected">Offline</span>';
        const temp = room.temperature ? room.temperature.value + ' ' + room.temperature.unit : '-';
        const hum  = room.humidity    ? room.humidity.value + ' ' + room.humidity.unit    : '-';
        const co2  = room.co2         ? room.co2.value + ' ' + room.co2.unit              : '-';

        document.getElementById('room-detail-content').innerHTML = `
            <div class="detail-header">
                <div class="detail-room-name">${room.name}</div>
                <span class="detail-status ${room.status}">▲ ${statusLabel}</span>
            </div>
            <div class="detail-updated">
                <i data-feather="circle"></i>
                Terakhir diperbarui: ${room.updated_at}
            </div>
            <div class="detail-row">
                <span class="detail-row-label">Suhu</span>
                <span class="detail-row-value">${temp}</span>
            </div>
            <div class="detail-row">
                <span class="detail-row-label">Kelembaban</span>
                <span class="detail-row-value">${hum}</span>
            </div>
            <div class="detail-row">
                <span class="detail-row-label">Level CO₂</span>
                <span class="detail-row-value">${co2}</span>
            </div>
            <div class="detail-row">
                <span class="detail-row-label">Status AC</span>
                <span class="detail-row-value">${acStatus}</span>
            </div>
            <div class="detail-row">
                <span class="detail-row-label">Status Sensor</span>
                ${sensorStatus}
            </div>
            <button class="btn-analisa">Analisa Data</button>
        `;
        feather.replace();
    })
    .catch(() => {
        document.getElementById('room-detail-content').innerHTML = `
            <div class="detail-placeholder">Gagal memuat data ruangan.</div>
        `;
    });
}
</script>
@endsection
