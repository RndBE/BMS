<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Room;
use App\Models\SensorReading;
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

        $latest  = SensorReading::where('room_id', $selectedRoomId)->orderByDesc('waktu')->value($column);
        $average = round((float) $statsQuery->avg($column), 1);
        $max     = round((float) $statsQuery->max($column), 1);
        $min     = round((float) $statsQuery->min($column), 1);

        // Batas normal per parameter
        $thresholds = [
            'temperature' => ['normal_min' => 23,  'normal_max' => 26,   'warn_lower' => 21,  'warn_upper' => 28],
            'humidity'    => ['normal_min' => 40,  'normal_max' => 60,   'warn_lower' => 30,  'warn_upper' => 70],
            'co2'         => ['normal_min' => 400, 'normal_max' => 800,  'warn_lower' => 350, 'warn_upper' => 1200],
        ];

        // Peringatan terkait ruangan ini
        $alerts = Alert::with('room')
            ->where('room_id', $selectedRoomId)
            ->latest()
            ->limit(6)
            ->get();

        // Tabel data parameter utama — rata-rata per jam/hari (sama seperti chart)
        $th = $thresholds[$parameter] ?? $thresholds['temperature'];
        $tableData = SensorReading::where('room_id', $selectedRoomId)
            ->whereBetween('waktu', [$dateFrom, $dateTo])
            ->selectRaw("DATE_FORMAT(waktu, '{$groupFormat}') as label, AVG({$column}) as nilai, MAX(waktu) as last_waktu")
            ->groupByRaw("DATE_FORMAT(waktu, '{$groupFormat}')")
            ->orderBy('last_waktu')
            ->get()
            ->map(function ($row) use ($th) {
                $val    = (float) $row->nilai;
                $status = 'normal';
                if ($val < $th['warn_lower'] || $val > $th['warn_upper']) {
                    $status = 'poor';
                } elseif ($val < $th['normal_min'] || $val > $th['normal_max']) {
                    $status = 'warning';
                }
                return [
                    'waktu'  => $row->label,
                    'nilai'  => round($val, 2),
                    'status' => $status,
                ];
            });

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
            'chartData', 'latest', 'average', 'max', 'min',
            'thresholds', 'alerts', 'tableData',
            'parameterLabels', 'parameterUnits', 'timeFormat',
            'timezone', 'dateFormat', 'phpDateFormat'
        ));
    }
}
