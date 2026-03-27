<?php

namespace App\Http\Controllers;

use App\Models\AcUnit;
use App\Models\Alert;
use App\Models\Floor;
use App\Models\Room;
use App\Models\SensorParameter;
use App\Models\SensorReading;
use App\Models\SensorReadingLatest;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Load rooms + AC units — status dikelola oleh SensorReadingObserver
        $rooms = Room::with('acUnits')->get();

        // Summary stats dari kolom status di DB
        $statusCounts = [
            'normal'  => $rooms->where('status', 'normal')->count(),
            'warning' => $rooms->where('status', 'warning')->count(),
            'poor'    => $rooms->where('status', 'poor')->count(),
        ];

        // ── Avg sensor: hanya dari data hari ini ──
        $avgSensor   = SensorReadingLatest::whereDate('waktu', today())
            ->selectRaw('AVG(sensor1) as avg_temp, AVG(sensor2) as avg_humidity')
            ->first();
        $avgTemp     = round((float) ($avgSensor->avg_temp ?? 0), 1);
        $avgHumidity = round((float) ($avgSensor->avg_humidity ?? 0), 1);

        $activeAc = AcUnit::where('is_active', true)->count();
        $totalAc  = AcUnit::count();

        // Dapatkan parameter 'daya' beserta room_id dan kolom_reading-nya
        $dayaParams = SensorParameter::whereRaw('LOWER(nama_parameter) LIKE ?', ['%daya%'])
            ->whereNotNull('kolom_reading')
            ->get(['room_id', 'kolom_reading']);

        if ($dayaParams->isNotEmpty()) {
            // Pre-load latest readings sekaligus (1 query)
            $latestMap = SensorReadingLatest::whereIn('room_id', $dayaParams->pluck('room_id'))
                ->get()->keyBy('room_id');

            $currentPower = 0;
            $energyToday  = 0;

            foreach ($dayaParams as $param) {
                $latest = $latestMap->get($param->room_id);

                // Daya: hanya hitung jika data terbaru adalah hari ini
                if ($latest && $latest->waktu?->isToday()) {
                    $currentPower += (float) ($latest->{$param->kolom_reading} ?? 0);
                }

                // Energi hari ini: SUM per ruangan dari sensor_readings 00:00 - sekarang
                $energyToday += (float) SensorReading::where('room_id', $param->room_id)
                    ->whereBetween('waktu', [today()->startOfDay(), now()])
                    ->sum($param->kolom_reading);
            }

            $currentPower = round($currentPower, 2);
            $energyToday  = round($energyToday,  2);
        } else {
            // Fallback: pakai ac_units jika parameter daya belum dikonfigurasi
            $currentPower = AcUnit::where('is_active', true)->sum('power_kw');
            $energyToday  = round($currentPower * 11.5, 1);
        }

        $recentAlerts = Alert::with(['room', 'alertRule'])->latest()->limit(4)->get();

        // Load floor untuk denah
        $displayFloor = Floor::with(['building', 'rooms.acUnits'])
            ->where(function ($q) {
                $q->whereNotNull('plan_file_path')->orWhereNotNull('canvas_data');
            })->latest()->first();

        // Pre-load semua latest reading sekaligus (1 query, bukan N)
        $latestReadings = SensorReadingLatest::whereIn('room_id', $rooms->pluck('id'))
            ->get()->keyBy('room_id');

        $offlineThreshold = now()->subMinutes(60);

        // Pre-load SensorParameter per room (1 query, untuk resolusi kolom dinamis)
        $allParams = SensorParameter::whereIn('room_id', $rooms->pluck('id'))
            ->whereNotNull('kolom_reading')
            ->get(['room_id', 'nama_parameter', 'kolom_reading'])
            ->groupBy('room_id');

        // Helper: cari kolom sensor berdasarkan keyword nama_parameter
        $resolveCol = function ($roomParams, string $keyword): ?string {
            if (!$roomParams) return null;
            $match = $roomParams->first(fn($p) => stripos($p->nama_parameter, $keyword) !== false);
            return $match?->kolom_reading;
        };

        // Pre-build all room detail data as JSON (eliminates AJAX roundtrip)
        $roomDetailMap = $rooms->mapWithKeys(function ($room) use ($latestReadings, $offlineThreshold, $allParams, $resolveCol) {
            $latest     = $latestReadings->get($room->id);
            $ac         = $room->acUnits->first();
            $connected  = $latest !== null
                       && $latest->waktu !== null
                       && $latest->waktu->gte($offlineThreshold);
            $roomParams = $allParams->get($room->id);

            // Resolve kolom dinamis dari SensorParameter
            $colSuhu      = $resolveCol($roomParams, 'suhu')      ?? $resolveCol($roomParams, 'temp')   ?? 'sensor1';
            $colHumidity  = $resolveCol($roomParams, 'kelembab')  ?? $resolveCol($roomParams, 'humid')  ?? 'sensor2';
            $colCo2       = $resolveCol($roomParams, 'co2')                                              ?? 'sensor5';
            $colEnergy    = $resolveCol($roomParams, 'energi')    ?? $resolveCol($roomParams, 'energy') ?? 'sensor3';
            $colPower     = $resolveCol($roomParams, 'daya')      ?? $resolveCol($roomParams, 'power')  ?? 'sensor4';

            return [$room->id => [
                'id'               => $room->id,
                'name'             => $room->name,
                'status'           => $room->status,
                'updated_at'       => $latest?->waktu?->format('d/m/Y H:i') ?? $room->updated_at->format('d/m/Y H:i'),
                'temperature'      => $latest ? ['value' => $latest->{$colSuhu},     'unit' => '°C']  : null,
                'humidity'         => $latest ? ['value' => $latest->{$colHumidity}, 'unit' => '%']   : null,
                'co2'              => $latest ? ['value' => $latest->{$colCo2},      'unit' => 'ppm'] : null,
                'energy'           => $latest ? ['value' => $latest->{$colEnergy},   'unit' => 'kWh'] : null,
                'power'            => $latest ? ['value' => $latest->{$colPower},    'unit' => 'W']   : null,
                'ac_status'        => $ac ? ($ac->is_active ? 'ON' : 'OFF') : 'N/A',
                'sensor_connected' => $connected,
            ]];
        });


        // Refresh interval dari setting (dalam detik)
        $refreshInterval = (int) Setting::get('refresh_interval', '0');

        return view('dashboard', compact(
            'rooms', 'statusCounts', 'avgTemp', 'avgHumidity',
            'activeAc', 'totalAc', 'currentPower', 'energyToday',
            'recentAlerts', 'displayFloor', 'roomDetailMap', 'refreshInterval'
        ));
    }

    public function byFloor(Floor $floor)
    {
        return redirect()->route('dashboard');
    }

    public function roomDetail(int $id): JsonResponse
    {
        $room      = Room::with('acUnits')->findOrFail($id);
        $latest    = SensorReadingLatest::where('room_id', $id)->first();
        $ac        = $room->acUnits->first();
        $connected = $latest !== null
                && $latest->waktu !== null
                && $latest->waktu->gte(now()->subMinutes(60));

        // Resolve kolom dinamis dari SensorParameter room ini
        $params = SensorParameter::where('room_id', $id)
            ->whereNotNull('kolom_reading')
            ->get(['nama_parameter', 'kolom_reading']);

        $resolveCol = function (string $keyword, string $fallback) use ($params): string {
            $match = $params->first(fn($p) => stripos($p->nama_parameter, $keyword) !== false);
            return $match?->kolom_reading ?? $fallback;
        };

        $colSuhu     = $resolveCol('suhu', $resolveCol('temp',   'sensor1'));
        $colHumidity = $resolveCol('kelembab', $resolveCol('humid', 'sensor2'));
        $colCo2      = $resolveCol('co2',    'sensor5');
        $colEnergy   = $resolveCol('energi', $resolveCol('energy', 'sensor3'));
        $colPower    = $resolveCol('daya',   $resolveCol('power',  'sensor4'));

        return response()->json([
            'id'               => $room->id,
            'name'             => $room->name,
            'status'           => $room->status,
            'updated_at'       => $latest?->waktu?->format('d/m/Y H:i') ?? $room->updated_at->format('d/m/Y H:i'),
            'temperature'      => $latest ? ['value' => $latest->{$colSuhu},     'unit' => '°C']  : null,
            'humidity'         => $latest ? ['value' => $latest->{$colHumidity}, 'unit' => '%']   : null,
            'co2'              => $latest ? ['value' => $latest->{$colCo2},      'unit' => 'ppm'] : null,
            'energy'           => $latest ? ['value' => $latest->{$colEnergy},   'unit' => 'kWh'] : null,
            'power'            => $latest ? ['value' => $latest->{$colPower},    'unit' => 'W']   : null,
            'ac_status'        => $ac ? ($ac->is_active ? 'ON' : 'OFF') : 'N/A',
            'sensor_connected' => $connected,
        ]);
    }

    /**
     * GET /api/dashboard/rooms-status
     * Return status + data sensor terbaru semua room (untuk live polling di dashboard).
     */
    public function roomsStatus(): JsonResponse
    {
        $offlineThreshold = now()->subMinutes(60);
        $rooms            = Room::select('id', 'name', 'status')->get();
        $latestReadings   = SensorReadingLatest::whereIn('room_id', $rooms->pluck('id'))->get()->keyBy('room_id');
        $allParams        = SensorParameter::whereIn('room_id', $rooms->pluck('id'))
            ->whereNotNull('kolom_reading')
            ->get(['room_id', 'nama_parameter', 'kolom_reading'])
            ->groupBy('room_id');

        $resolveCol = function ($rp, string $kw, string $fb) {
            if (!$rp) return $fb;
            $m = $rp->first(fn($p) => stripos($p->nama_parameter, $kw) !== false);
            return $m?->kolom_reading ?? $fb;
        };

        $statusCounts = ['normal' => 0, 'warning' => 0, 'poor' => 0];

        $roomStatuses = $rooms->map(function ($room) use ($latestReadings, $allParams, $resolveCol, $offlineThreshold, &$statusCounts) {
            $latest    = $latestReadings->get($room->id);
            $connected = $latest && $latest->waktu && $latest->waktu->gte($offlineThreshold);
            $rp        = $allParams->get($room->id);

            $colSuhu = $resolveCol($rp, 'suhu',    $resolveCol($rp, 'temp',   'sensor1'));
            $colHum  = $resolveCol($rp, 'kelembab', $resolveCol($rp, 'humid', 'sensor2'));
            $colCo2  = $resolveCol($rp, 'co2',     'sensor5');
            $colEn   = $resolveCol($rp, 'energi',  $resolveCol($rp, 'energy', 'sensor3'));
            $colPow  = $resolveCol($rp, 'daya',    $resolveCol($rp, 'power',  'sensor4'));

            $statusCounts[$room->status] = ($statusCounts[$room->status] ?? 0) + 1;

            return [
                'id'               => $room->id,
                'status'           => $room->status,
                'sensor_connected' => $connected,
                'updated_at'       => $latest?->waktu?->format('d/m/Y H:i'),
                'temperature'      => $latest ? ['value' => $latest->{$colSuhu}, 'unit' => '°C']  : null,
                'humidity'         => $latest ? ['value' => $latest->{$colHum},  'unit' => '%']   : null,
                'co2'              => $latest ? ['value' => $latest->{$colCo2},  'unit' => 'ppm'] : null,
                'energy'           => $latest ? ['value' => $latest->{$colEn},   'unit' => 'kWh'] : null,
                'power'            => $latest ? ['value' => $latest->{$colPow},  'unit' => 'W']   : null,
            ];
        })->keyBy('id');

        return response()->json([
            'rooms'         => $roomStatuses,
            'status_counts' => $statusCounts,
        ]);
    }
}
