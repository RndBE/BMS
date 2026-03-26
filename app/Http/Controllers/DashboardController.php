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

        // Pre-build all room detail data as JSON (eliminates AJAX roundtrip)
        $roomDetailMap = $rooms->mapWithKeys(function ($room) use ($latestReadings, $offlineThreshold) {
            $latest    = $latestReadings->get($room->id);
            $ac        = $room->acUnits->first();
            $connected = $latest !== null
                      && $latest->waktu !== null
                      && $latest->waktu->gte($offlineThreshold);

            return [$room->id => [
                'id'               => $room->id,
                'name'             => $room->name,
                'status'           => $room->status,
                'updated_at'       => $latest?->waktu?->format('d/m/Y H:i') ?? $room->updated_at->format('d/m/Y H:i'),
                'temperature'      => $latest ? ['value' => $latest->sensor1, 'unit' => '°C']   : null,
                'humidity'         => $latest ? ['value' => $latest->sensor2, 'unit' => '%']     : null,
                'co2'              => $latest ? ['value' => $latest->sensor5, 'unit' => 'ppm']   : null,
                'energy'           => $latest ? ['value' => $latest->sensor3, 'unit' => 'kWh']  : null,
                'power'            => $latest ? ['value' => $latest->sensor4, 'unit' => 'W']    : null,
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

        return response()->json([
            'id'               => $room->id,
            'name'             => $room->name,
            'status'           => $room->status,
            'updated_at'       => $latest?->waktu?->format('d/m/Y H:i') ?? $room->updated_at->format('d/m/Y H:i'),
            'temperature'      => $latest ? ['value' => $latest->sensor1, 'unit' => '°C']   : null,
            'humidity'         => $latest ? ['value' => $latest->sensor2, 'unit' => '%']     : null,
            'co2'              => $latest ? ['value' => $latest->sensor5, 'unit' => 'ppm']   : null,
            'energy'           => $latest ? ['value' => $latest->sensor3, 'unit' => 'kWh']  : null,
            'power'            => $latest ? ['value' => $latest->sensor4, 'unit' => 'W']    : null,
            'ac_status'        => $ac ? ($ac->is_active ? 'ON' : 'OFF') : 'N/A',
            'sensor_connected' => $connected,
        ]);
    }
}
