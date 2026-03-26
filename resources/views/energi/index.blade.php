@extends('layouts.app')

@section('page-title', 'Energi')

@section('content')



{{-- ── STAT CARDS ───────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-5 gap-4 mb-5">
    @foreach($statCardData as $card)
        <div class="bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-xl border border-slate-100 shadow-sm px-5 py-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                 style="background-color: {{ $card['bg'] }}; border: 1.5px solid {{ $card['bg'] }}">
                <div style="
                    width: 24px; height: 24px;
                    background-color: {{ $card['iconColor'] }};
                    -webkit-mask-image: url('{{ asset('icons/' . $card['icon']) }}');
                    mask-image: url('{{ asset('icons/' . $card['icon']) }}');
                    -webkit-mask-size: contain;
                    mask-size: contain;
                    -webkit-mask-repeat: no-repeat;
                    mask-repeat: no-repeat;
                    -webkit-mask-position: center;
                    mask-position: center;
                "></div>
            </div>
            <div class="min-w-0">
                <div class="text-[11px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide truncate">
                    {{ $card['label'] }}
                </div>
                <div class="text-[18px] font-bold text-slate-800 dark:text-white leading-tight">
                    {{ $card['value'] }}
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- ── MAIN LAYOUT: Chart + Side Panel ─────────────────────────────────────── --}}
<div class="flex gap-4 mb-5">

    {{-- Chart Card --}}
    <div class="flex-1 bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-5 pt-5 pb-3">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[18px] font-bold text-slate-800 dark:text-white">Grafik {{ $paramLabel }} Gedung</div>
                    <div class="text-[12px] text-slate-400 mt-0.5">{{ $date->translatedFormat('d F Y') }}</div>
                </div>
                {{-- Dropdown inline Parameter + Periode — auto-submit --}}
                <div class="flex items-center gap-2">
                    {{-- Parameter --}}
                    <div class="relative">
                        <select id="chart-parameter"
                            class="appearance-none border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 bg-gray-100 rounded-full pl-3 pr-7 py-1.5 text-[12px] font-medium text-slate-700 focus:outline-none cursor-pointer hover:border-gray-300 transition-colors">
                            <option value="power"   {{ $parameter === 'power'   ? 'selected' : '' }}>Daya</option>
                            <option value="voltage" {{ $parameter === 'voltage' ? 'selected' : '' }}>Tegangan</option>
                        </select>
                    </div>
                    {{-- Periode --}}
                    <div class="relative">
                        <select id="chart-periode"
                            class="appearance-none border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 bg-gray-100 rounded-full pl-3 pr-7 py-1.5 text-[12px] font-medium text-slate-700 focus:outline-none cursor-pointer hover:border-gray-300 transition-colors">
                            <option value="harian"   {{ $periode === 'harian'   ? 'selected' : '' }}>Harian</option>
                            <option value="mingguan" {{ $periode === 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                            <option value="bulanan"  {{ $periode === 'bulanan'  ? 'selected' : '' }}>Bulanan</option>
                            <option value="tahunan"  {{ $periode === 'tahunan'  ? 'selected' : '' }}>Tahunan</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-4 pb-4" style="height:340px; position:relative;">
            <canvas id="energyChart"></canvas>
        </div>
        <div class="flex justify-center items-center gap-1.5 mt-2 pb-4">
            <span class="w-6 h-0.5 inline-block rounded bg-red-500"></span>
            <span class="text-[12px] text-slate-500 dark:text-slate-400">{{ $paramLabel }} Gedung</span>
        </div>
    </div>

    {{-- Side Panel --}}
    <div class="w-[270px] flex flex-col gap-4 shrink-0 overflow-hidden">

        {{-- Batas Normal --}}
        <div class="bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-xl border border-slate-100 shadow-sm px-5 py-4">
            <div class="text-[13px] font-semibold text-slate-800 dark:text-white mb-3">Batas Normal</div>
            @if($alertLimit)
            <div class="flex flex-col gap-2.5">
                {{-- Normal --}}
                <div class="flex items-start gap-2">
                    <img src="{{ asset('icons/normal.svg') }}" class="w-5 h-5 mt-0.5 shrink-0">
                    <div class="text-[12px]">
                        <span class="font-semibold text-slate-700 dark:text-slate-300">Normal</span>
                        <span class="text-slate-500 dark:text-slate-400 ml-1">
                            : 
                            @if($alertLimit->normal_min !== null)
                                    &lt;{{ $alertLimit->normal_min }}{{ $unit }}
                                @endif
                                @if($alertLimit->normal_min !== null && $alertLimit->normal_max !== null)
                                    atau
                                @endif
                                @if($alertLimit->normal_max !== null)
                                    &gt;{{ $alertLimit->normal_max }}{{ $unit }}
                            @endif
                        </span>
                    </div>
                </div>
                {{-- Warning (rendah + tinggi digabung) --}}
                <div class="flex items-start gap-2">
                    <img src="{{ asset('icons/warning.svg') }}" class="w-5 h-5 mt-0.5 shrink-0">
                    <div class="text-[12px]">
                        <span class="font-semibold text-slate-700 dark:text-slate-300">Warning</span>
                        <span class="text-slate-500 dark:text-slate-400 ml-1">:
                            @if($alertLimit->warn_low_min !== null)
                                {{ $alertLimit->warn_low_min }}{{ $unit }} – {{ $alertLimit->warn_low_max }}{{ $unit }}
                            @endif
                            @if($alertLimit->warn_low_min !== null && $alertLimit->warn_high_min !== null)
                                atau
                            @endif
                            @if($alertLimit->warn_high_min !== null)
                                {{ $alertLimit->warn_high_min }}{{ $unit }} – {{ $alertLimit->warn_high_max }}{{ $unit }}
                            @endif
                        </span>
                    </div>
                </div>
                {{-- Poor --}}
                @if($alertLimit->poor_low !== null || $alertLimit->poor_high !== null)
                <div class="flex items-start gap-2">
                    <img src="{{ asset('icons/poor.svg') }}" class="w-5 h-5 mt-0.5 shrink-0">
                    <div class="text-[12px]">
                        <span class="font-semibold text-slate-700 dark:text-slate-300">Poor</span>
                        <span class="text-slate-500 dark:text-slate-400 ml-1">:
                            @if($alertLimit->poor_low !== null)
                                &lt;{{ $alertLimit->poor_low }}{{ $unit }}
                            @endif
                            @if($alertLimit->poor_low !== null && $alertLimit->poor_high !== null)
                                atau
                            @endif
                            @if($alertLimit->poor_high !== null)
                                &gt;{{ $alertLimit->poor_high }}{{ $unit }}
                            @endif
                        </span>
                    </div>
                </div>
                @endif
            </div>
            @else
            <div class="text-[12px] text-slate-400 text-center py-3">Belum dikonfigurasi</div>
            @endif
        </div>

        {{-- Peringatan Terkait --}}
        <div class="bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-xl border border-slate-100 shadow-sm flex-1 flex flex-col overflow-hidden">
            <div class="px-5 pt-4 pb-2 border-b border-slate-50 dark:border-[#2d2d2d] shrink-0">
                <div class="text-[13px] font-semibold text-slate-800 dark:text-white">Peringatan Terkait</div>
            </div>
            <div class="overflow-y-auto flex-1 min-h-0 px-5">
                @forelse($alerts as $alert)
                    @php
                        $iconTypeMap = [
                            'high_temp'     => 'suhu-tinggi',
                            'low_temp'      => 'suhu',
                            'high_humidity' => 'kelembapan',
                            'low_humidity'  => 'kelembapan',
                            'co2_tinggi'    => 'co2',
                            'co2_rendah'    => 'co2',
                            'high_power'    => 'daya-tinggi',
                            'low_power'     => 'daya',
                            'high_voltage'  => 'tegangan',
                            'low_voltage'   => 'tegangan',
                            'sensor_offline'=> 'sensor-offline',
                            'ac_off'        => 'freeze',
                            'critical'      => 'poor',
                            'warning'       => 'warning',
                        ];
                        $alertIcon = $iconTypeMap[$alert->type] ?? 'warning';
                    @endphp
                    <div class="flex items-center gap-2.5 py-2 border-b border-slate-50 dark:border-[#2d2d2d] last:border-0">
                        <img src="{{ asset('icons/' . $alertIcon . '.svg') }}"
                            onerror="this.src='{{ asset('icons/status.svg') }}'"
                             class="w-5 h-5 shrink-0">
                        <div class="flex-1 min-w-0">
                            <div class="text-[12px] font-semibold text-slate-800 dark:text-slate-200 truncate">{{ $alert->message }}</div>
                            <div class="text-[11px] text-slate-400">{{ $alert->room->name ?? '-' }}</div>
                        </div>
                        <div class="text-[11px] text-slate-400 whitespace-nowrap">
                            {{ $alert->created_at->format('d/m H:i') }}
                        </div>
                    </div>
                @empty
                    <div class="text-center text-slate-400 text-[12px] py-4">Tidak ada peringatan</div>
                @endforelse
            </div>
        </div>

    </div>
</div>

{{-- ── TABEL DATA ───────────────────────────────────────────────────────────── --}}
<div class="bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-xl border border-slate-100 shadow-sm">
    <div class="px-5 py-4 border-b border-slate-100 dark:border-[#2d2d2d]">
        <span class="text-[16px] font-bold text-slate-800 dark:text-white">Tabel Data</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-[13px]">
            <thead>
                <tr class="bg-slate-50 dark:bg-[#1e1e1e] border-b border-slate-100 dark:border-[#2d2d2d]">
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide w-1/3">Waktu</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide w-1/3">Nilai</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tableData as $row)
                    <tr class="border-b border-slate-50 dark:border-[#2d2d2d] hover:bg-slate-50 dark:hover:bg-[#2a2a2a] transition-colors">
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $row['waktu'] }}</td>
                        <td class="px-5 py-3 text-slate-700 dark:text-slate-200 font-medium">{{ $row['nilai'] }} {{ $unit }}</td>
                        <td class="px-5 py-3">
                            @if($row['status'] === 'normal')
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-green-600 bg-green-50 rounded-full px-2.5 py-0.5">
                                    <img src="{{ asset('icons/normal.svg') }}" class="w-4 h-4"> Normal
                                </span>
                            @elseif($row['status'] === 'warning')
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-600 bg-orange-50 rounded-full px-2.5 py-0.5">
                                    <img src="{{ asset('icons/warning.svg') }}" class="w-4 h-4"> Warning
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-red-600 bg-red-50 rounded-full px-2.5 py-0.5">
                                    <img src="{{ asset('icons/poor.svg') }}" class="w-4 h-4"> Poor
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

@endsection

@push('scripts')

<script>
// ── Dropdown chart inline → redirect URL langsung ────────────────────────────
(function () {
    const baseUrl  = '{{ route("energi.index") }}';
    const curParam = '{{ $parameter }}';
    const curPer   = '{{ $periode }}';
    const curTgl   = '{{ $tanggal }}';

    function navigate(parameter, periode) {
        const url = baseUrl + '?parameter=' + parameter + '&periode=' + periode + '&tanggal=' + encodeURIComponent(curTgl);
        window.location.href = url;
    }

    const selParam = document.getElementById('chart-parameter');
    const selPer   = document.getElementById('chart-periode');

    selParam?.addEventListener('change', function () {
        navigate(this.value, selPer?.value ?? curPer);
    });

    selPer?.addEventListener('change', function () {
        navigate(selParam?.value ?? curParam, this.value);
    });
})();
</script>


<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const labels = {!! json_encode($chartLabels) !!};
    const values = {!! json_encode($chartValues) !!};
    const unit   = '{{ $unit }}';
    const th     = {!! json_encode($th) !!};
    const param  = '{{ $paramLabel }}';

    const statusColors = { normal: '#22c55e', warning: '#f59e0b', poor: '#ef4444' };
    function getStatus(v) {
        if (v > th.warn_upper) return 'poor';
        if (v > th.normal_max) return 'warning';
        return 'normal';
    }

    const ctx = document.getElementById('energyChart').getContext('2d');

    // Gradient fill — merah muda fade ke transparan
    const gradient = ctx.createLinearGradient(0, 0, 0, 320);
    gradient.addColorStop(0,   'rgba(239,68,68,0.18)');
    gradient.addColorStop(1,   'rgba(239,68,68,0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: param + ' Gedung',
                data: values,
                borderColor: '#ef4444',
                backgroundColor: gradient,
                borderWidth: 2,
                // Hollow circles: background putih, border sesuai status
                spanGaps: false,
                pointRadius: 5,
                pointBackgroundColor: '#fff',
                pointBorderColor: values.map(v => v === null ? 'transparent' : statusColors[getStatus(v)]),
                pointBorderWidth: 2,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderWidth: 2.5,
                fill: true,
                tension: 0.4,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    enabled: false,   // matikan tooltip bawaan
                    external: function ({ chart, tooltip }) {
                        let el = document.getElementById('energyTooltip');
                        if (!el) {
                            el = document.createElement('div');
                            el.id = 'energyTooltip';
                            el.style.cssText = [
                                'position:absolute',
                                'pointer-events:none',
                                'background:#fff',
                                'border:1px solid #e2e8f0',
                                'border-radius:8px',
                                'padding:9px 12px',
                                'font-size:12px',
                                'box-shadow:0 2px 10px rgba(0,0,0,0.08)',
                                'z-index:99',
                                'min-width:110px',
                                'transition:opacity .15s',
                            ].join(';');
                            document.body.appendChild(el);
                        }

                        if (tooltip.opacity === 0) {
                            el.style.opacity = '0';
                            return;
                        }

                        const iconBase = '{{ asset("icons") }}';

                        const dp    = tooltip.dataPoints?.[0];
                        const val   = dp ? dp.parsed.y : 0;
                        const s     = getStatus(val);
                        const sc    = statusColors[s];
                        const sLbl  = s.charAt(0).toUpperCase() + s.slice(1);
                        const title = tooltip.title?.[0] ?? '';

                        const dot  = color => `<span style="display:inline-block;width:13px;height:13px;border-radius:50%;background:${color};margin-right:6px;flex-shrink:0"></span>`;
                        const icon = status => `<img src="${iconBase}/${status}.svg" style="width:16px;height:16px;margin-right:6px;flex-shrink:0">`;

                        el.innerHTML = `
                            <div style="font-weight:700;font-size:13px;color:#0f172a;margin-bottom:6px">${title}</div>
                            <div style="display:flex;align-items:center;color:#475569;margin-bottom:3px">${dot(sc)}${val} ${unit}</div>
                            <div style="display:flex;align-items:center;color:#475569">${icon(s)}${sLbl}</div>
                        `;

                        const canvas = chart.canvas;
                        const rect   = canvas.getBoundingClientRect();
                        const scrollX = window.scrollX || document.documentElement.scrollLeft;
                        const scrollY = window.scrollY || document.documentElement.scrollTop;

                        let left = rect.left + scrollX + tooltip.caretX + 12;
                        let top  = rect.top  + scrollY + tooltip.caretY  - 20;

                        // jangan melewati kanan layar
                        if (left + 140 > window.innerWidth + scrollX) {
                            left = rect.left + scrollX + tooltip.caretX - 130;
                        }

                        el.style.opacity = '1';
                        el.style.left    = left + 'px';
                        el.style.top     = top  + 'px';
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },  // tidak ada grid vertikal
                    border: { display: false },
                    ticks: { font: { size: 11 }, color: '#94a3b8' },
                },
                y: {
                    grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false },
                    border: { display: false, dash: [4, 4] },
                    ticks: {
                        font: { size: 11 },
                        color: '#94a3b8',
                        callback: v => v + ' ' + unit,
                    },
                },
            },
        },
    });
})();
</script>
@endpush
