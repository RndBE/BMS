<?php

namespace App\Http\Controllers;

use App\Models\AcUnit;
use App\Models\Alert;
use App\Models\Floor;
use App\Models\Room;
use App\Models\SensorReading;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Load rooms dengan latest sensor reading per room
        $rooms = Room::with(['latestReading', 'acUnits'])->get();

        // Summary stats dari collection (tidak perlu query lagi)
        $statusCounts = [
            'normal'  => $rooms->where('status', 'normal')->count(),
            'warning' => $rooms->where('status', 'warning')->count(),
            'poor'    => $rooms->where('status', 'poor')->count(),
        ];

        // ── Avg sensor: ambil latest reading per room, lalu avg ──
        $avgTemp = SensorReading::whereIn('id', function ($q) {
            $q->selectRaw('MAX(id)')
              ->from('sensor_readings')
              ->groupBy('room_id');
        })->avg('temperature');

        $avgHumidity = SensorReading::whereIn('id', function ($q) {
            $q->selectRaw('MAX(id)')
              ->from('sensor_readings')
              ->groupBy('room_id');
        })->avg('humidity');

        $activeAc     = AcUnit::where('is_active', true)->count();
        $totalAc      = AcUnit::count();
        $currentPower = AcUnit::where('is_active', true)->sum('power_kw');
        $energyToday  = round($currentPower * 11.5, 1);

        $recentAlerts = Alert::with('room')->latest()->limit(4)->get();

        // Load floor untuk denah
        $displayFloor = Floor::with(['building', 'rooms.acUnits'])
            ->where(function ($q) {
                $q->whereNotNull('plan_file_path')->orWhereNotNull('canvas_data');
            })->latest()->first();

        // Pre-build all room detail data as JSON (eliminates AJAX roundtrip)
        $roomDetailMap = $rooms->mapWithKeys(function ($room) {
            $latest = $room->latestReading;
            $ac     = $room->acUnits->first();
            return [$room->id => [
                'id'               => $room->id,
                'name'             => $room->name,
                'status'           => $room->status,
                'updated_at'       => $room->updated_at->format('d/m/Y H:i'),
                'temperature'      => $latest ? ['value' => $latest->temperature, 'unit' => '°C'] : null,
                'humidity'         => $latest ? ['value' => $latest->humidity,    'unit' => '%']  : null,
                'energy'           => $latest ? ['value' => $latest->energy,      'unit' => 'kWh'] : null,
                'power'            => $latest ? ['value' => $latest->power,       'unit' => 'W']  : null,
                'ac_status'        => $ac ? ($ac->is_active ? 'ON' : 'OFF') : 'N/A',
                'sensor_connected' => $latest !== null,
            ]];
        });

        return view('dashboard', compact(
            'rooms', 'statusCounts', 'avgTemp', 'avgHumidity',
            'activeAc', 'totalAc', 'currentPower', 'energyToday',
            'recentAlerts', 'displayFloor', 'roomDetailMap'
        ));
    }

    public function byFloor(Floor $floor)
    {
        return redirect()->route('dashboard');
    }

    public function roomDetail(int $id): JsonResponse
    {
        $room   = Room::with(['latestReading', 'acUnits'])->findOrFail($id);
        $latest = $room->latestReading;
        $ac     = $room->acUnits->first();

        return response()->json([
            'id'               => $room->id,
            'name'             => $room->name,
            'status'           => $room->status,
            'updated_at'       => $room->updated_at->format('d/m/Y H:i'),
            'temperature'      => $latest ? ['value' => $latest->temperature, 'unit' => '°C'] : null,
            'humidity'         => $latest ? ['value' => $latest->humidity,    'unit' => '%']  : null,
            'energy'           => $latest ? ['value' => $latest->energy,      'unit' => 'kWh'] : null,
            'power'            => $latest ? ['value' => $latest->power,       'unit' => 'W']  : null,
            'ac_status'        => $ac ? ($ac->is_active ? 'ON' : 'OFF') : 'N/A',
            'sensor_connected' => $latest !== null,
        ]);
    }
}
