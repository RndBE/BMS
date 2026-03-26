<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\AlertLimit;
use App\Models\Room;
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
        $dateFormat   = Setting::get('date_format',   'DD/MM/YYYY');   // e.g. DD/MM/YYYY
        $timeFormat   = Setting::get('time_format',   '24');           // 24 atau 12
        $defaultRange = Setting::get('default_range', 'harian');       // harian/mingguan/bulanan

        // Mapping date_format setting → PHP Carbon format
        $phpDateFormat = match ($dateFormat) {
            'MM/DD/YYYY' => 'm/d/Y',
            'YYYY-MM-DD' => 'Y-m-d',
            default      => 'd/m/Y',  // DD/MM/YYYY
        };
        // MySQL DATE_FORMAT equivalent
        $sqlDateFormat = match ($dateFormat) {
            'MM/DD/YYYY' => '%m/%d/%Y',
            'YYYY-MM-DD' => '%Y-%m-%d',
            default      => '%d/%m/%Y',
        };

        $is12h = $timeFormat === '12';

        // Default: room pertama, parameter temperature, default_range dari setting, hari ini
        $selectedRoomId = $request->get('room_id', $rooms->first()?->id);
        $parameter      = $request->get('parameter', 'temperature');
        $periode        = $request->get('periode', $defaultRange);
        // Tanggal selalu Y-m-d karena type="date" HTML selalu kirim format ini
        $tanggal        = $request->get('tanggal', now()->setTimezone($timezone)->format('Y-m-d'));

        // Pemetaan parameter (nama display) → kolom sensor_readings
        // sensor1=Suhu, sensor2=Kelembaban, sensor3=Energi, sensor4=Daya, sensor5=CO₂
        $columnMap = [
            'temperature' => 'sensor1',
            'humidity'    => 'sensor2',
            'energy'      => 'sensor3',
            'power'       => 'sensor4',
            'co2'         => 'sensor5',
        ];
        // Kolom aktual yang dipakai di query (fallback ke sensor1 jika parameter tidak dikenal)
        $column = $columnMap[$parameter] ?? 'sensor1';

        $selectedRoom = $rooms->find($selectedRoomId);

        // Parse tanggal — selalu Y-m-d (dari type="date" input)
        try {
            $date = Carbon::createFromFormat('Y-m-d', $tanggal)
                        ->setTimezone($timezone)
                        ->startOfDay();
        } catch (\Exception $e) {
            $date = Carbon::today()->setTimezone($timezone)->startOfDay();
            $tanggal = $date->format('Y-m-d');
        }

        // Format jam dari setting Umum (24 atau 12)
        // Tentukan rentang waktu berdasarkan periode
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

        // Jika periode harian dan tanggal = hari ini → potong dateTo ke jam sekarang
        // supaya chart & tabel tidak menampilkan jam yang belum terjadi
        if ($periode === 'harian' && $date->isSameDay(Carbon::today())) {
            $dateTo = Carbon::now()->setTimezone($timezone);
        }

        // Query chart data (group by jam/hari) — gunakan $column bukan $parameter
        $chartData = SensorReading::where('room_id', $selectedRoomId)
            ->whereBetween('waktu', [$dateFrom, $dateTo])
            ->selectRaw("DATE_FORMAT(waktu, '{$groupFormat}') as label, AVG({$column}) as value, MAX(waktu) as last_waktu")
            ->groupByRaw("DATE_FORMAT(waktu, '{$groupFormat}')")
            ->orderBy('last_waktu')
            ->get();

        // Stats — gunakan $column
        $statsQuery = SensorReading::where('room_id', $selectedRoomId)
            ->whereBetween('waktu', [$dateFrom, $dateTo]);

        $latest  = SensorReadingLatest::where('room_id', $selectedRoomId)
            ->whereDate('waktu', today())
            ->value($column);
        $average = round((float) $statsQuery->avg($column), 1);
        $max     = round((float) $statsQuery->max($column), 1);
        $min     = round((float) $statsQuery->min($column), 1);

        // ── Map parameter analisa-data → parameter_key di AlertLimit ─────────
        $paramKeyMap = [
            'temperature' => 'suhu',
            'humidity'    => 'kelembaban',
            'co2'         => 'co2',
        ];

        // Load semua limit dari DB, index by parameter_key
        $limitsFromDb = AlertLimit::all()->keyBy('parameter_key');

        // Bangun $thresholds dari DB (fallback ke nilai statis jika belum ada di DB)
        $thresholds = [];
        foreach ($paramKeyMap as $paramKey => $dbKey) {
            $lim = $limitsFromDb->get($dbKey);
            if ($lim) {
                $thresholds[$paramKey] = [
                    'normal_min'    => $lim->normal_min,
                    'normal_max'    => $lim->normal_max,
                    'warn_low_min'  => $lim->warn_low_min,
                    'warn_low_max'  => $lim->warn_low_max,
                    'warn_high_min' => $lim->warn_high_min,
                    'warn_high_max' => $lim->warn_high_max,
                    'poor_low'      => $lim->poor_low,
                    'poor_high'     => $lim->poor_high,
                    // backward-compat fields untuk tabel & chart status
                    'warn_lower'    => $lim->warn_low_min ?? $lim->warn_high_min,
                    'warn_upper'    => $lim->warn_high_max ?? $lim->warn_low_max,
                ];
            } else {
                $thresholds[$paramKey] = [
                    'normal_min' => null, 'normal_max' => null,
                    'warn_low_min' => null, 'warn_low_max' => null,
                    'warn_high_min' => null, 'warn_high_max' => null,
                    'poor_low' => null, 'poor_high' => null,
                    'warn_lower' => null, 'warn_upper' => null,
                ];
            }
        }

        // AlertLimit untuk parameter saat ini (untuk view langsung)
        $alertLimit = $limitsFromDb->get($paramKeyMap[$parameter] ?? 'suhu');

        // Peringatan terkait ruangan ini
        $alerts = Alert::with('room')
            ->where('room_id', $selectedRoomId)
            ->latest()
            ->limit(6)
            ->get();

        // Tabel data parameter utama — rata-rata per jam/hari (sama seperti chart)
        $th = $thresholds[$parameter] ?? $thresholds['temperature'];

        // Untuk periode harian: generate semua label jam 00:00 s.d. jam terakhir dateTo
        // Untuk 12h: format berbeda (misal 09:00 AM); untuk 24h: HH:00
        if ($periode === 'harian') {
            $endHour   = (int) $dateTo->format('H');
            $allLabels = [];
            for ($h = 0; $h <= $endHour; $h++) {
                // Format label sesuai setting waktu
                $allLabels[] = $is12h
                    ? Carbon::today()->setTimezone($timezone)->setHour($h)->format('g:00 A')
                    : sprintf('%02d:00', $h);
            }

            // Index chartData berdasarkan label
            $indexed = $chartData->keyBy('label');

            // Chart: semua label, slot tanpa data diisi null
            $chartLabels = collect($allLabels);
            $chartValues = $chartLabels->map(
                fn($l) => $indexed->has($l)
                    ? round((float) $indexed[$l]->value, 2)
                    : null
            );

            // Tabel: hanya slot yang ada datanya
            $tableData = $chartLabels->map(function ($l) use ($indexed, $th) {
                if (! $indexed->has($l)) return null;
                $val    = (float) $indexed[$l]->value;
                $status = 'normal';
                if ($val < $th['warn_lower'] || $val > $th['warn_upper']) {
                    $status = 'poor';
                } elseif ($val < $th['normal_min'] || $val > $th['normal_max']) {
                    $status = 'warning';
                }
                return ['waktu' => $l, 'nilai' => round($val, 2), 'status' => $status];
            })->filter()->values();

        } else {
            // Periode mingguan / bulanan: tampilkan semua slot dari query
            $chartLabels = $chartData->pluck('label');
            $chartValues = $chartData->pluck('value')->map(fn($v) => round((float)$v, 2));

            $tableData = $chartData->map(function ($row) use ($th) {
                $val    = (float) $row->nilai;
                $status = 'normal';
                if ($val < $th['warn_lower'] || $val > $th['warn_upper']) {
                    $status = 'poor';
                } elseif ($val < $th['normal_min'] || $val > $th['normal_max']) {
                    $status = 'warning';
                }
                return ['waktu' => $row->label, 'nilai' => round($val, 2), 'status' => $status];
            });
        }

        $parameterLabels = [
            'temperature' => 'Suhu',
            'humidity'    => 'Kelembapan',
            'co2'         => 'CO₂',
        ];
        $parameterUnits = [
            'temperature' => '°C',
            'humidity'    => '%',
            'co2'         => 'ppm',
        ];

        return view('analisa-data.index', compact(
            'rooms', 'selectedRoom', 'selectedRoomId',
            'parameter', 'periode', 'tanggal', 'date',
            'chartData', 'chartLabels', 'chartValues',
            'latest', 'average', 'max', 'min',
            'thresholds', 'alerts', 'tableData', 'alertLimit',
            'parameterLabels', 'parameterUnits', 'timeFormat',
            'timezone', 'dateFormat', 'phpDateFormat'
        ));
    }
}
