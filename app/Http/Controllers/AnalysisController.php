<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\AlertLimit;
use App\Models\Room;
use App\Models\SensorParameter;
use App\Models\SensorReading;
use App\Models\SensorReadingLatest;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::orderBy('name')->get();

        // ── Baca semua setting dari DB ────────────────────────────────────────
        $timezone     = Setting::get('timezone',      'Asia/Jakarta');
        $dateFormat   = Setting::get('date_format',   'DD/MM/YYYY');
        $timeFormat   = Setting::get('time_format',   '24');
        $defaultRange = Setting::get('default_range', 'harian');

        // Mapping date_format setting → PHP Carbon format
        $phpDateFormat = match ($dateFormat) {
            'MM/DD/YYYY' => 'm/d/Y',
            'YYYY-MM-DD' => 'Y-m-d',
            default      => 'd/m/Y',
        };
        $sqlDateFormat = match ($dateFormat) {
            'MM/DD/YYYY' => '%m/%d/%Y',
            'YYYY-MM-DD' => '%Y-%m-%d',
            default      => '%d/%m/%Y',
        };

        $is12h = $timeFormat === '12';

        $selectedRoomId = $request->get('room_id', $rooms->first()?->id);
        $periode        = $request->get('periode', $defaultRange);
        $tanggal        = $request->get('tanggal', now()->setTimezone($timezone)->format('Y-m-d'));

        $selectedRoom = $rooms->find($selectedRoomId);

        // ── Ambil sensor parameters dinamis dari DB per ruangan ───────────────
        $sensorParams = SensorParameter::where('room_id', $selectedRoomId)
            ->orderBy('sort_order')
            ->get();

        // Bangun pemetaan dinamis: kolom_reading → [nama, unit]
        // Contoh: ['sensor1' => ['nama' => 'Suhu', 'unit' => '°C'], ...]
        $paramMap = [];
        foreach ($sensorParams as $sp) {
            $paramMap[$sp->kolom_reading] = [
                'nama' => $sp->nama_parameter,
                'unit' => $sp->unit ?? '',
            ];
        }

        // Fallback jika ruangan belum punya parameter di DB
        if (empty($paramMap)) {
            $paramMap = ['sensor1' => ['nama' => 'Sensor 1', 'unit' => '']];
        }

        // Tentukan parameter aktif (kolom_reading), fallback ke yang pertama
        $requestedParam = $request->get('parameter', '');
        $parameter = array_key_exists($requestedParam, $paramMap)
            ? $requestedParam
            : array_key_first($paramMap);

        $column    = $parameter; // langsung pakai sebagai nama kolom
        $paramInfo = $paramMap[$parameter];

        // Parse tanggal
        try {
            $date = Carbon::createFromFormat('Y-m-d', $tanggal)
                        ->setTimezone($timezone)
                        ->startOfDay();
        } catch (\Exception $e) {
            $date    = Carbon::today()->setTimezone($timezone)->startOfDay();
            $tanggal = $date->format('Y-m-d');
        }

        // Rentang waktu & format label berdasarkan periode
        [$dateFrom, $dateTo, $groupFormat, $labelFormat] = match ($periode) {
            'mingguan' => [
                $date->copy()->startOfWeek(),
                $date->copy()->endOfWeek(),
                '%Y-%m-%d',
                $sqlDateFormat,
            ],
            'bulanan' => [
                $date->copy()->startOfMonth(),
                $date->copy()->endOfMonth(),
                '%Y-%m-%d',
                $sqlDateFormat,
            ],
            default => [  // harian
                $date->copy()->startOfDay(),
                $date->copy()->endOfDay(),
                $is12h ? '%h:00 %p' : '%H:00',
                $is12h ? 'h:i A'   : 'H:i',
            ],
        };

        // Potong dateTo ke jam sekarang jika harian & hari ini
        if ($periode === 'harian' && $date->isSameDay(Carbon::today())) {
            $dateTo = Carbon::now()->setTimezone($timezone);
        }

        // ── Query chart data (rata-rata per interval) ─────────────────────────
        $chartData = SensorReading::where('room_id', $selectedRoomId)
            ->whereBetween('waktu', [$dateFrom, $dateTo])
            ->selectRaw("DATE_FORMAT(waktu, '{$groupFormat}') as label, AVG({$column}) as value, MAX(waktu) as last_waktu")
            ->groupByRaw("DATE_FORMAT(waktu, '{$groupFormat}')")
            ->orderBy('last_waktu')
            ->get();

        // ── Stats ─────────────────────────────────────────────────────────────
        $statsQuery = SensorReading::where('room_id', $selectedRoomId)
            ->whereBetween('waktu', [$dateFrom, $dateTo]);

        $latest  = SensorReadingLatest::where('room_id', $selectedRoomId)
            ->whereDate('waktu', today())
            ->value($column);
        $average = round((float) $statsQuery->avg($column), 1);
        $max     = round((float) $statsQuery->max($column), 1);
        $min     = round((float) $statsQuery->min($column), 1);

        // ── Alert Limits: coba cocokkan berdasarkan nama_parameter (lowercase) ─
        // Contoh nama_parameter: "Suhu" → cek key 'suhu'
        $limitsFromDb = AlertLimit::all()->keyBy('parameter_key');

        // Prioritas: exact match nama_parameter lowercase, lalu cari by kandungan kata
        $alertLimit = null;
        $paramNameLower = strtolower($paramInfo['nama']);

        // Coba match langsung
        if ($limitsFromDb->has($paramNameLower)) {
            $alertLimit = $limitsFromDb->get($paramNameLower);
        } else {
            // Coba partial match: 'suhu', 'kelembaban', 'co2'
            foreach ($limitsFromDb as $key => $lim) {
                if (str_contains($paramNameLower, $key) || str_contains($key, $paramNameLower)) {
                    $alertLimit = $lim;
                    break;
                }
            }
        }

        // Bangun array threshold untuk dipakai di chart tooltip
        $th = $alertLimit ? [
            'normal_min'    => $alertLimit->normal_min,
            'normal_max'    => $alertLimit->normal_max,
            'warn_low_min'  => $alertLimit->warn_low_min,
            'warn_low_max'  => $alertLimit->warn_low_max,
            'warn_high_min' => $alertLimit->warn_high_min,
            'warn_high_max' => $alertLimit->warn_high_max,
            'poor_low'      => $alertLimit->poor_low,
            'poor_high'     => $alertLimit->poor_high,
            'warn_lower'    => $alertLimit->warn_low_min ?? $alertLimit->warn_high_min,
            'warn_upper'    => $alertLimit->warn_high_max ?? $alertLimit->warn_low_max,
        ] : [
            'normal_min' => null, 'normal_max' => null,
            'warn_low_min' => null, 'warn_low_max' => null,
            'warn_high_min' => null, 'warn_high_max' => null,
            'poor_low' => null, 'poor_high' => null,
            'warn_lower' => null, 'warn_upper' => null,
        ];

        // ── Peringatan terkait ruangan ────────────────────────────────────────
        $alerts = Alert::with('room')
            ->where('room_id', $selectedRoomId)
            ->latest()
            ->limit(6)
            ->get();

        // ── Bangun chart labels & values ──────────────────────────────────────
        if ($periode === 'harian') {
            $endHour   = (int) $dateTo->format('H');
            $allLabels = [];
            for ($h = 0; $h <= $endHour; $h++) {
                $allLabels[] = $is12h
                    ? Carbon::today()->setTimezone($timezone)->setHour($h)->format('g:00 A')
                    : sprintf('%02d:00', $h);
            }

            $indexed = $chartData->keyBy('label');

            $chartLabels = collect($allLabels);
            $chartValues = $chartLabels->map(
                fn($l) => $indexed->has($l)
                    ? round((float) $indexed[$l]->value, 2)
                    : null
            );

            $tableData = $chartLabels->map(function ($l) use ($indexed, $th) {
                if (! $indexed->has($l)) return null;
                $val    = (float) $indexed[$l]->value;
                $status = $this->resolveStatus($val, $th);
                return ['waktu' => $l, 'nilai' => round($val, 2), 'status' => $status];
            })->filter()->values();

        } else {
            $chartLabels = $chartData->pluck('label');
            $chartValues = $chartData->pluck('value')->map(fn($v) => round((float)$v, 2));

            $tableData = $chartData->map(function ($row) use ($th) {
                $val    = (float) $row->value;
                $status = $this->resolveStatus($val, $th);
                return ['waktu' => $row->label, 'nilai' => round($val, 2), 'status' => $status];
            });
        }

        return view('analisa-data.index', compact(
            'rooms', 'selectedRoom', 'selectedRoomId',
            'parameter', 'periode', 'tanggal', 'date',
            'paramMap',          // dynamic parameter list per room
            'paramInfo',         // ['nama' => '...', 'unit' => '...'] for active param
            'chartData', 'chartLabels', 'chartValues',
            'latest', 'average', 'max', 'min',
            'th', 'alerts', 'tableData', 'alertLimit',
            'timeFormat', 'timezone', 'dateFormat', 'phpDateFormat'
        ));
    }

    /**
     * Tentukan status (normal/warning/poor) berdasarkan nilai dan threshold.
     */
    private function resolveStatus(float $val, array $th): string
    {
        if ($th['warn_lower'] !== null && $th['warn_upper'] !== null) {
            if ($val < $th['warn_lower'] || $val > $th['warn_upper']) return 'poor';
        }
        if ($th['normal_min'] !== null && $th['normal_max'] !== null) {
            if ($val < $th['normal_min'] || $val > $th['normal_max']) return 'warning';
        }
        return 'normal';
    }
}
