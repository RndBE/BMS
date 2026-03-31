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
        <div class="bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-xl border border-slate-100 shadow-sm px-5 py-4">
            <div class="flex flex-wrap gap-3 items-end w-full">
                {{-- Ruangan --}}
                <div class="flex flex-col gap-2 flex-1">
                    <label class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Ruangan</label>
                    <div class="relative custom-select-wrapper w-full">
                        <select name="room_id" id="sel-room" class="hidden real-select">
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" {{ $room->id == $selectedRoomId ? 'selected' : '' }}>{{ $room->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="select-btn flex justify-between items-center w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3 py-[7px] text-[13px] text-slate-700 bg-white focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400 cursor-pointer">
                            <span class="select-text truncate">Pilih Ruangan</span>
                            <svg class="w-4 h-4 text-slate-400 shrink-0 pointer-events-none ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <ul class="select-dropdown absolute top-full left-0 w-full mt-1 bg-white dark:bg-[#2a2a2a] border border-slate-200 dark:border-[#3d3d3d] rounded-lg shadow-lg hidden max-h-60 overflow-y-auto py-1 z-50 text-[13px] text-slate-700 dark:text-slate-200"></ul>
                    </div>
                </div>

                {{-- Parameter --}}
                <div class="flex flex-col gap-2 flex-1">
                    <label class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Parameter</label>
                    <div class="relative custom-select-wrapper w-full">
                        <select name="parameter" id="sel-parameter" class="hidden real-select">
                            @foreach($paramMap as $colKey => $info)
                                <option value="{{ $colKey }}" {{ $parameter === $colKey ? 'selected' : '' }}>{{ $info['nama'] }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="select-btn flex justify-between items-center w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3 py-[7px] text-[13px] text-slate-700 bg-white focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400 cursor-pointer">
                            <span class="select-text truncate">Pilih Parameter</span>
                            <svg class="w-4 h-4 text-slate-400 shrink-0 pointer-events-none ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <ul class="select-dropdown absolute top-full left-0 w-full mt-1 bg-white dark:bg-[#2a2a2a] border border-slate-200 dark:border-[#3d3d3d] rounded-lg shadow-lg hidden max-h-60 overflow-y-auto py-1 z-50 text-[13px] text-slate-700 dark:text-slate-200"></ul>
                    </div>
                </div>

                {{-- Periode --}}
                <div class="flex flex-col gap-2 flex-1">
                    <label class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Periode</label>
                    <div class="relative custom-select-wrapper w-full">
                        <select name="periode" id="sel-periode" class="hidden real-select">
                            <option value="harian"   {{ $periode === 'harian'   ? 'selected' : '' }}>Harian</option>
                            <option value="mingguan" {{ $periode === 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                            <option value="bulanan"  {{ $periode === 'bulanan'  ? 'selected' : '' }}>Bulanan</option>
                        </select>
                        <button type="button" class="select-btn flex justify-between items-center w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3 py-[7px] text-[13px] text-slate-700 bg-white focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400 cursor-pointer">
                            <span class="select-text truncate">Pilih Periode</span>
                            <svg class="w-4 h-4 text-slate-400 shrink-0 pointer-events-none ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <ul class="select-dropdown absolute top-full left-0 w-full mt-1 bg-white dark:bg-[#2a2a2a] border border-slate-200 dark:border-[#3d3d3d] rounded-lg shadow-lg hidden max-h-60 overflow-y-auto py-1 z-50 text-[13px] text-slate-700 dark:text-slate-200"></ul>
                    </div>
                </div>

                {{-- Tanggal --}}
                <div class="flex flex-col gap-2 flex-1">
                    <label class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Tanggal</label>
                    <input type="date" name="tanggal" id="inp-tanggal" value="{{ $tanggal }}"
                        class="border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3 py-[7px] text-[13px] text-slate-700 bg-white focus:outline-none focus:border-red-400 w-full cursor-pointer">
                </div>

                {{-- Buttons --}}
                <div class="flex gap-2 pb-[1px] ml-auto shrink-0">
                    <button type="submit"
                        class="bg-red-600 hover:bg-red-800 text-white text-[13px] font-semibold px-5 py-[8px] rounded-lg transition-colors cursor-pointer whitespace-nowrap">
                        Terapkan
                    </button>
                    <a href="{{ route('analisa-data.index') }}"
                        class="hover:bg-slate-50 text-slate-700 text-[13px] font-semibold px-5 py-[8px] rounded-lg border border-slate-200 transition-colors no-underline whitespace-nowrap dark:text-slate-200 dark:border-slate-200">
                        Reset
                    </a>
                </div>
            </div>
        </div>
    </form>

    {{-- ── MAIN ROW: Chart + Side Panel ───────────────────────────────────── --}}
    @php
        $paramLabel = $paramInfo['nama'];
        $unit       = $paramInfo['unit'];

        // Semua parameter pakai warna merah BMS (konsisten)
        $color = ['line' => '#ef4444', 'fill' => 'rgba(239,68,68,0.12)'];
    @endphp

    <div class="flex flex-col xl:flex-row gap-5">

        {{-- Chart --}}
        <div class="flex-1 min-w-0 bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-xl border border-slate-100 shadow-sm p-5">
            <div class="text-center mb-1">
                <div class="text-[20px] font-semibold text-slate-800 dark:text-white">
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
            <div class="bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-xl border border-slate-100 shadow-sm p-4">
                <div class="text-[13px] font-semibold text-slate-700 dark:text-slate-200 mb-3">Batas Normal</div>
                @if($alertLimit)
                <div class="space-y-2">
                    {{-- Normal --}}
                    <div class="flex items-start gap-2">
                        <img src="{{ asset('icons/normal.svg') }}" alt="Normal" class="w-5 h-5 mt-0.5 shrink-0">
                        <div class="text-[12px]">
                            <span class="font-semibold text-slate-700 dark:text-slate-200">Normal</span>
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
                    {{-- Warning (Rendah + Tinggi digabung) --}}
                    <div class="flex items-start gap-2">
                        <img src="{{ asset('icons/warning.svg') }}" alt="Warning" class="w-5 h-5 mt-0.5 shrink-0">
                        <div class="text-[12px]">
                            <span class="font-semibold text-slate-700 dark:text-slate-200">Warning</span>
                            <span class="text-slate-500 dark:text-slate-400 ml-1">:
                                @if($alertLimit && $alertLimit->warn_low_min !== null)
                                    {{ $alertLimit->warn_low_min }}{{ $unit }} – {{ $alertLimit->warn_low_max }}{{ $unit }}
                                @endif
                                @if($alertLimit && $alertLimit->warn_low_min !== null && $alertLimit->warn_high_min !== null)
                                    atau
                                @endif
                                @if($alertLimit && $alertLimit->warn_high_min !== null)
                                    {{ $alertLimit->warn_high_min }}{{ $unit }} – {{ $alertLimit->warn_high_max }}{{ $unit }}
                                @endif
                            </span>
                        </div>
                    </div>

                    {{-- Poor --}}
                    @if($alertLimit->poor_low !== null || $alertLimit->poor_high !== null)
                    <div class="flex items-start gap-2">
                        <img src="{{ asset('icons/poor.svg') }}" alt="Poor" class="w-5 h-5 mt-0.5 shrink-0">
                        <div class="text-[12px]">
                            <span class="font-semibold text-slate-700 dark:text-slate-200">Poor</span>
                            <span class="text-slate-500 dark:text-slate-400 ml-1"> :
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
            <div class="bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-xl border border-slate-100 shadow-sm p-4 flex-1">
                <div class="text-[13px] font-semibold text-slate-700 dark:text-slate-200 mb-3">Peringatan Terkait</div>
                @if($alerts->isEmpty())
                    <div class="text-[12px] text-slate-400 text-center py-4">Tidak ada peringatan</div>
                @else
                    <div class="space-y-2.5">
                        @foreach($alerts as $alert)
                            @php
                                $iconMap = [
                                    // Suhu
                                    'high_temp'      => asset('icons/suhu-tinggi.svg'),
                                    'low_temp'       => asset('icons/suhu.svg'),
                                    // Kelembaban
                                    'high_humidity'  => asset('icons/kelembapan.svg'),
                                    'low_humidity'   => asset('icons/kelembapan.svg'),
                                    // CO2
                                    'co2_tinggi'     => asset('icons/co2_tinggi.svg'),
                                    'co2_rendah'     => asset('icons/co2_tinggi.svg'),
                                    // Daya / Energi
                                    'high_power'     => asset('icons/daya-tinggi.svg'),
                                    'low_power'      => asset('icons/daya.svg'),
                                    // Tegangan
                                    'high_voltage'   => asset('icons/tegangan.svg'),
                                    'low_voltage'    => asset('icons/tegangan.svg'),
                                    // Lainnya
                                    'sensor_offline' => asset('icons/sensor-offline.svg'),
                                    'ac_off'         => asset('icons/freeze.svg'),
                                    'critical'       => asset('icons/poor.svg'),
                                    'warning'        => asset('icons/warning.svg'),
                                ];
                                $iconSrc   = $iconMap[$alert->type] ?? asset('icons/warning.svg');
                                $isCo2Icon = in_array($alert->type, ['co2_tinggi', 'co2_rendah']);
                            @endphp
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    @if($isCo2Icon)
                                        {{-- Icon CO2 — tampil abu-abu via mask-image --}}
                                        <div class="shrink-0" style="
                                            width:20px; height:20px;
                                            background-color: #52260bff;
                                            -webkit-mask-image: url('{{ $iconSrc }}');
                                            mask-image: url('{{ $iconSrc }}');
                                            -webkit-mask-size: contain; mask-size: contain;
                                            -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat;
                                            -webkit-mask-position: center; mask-position: center;
                                        "></div>
                                    @else
                                        <img src="{{ $iconSrc }}" alt="{{ $alert->type }}" class="w-5 h-5 shrink-0">
                                    @endif
                                    <span class="text-[12px] text-slate-700 dark:text-slate-300 truncate">{{ $alert->message ?? $alert->type }}</span>
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
        // Deteksi ikon berdasarkan kata kunci di nama_parameter (case-insensitive)
        $nameLower = strtolower($paramLabel);

        // Definisi ikon per kelompok keyword
        $keywordIcons = [
            'suhu'       => ['terbaru'=>'analisa_data/suhu_terbaru.svg',          'rerata'=>'analisa_data/rerata_suhu.svg',       'tertinggi'=>'analisa_data/suhu_tertinggi.svg',      'terendah'=>'analisa_data/suhu_terendah.svg',     'color_terbaru'=>'#D9EDFF','color_rerata'=>'#FFF3E9','color_tertinggi'=>'#FFE1E2','color_terendah'=>'#D9F6FC'],
            'temperatur' => ['terbaru'=>'analisa_data/suhu_terbaru.svg',          'rerata'=>'analisa_data/rerata_suhu.svg',       'tertinggi'=>'analisa_data/suhu_tertinggi.svg',      'terendah'=>'analisa_data/suhu_terendah.svg',     'color_terbaru'=>'#D9EDFF','color_rerata'=>'#FFF3E9','color_tertinggi'=>'#FFE1E2','color_terendah'=>'#D9F6FC'],
            'kelembab'   => ['terbaru'=>'analisa_data/kelembaban_terbaru.svg',    'rerata'=>'analisa_data/rerata_kelembaban.svg', 'tertinggi'=>'analisa_data/kelembaban_tinggi.svg',   'terendah'=>'analisa_data/kelembaban_rendah.svg', 'color_terbaru'=>'#D9EDFF','color_rerata'=>'#FFF3E9','color_tertinggi'=>'#FFE1E2','color_terendah'=>'#D9F6FC'],
            'humid'      => ['terbaru'=>'analisa_data/kelembaban_terbaru.svg',    'rerata'=>'analisa_data/rerata_kelembaban.svg', 'tertinggi'=>'analisa_data/kelembaban_tinggi.svg',   'terendah'=>'analisa_data/kelembaban_rendah.svg', 'color_terbaru'=>'#D9EDFF','color_rerata'=>'#FFF3E9','color_tertinggi'=>'#FFE1E2','color_terendah'=>'#D9F6FC'],
            'co2'        => ['terbaru'=>'analisa_data/co2_terbaru.svg',           'rerata'=>'analisa_data/rerata_co2.svg',        'tertinggi'=>'analisa_data/co2_tertinggi.svg',        'terendah'=>'analisa_data/co2_terendah.svg',      'color_terbaru'=>'#D9EDFF','color_rerata'=>'#FFF3E9','color_tertinggi'=>'#FFE1E2','color_terendah'=>'#D9F6FC'],
            'energi'     => ['terbaru'=>'energi.svg',                             'rerata'=>'energi.svg',                        'tertinggi'=>'energi.svg',                            'terendah'=>'energi.svg',                         'color_terbaru'=>'#FFF9E6','color_rerata'=>'#FFF9E6','color_tertinggi'=>'#FFE1E2','color_terendah'=>'#D9F6FC'],
            'energy'     => ['terbaru'=>'energi.svg',                             'rerata'=>'energi.svg',                        'tertinggi'=>'energi.svg',                            'terendah'=>'energi.svg',                         'color_terbaru'=>'#FFF9E6','color_rerata'=>'#FFF9E6','color_tertinggi'=>'#FFE1E2','color_terendah'=>'#D9F6FC'],
            'daya'       => ['terbaru'=>'daya.svg',                               'rerata'=>'daya.svg',                          'tertinggi'=>'daya-tinggi.svg',                       'terendah'=>'daya.svg',                           'color_terbaru'=>'#F3EEFF','color_rerata'=>'#F3EEFF','color_tertinggi'=>'#FFE1E2','color_terendah'=>'#D9F6FC'],
            'power'      => ['terbaru'=>'daya.svg',                               'rerata'=>'daya.svg',                          'tertinggi'=>'daya-tinggi.svg',                       'terendah'=>'daya.svg',                           'color_terbaru'=>'#F3EEFF','color_rerata'=>'#F3EEFF','color_tertinggi'=>'#FFE1E2','color_terendah'=>'#D9F6FC'],
        ];

        // Cari match keyword pertama
        $matchedIcons = null;
        foreach ($keywordIcons as $keyword => $icons) {
            if (str_contains($nameLower, $keyword)) {
                $matchedIcons = $icons;
                break;
            }
        }

        // Fallback ke ikon generik monitoring jika tidak ada keyword yang cocok
        $defaultIcon  = 'suhu.svg';
        $defaultColor = '#F1F5F9';

        $statCards = [
            [
                'icon'  => $matchedIcons ? $matchedIcons['terbaru']          : $defaultIcon,
                'color' => $matchedIcons ? $matchedIcons['color_terbaru']    : $defaultColor,
                'label' => $paramLabel . ' Terbaru',
                'value' => ($latest !== null ? number_format((float)$latest, 1) : '0') . ' ' . $unit,
            ],
            [
                'icon'  => $matchedIcons ? $matchedIcons['rerata']           : $defaultIcon,
                'color' => $matchedIcons ? $matchedIcons['color_rerata']     : $defaultColor,
                'label' => 'Rerata ' . $paramLabel,
                'value' => ($average !== null ? $average : '–') . ' ' . $unit,
            ],
            [
                'icon'  => $matchedIcons ? $matchedIcons['tertinggi']        : $defaultIcon,
                'color' => $matchedIcons ? $matchedIcons['color_tertinggi']  : $defaultColor,
                'label' => $paramLabel . ' Tertinggi',
                'value' => ($max !== null ? $max : '–') . ' ' . $unit,
            ],
            [
                'icon'  => $matchedIcons ? $matchedIcons['terendah']         : $defaultIcon,
                'color' => $matchedIcons ? $matchedIcons['color_terendah']   : $defaultColor,
                'label' => $paramLabel . ' Terendah',
                'value' => ($min !== null ? $min : '–') . ' ' . $unit,
            ],
        ];
    @endphp

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($statCards as $card)
            <div class="bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-xl border border-slate-100 shadow-sm px-5 py-4 flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                    style="background-color: {{ $card['color'] }}; border: 1.5px solid {{ $card['color'] }}">
                    <img src="{{ asset('icons/' . $card['icon']) }}" alt="{{ $card['label'] }}" class="w-6 h-6">
                </div>
                <div class="min-w-0">
                    <div class="text-[11px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide truncate">{{ $card['label'] }}</div>
                    <div class="text-[18px] font-bold text-slate-800 dark:text-white leading-tight">{{ $card['value'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── TABEL DATA ──────────────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-xl border border-slate-100 shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-[#2d2d2d]">
            <span class="text-[14px] font-semibold text-slate-800 dark:text-white">Tabel Data</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-[13px]">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-[#2d2d2d]">
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Waktu</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Nilai</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tableData as $row)
                        <tr class="border-b border-slate-50 dark:border-[#2d2d2d] hover:bg-slate-50 dark:hover:bg-[#2a2a2a] transition-colors">
                            <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $row['waktu'] }}</td>
                            <td class="px-5 py-3 text-slate-700 dark:text-slate-200 font-medium">
                                {{ $row['nilai'] }} {{ $paramInfo['unit'] }}
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
    const labels     = @json($chartLabels);
    const values     = @json($chartValues);
    const lineColor  = '{{ $color['line'] }}';
    const fillColor  = '{{ $color['fill'] }}';
    const unit       = '{{ $paramInfo['unit'] }}';
    const paramLabel = '{{ $paramInfo['nama'] }}';

    // Icon URL dari public/icons (di-pass dari PHP)
    const icons = {
        normal:  '{{ asset('icons/normal.svg') }}',
        warning: '{{ asset('icons/warning.svg') }}',
        poor:    '{{ asset('icons/poor.svg') }}',
    };

    // Threshold dari PHP
    const th = {
        normalMin: {{ $th['normal_min'] ?? 'null' }},
        normalMax: {{ $th['normal_max'] ?? 'null' }},
        warnLower: {{ $th['warn_lower'] ?? 'null' }},
        warnUpper: {{ $th['warn_upper'] ?? 'null' }},
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

    // ── Custom Select Initialization ─────────────────────────────────────────
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
                li.className = 'px-3 py-2 cursor-pointer transition-colors ' + 
                    (index === realSelect.selectedIndex 
                        ? 'bg-red-50 text-red-700 font-medium dark:bg-red-900/30 dark:text-red-400' 
                        : 'hover:bg-red-50 hover:text-red-700 dark:hover:bg-[#3d3d3d] dark:hover:text-red-400');
                
                li.addEventListener('click', (e) => {
                    e.stopPropagation();
                    realSelect.selectedIndex = index;
                    text.textContent = opt.text;
                    dropdown.classList.add('hidden');
                    populateDropdown(); // re-render untuk warna status aktif
                    realSelect.dispatchEvent(new Event('change'));
                });
                dropdown.appendChild(li);
            });
        };
        
        populateDropdown();
        
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation(); // Biar klik ngga bubar document listener
            
            const isHidden = dropdown.classList.contains('hidden');
            // Tutup semua yang lain
            document.querySelectorAll('.select-dropdown:not(.hidden)').forEach(d => {
                d.classList.add('hidden');
                d.parentElement.querySelector('.select-btn').classList.remove('ring-1', 'ring-red-400');
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
            d.parentElement.querySelector('.select-btn').classList.remove('ring-1', 'ring-red-400');
        });
    });

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
                spanGaps: false,
                pointBackgroundColor: '#fff',
                pointBorderColor: values.map(v => v === null ? 'transparent' : lineColor),
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
