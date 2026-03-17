@extends('layouts.app')

@section('page-title')
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-500">
        <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
        <line x1="6" y1="20" x2="6" y2="14"/>
    </svg>
    Analisa Data
@endsection

@section('content')
<div class="space-y-5">

    {{-- ── FILTER BAR ─────────────────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('analisa-data.index') }}" id="filterForm">
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm px-5 py-4">
            <div class="flex flex-wrap gap-3 items-end w-full">
                {{-- Ruangan --}}
                <div class="flex flex-col gap-2 flex-1">
                    <label class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Ruangan</label>
                    <select name="room_id" id="sel-room"
                        class="border border-slate-200 rounded-lg px-3 py-[7px] text-[13px] text-slate-700 bg-white focus:outline-none focus:border-blue-400">
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ $room->id == $selectedRoomId ? 'selected' : '' }}>
                                {{ $room->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Parameter --}}
                <div class="flex flex-col gap-2 flex-1">
                    <label class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Parameter</label>
                    <select name="parameter" id="sel-parameter"
                        class="border border-slate-200 rounded-lg px-3 py-[7px] text-[13px] text-slate-700 bg-white focus:outline-none focus:border-blue-400">
                        @foreach($parameterLabels as $key => $label)
                            <option value="{{ $key }}" {{ $parameter === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Periode --}}
                <div class="flex flex-col gap-2 flex-1">
                    <label class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Periode</label>
                    <select name="periode" id="sel-periode"
                        class="border border-slate-200 rounded-lg px-3 py-[7px] text-[13px] text-slate-700 bg-white focus:outline-none focus:border-blue-400">
                        <option value="harian"   {{ $periode === 'harian'   ? 'selected' : '' }}>Harian</option>
                        <option value="mingguan" {{ $periode === 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                        <option value="bulanan"  {{ $periode === 'bulanan'  ? 'selected' : '' }}>Bulanan</option>
                    </select>
                </div>

                {{-- Tanggal --}}
                <div class="flex flex-col gap-2 flex-1">
                    <label class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Tanggal</label>
                    <input type="date" name="tanggal" id="inp-tanggal" value="{{ $tanggal }}"
                        class="border border-slate-200 rounded-lg px-3 py-[7px] text-[13px] text-slate-700 bg-white focus:outline-none focus:border-blue-400 w-full">
                </div>

                {{-- Buttons --}}
                <div class="flex gap-2 pb-[1px] ml-auto shrink-0">
                    <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white text-[13px] font-semibold px-5 py-[8px] rounded-lg transition-colors cursor-pointer whitespace-nowrap">
                        Terapkan
                    </button>
                    <a href="{{ route('analisa-data.index') }}"
                        class="bg-white hover:bg-slate-50 text-slate-700 text-[13px] font-semibold px-5 py-[8px] rounded-lg border border-slate-200 transition-colors no-underline whitespace-nowrap">
                        Reset
                    </a>
                </div>
            </div>
        </div>
    </form>

    {{-- ── MAIN ROW: Chart + Side Panel ───────────────────────────────────── --}}
    @php
        $paramLabel = $parameterLabels[$parameter];
        $unit       = $parameterUnits[$parameter];
        $th         = $thresholds[$parameter] ?? $thresholds['temperature'];

        $chartLabels = $chartData->pluck('label')->toJson();
        $chartValues = $chartData->pluck('value')->map(fn($v) => round((float)$v, 2))->toJson();

        // Warna per parameter
        $colorMap = [
            'temperature' => ['line' => '#ef4444', 'fill' => 'rgba(239,68,68,0.12)'],
            'humidity'    => ['line' => '#3b82f6', 'fill' => 'rgba(59,130,246,0.12)'],
            'energy'      => ['line' => '#f59e0b', 'fill' => 'rgba(245,158,11,0.12)'],
            'power'       => ['line' => '#8b5cf6', 'fill' => 'rgba(139,92,246,0.12)'],
        ];
        $color = $colorMap[$parameter] ?? $colorMap['temperature'];
    @endphp

    <div class="flex flex-col xl:flex-row gap-5">

        {{-- Chart --}}
        <div class="flex-1 bg-white rounded-xl border border-slate-100 shadow-sm p-5">
            <div class="text-center mb-1">
                <div class="text-[20px] font-semibold text-slate-800">
                    Grafik {{ $paramLabel }} Ruangan {{ $selectedRoom?->name ?? '-' }}
                </div>
                <div class="text-[12px] text-slate-400">{{ $date->translatedFormat('d F Y') }}</div>
            </div>
            <div class="relative" style="height: 320px">
                <canvas id="analysisChart"></canvas>
            </div>
            <div class="flex justify-center mt-3 gap-1.5 items-center">
                <span class="w-6 h-0.5 inline-block rounded" style="background:{{ $color['line'] }}"></span>
                <span class="text-[12px] text-slate-500">{{ $paramLabel }} Ruangan</span>
            </div>
        </div>

        {{-- Side Panel --}}
        <div class="w-full xl:w-[395px] flex flex-col gap-4 shrink-0">

            {{-- Batas Normal --}}
            <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4">
                <div class="text-[13px] font-semibold text-slate-700 mb-3">Batas Normal</div>
                <div class="space-y-2">
                    <div class="flex items-start gap-2">
                        <img src="{{ asset('icons/normal.svg') }}" alt="Normal" class="w-5 h-5">
                        <div class="text-[12px] text-slate-700">
                            <span class="font-semibold">Normal</span>
                            <span class="text-slate-600 ml-1">: {{ $th['normal_min'] }} – {{ $th['normal_max'] }}{{ $unit }}</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-2">
                        <img src="{{ asset('icons/warning.svg') }}" alt="Warning" class="w-5 h-5">
                        <div class="text-[12px] text-slate-700">
                            <span class="font-semibold">Warning</span>
                            <span class="text-slate-600 ml-1">: &lt;{{ $th['normal_min'] }}{{ $unit }} atau &gt;{{ $th['normal_max'] }}{{ $unit }}</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-2">
                        <img src="{{ asset('icons/poor.svg') }}" alt="Poor" class="w-5 h-5">
                        <div class="text-[12px] text-slate-700">
                            <span class="font-semibold">Poor</span>
                            <span class="text-slate-600 ml-1">: &lt;{{ $th['warn_lower'] }}{{ $unit }} atau &gt;{{ $th['warn_upper'] }}{{ $unit }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Peringatan Terkait --}}
            <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 flex-1">
                <div class="text-[13px] font-semibold text-slate-700 mb-3">Peringatan Terkait</div>
                @if($alerts->isEmpty())
                    <div class="text-[12px] text-slate-400 text-center py-4">Tidak ada peringatan</div>
                @else
                    <div class="space-y-2.5">
                        @foreach($alerts as $alert)
                            @php
                                $iconMap = [
                                    'sensor_offline' => '📡',
                                    'high_temp'      => '🌡️',
                                    'high_humidity'  => '💧',
                                    'ac_off'         => '❄️',
                                    'high_power'     => '⚡',
                                ];
                                $icon = $iconMap[$alert->type] ?? '⚠️';
                            @endphp
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="text-[14px] shrink-0">{{ $icon }}</span>
                                    <span class="text-[12px] text-slate-700 truncate">{{ $alert->message ?? $alert->type }}</span>
                                </div>
                                <span class="text-[10px] text-slate-400 shrink-0 whitespace-nowrap">
                                    {{ optional($alert->created_at)->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── STAT CARDS ──────────────────────────────────────────────────────── --}}
    @php
        // Icon SVG dari public/icons sesuai parameter
        $iconMap = [
            'temperature' => asset('icons/suhu.svg'),
            'humidity'    => asset('icons/kelembapan.svg'),
            'energy'      => asset('icons/energi.svg'),
            'power'       => asset('icons/daya.svg'),
            'co2'         => asset('icons/energi.svg'), // pakai energi.svg untuk CO2
        ];
        $statIcon = $iconMap[$parameter] ?? asset('icons/suhu.svg');

        $statCards = [
            ['icon' => $statIcon, 'label' => $paramLabel . ' Terbaru',  'value' => ($latest  !== null ? number_format((float)$latest,  1) : '–') . ' ' . $unit],
            ['icon' => $statIcon, 'label' => 'Rerata ' . $paramLabel,   'value' => ($average !== null ? $average : '–') . ' ' . $unit],
            ['icon' => $statIcon, 'label' => $paramLabel . ' Tertinggi', 'value' => ($max     !== null ? $max     : '–') . ' ' . $unit],
            ['icon' => $statIcon, 'label' => $paramLabel . ' Terendah',  'value' => ($min     !== null ? $min     : '–') . ' ' . $unit],
        ];
    @endphp

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($statCards as $card)
            <div class="bg-white rounded-xl border border-slate-100 shadow-sm px-5 py-4 flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <img src="{{ $card['icon'] }}" alt="icon" class="w-6 h-6 shrink-0">
                    <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide leading-tight">{{ $card['label'] }}</div>
                </div>
                <div class="text-[22px] font-bold text-slate-800 leading-tight">{{ $card['value'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- ── TABEL DATA ──────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100">
            <span class="text-[14px] font-semibold text-slate-800">Tabel Data</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-[13px]">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Waktu</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Nilai</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tableData as $row)
                        <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3 text-slate-600">{{ $row['waktu'] }}</td>
                            <td class="px-5 py-3 text-slate-700 font-medium">
                                {{ $row['nilai'] }} {{ $unit }}
                            </td>
                            <td class="px-5 py-3">
                                @if($row['status'] === 'normal')
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-green-600 bg-green-50 rounded-full px-2.5 py-0.5">
                                        <img src="{{ asset('icons/normal.svg') }}" alt="Normal" class="w-5 h-5"> Normal
                                    </span>
                                @elseif($row['status'] === 'warning')
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-600 bg-orange-50 rounded-full px-2.5 py-0.5">
                                        <img src="{{ asset('icons/warning.svg') }}" alt="Warning" class="w-5 h-5"> Warning
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-red-600 bg-red-50 rounded-full px-2.5 py-0.5">
                                        <img src="{{ asset('icons/poor.svg') }}" alt="Poor" class="w-5 h-5"> Poor
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-10 text-center text-slate-400 text-[13px]">
                                Tidak ada data untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    const labels     = @json($chartData->pluck('label'));
    const values     = @json($chartData->pluck('value')->map(fn($v) => round((float)$v, 2)));
    const lineColor  = '{{ $color['line'] }}';
    const fillColor  = '{{ $color['fill'] }}';
    const unit       = '{{ $unit }}';
    const paramLabel = '{{ $paramLabel }}';

    // Icon URL dari public/icons (di-pass dari PHP)
    const icons = {
        normal:  '{{ asset('icons/normal.svg') }}',
        warning: '{{ asset('icons/warning.svg') }}',
        poor:    '{{ asset('icons/poor.svg') }}',
    };

    // Threshold dari PHP
    const th = {
        normalMin: {{ $th['normal_min'] }},
        normalMax: {{ $th['normal_max'] }},
        warnLower: {{ $th['warn_lower'] }},
        warnUpper: {{ $th['warn_upper'] }},
    };

    function getStatus(val) {
        if (val < th.warnLower || val > th.warnUpper) return { label: 'Poor',    icon: icons.poor,    color: '#ef4444' };
        if (val < th.normalMin || val > th.normalMax) return { label: 'Warning',  icon: icons.warning, color: '#f59e0b' };
        return                                               { label: 'Normal',   icon: icons.normal,  color: '#22c55e' };
    }

    // ── Custom HTML Tooltip ──────────────────────────────────────────────────
    function customTooltip(context) {
        let tooltipEl = document.getElementById('chart-tooltip');
        if (!tooltipEl) {
            tooltipEl = document.createElement('div');
            tooltipEl.id = 'chart-tooltip';
            tooltipEl.style.cssText = `
                position: absolute; pointer-events: none; transition: opacity .15s;
                background: #fff; border: 1px solid #e2e8f0; border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.10); padding: 8px 12px;
                font-family: Inter, sans-serif; font-size: 12px; color: #1e293b;
                min-width: 110px; z-index: 50;
            `;
            document.getElementById('analysisChart').parentElement.appendChild(tooltipEl);
        }

        const tooltip = context.tooltip;
        if (tooltip.opacity === 0) { tooltipEl.style.opacity = '0'; return; }

        const val  = tooltip.dataPoints?.[0]?.parsed?.y;
        const lbl  = tooltip.dataPoints?.[0]?.label ?? '';
        const st   = getStatus(val);

        tooltipEl.innerHTML = `
            <div style="font-weight:700;font-size:13px;color:#0f172a;margin-bottom:6px;text-align:center">${lbl}</div>
            <div style="display:flex;align-items:center;margin-bottom:3px;color:#475569">
                <span style="display:inline-block;width:13px;height:13px;border-radius:50%;background:${st.color};margin-right:6px;flex-shrink:0"></span>
                ${val} ${unit}
            </div>
            <div style="display:flex;align-items:center;color:#475569">
                <img src="${st.icon}" style="width:16px;height:16px;margin-right:6px;flex-shrink:0">
                <span>${st.label}</span>
            </div>
        `;

        tooltipEl.style.opacity = '1';

        const { offsetLeft: posX, offsetTop: posY } = context.chart.canvas;
        let left = posX + tooltip.caretX + 10;
        let top  = posY + tooltip.caretY - 10;
        tooltipEl.style.left = left + 'px';
        tooltipEl.style.top  = top  + 'px';
    }

    const ctx = document.getElementById('analysisChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: paramLabel + ' Ruangan',
                data: values,
                borderColor: lineColor,
                backgroundColor: fillColor,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#fff',
                pointBorderColor: lineColor,
                pointBorderWidth: 1.5,
                pointRadius: 3,
                pointHoverRadius: 5,
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    enabled: false,          // matikan tooltip default
                    external: customTooltip, // pakai custom HTML
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: { color: '#94a3b8', font: { size: 11 } }
                },
                y: {
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: {
                        color: '#94a3b8',
                        font: { size: 11 },
                        callback: (v) => v + unit,
                    }
                }
            }
        }
    });
})();
</script>


@endsection
