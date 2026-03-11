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
        $rooms = Room::with(['sensors.readings' => function ($q) {
            $q->latest('recorded_at')->limit(1);
        }, 'acUnits'])->get();

        // Summary stats
        $statusCounts = [
            'normal'  => $rooms->where('status', 'normal')->count(),
            'warning' => $rooms->where('status', 'warning')->count(),
            'poor'    => $rooms->where('status', 'poor')->count(),
        ];

        $avgTemp = SensorReading::whereHas('sensor', fn($q) => $q->where('type', 'temperature'))
            ->latest('recorded_at')->get()->groupBy('sensor_id')
            ->map(fn($r) => $r->first()->value)->avg();

        $avgHumidity = SensorReading::whereHas('sensor', fn($q) => $q->where('type', 'humidity'))
            ->latest('recorded_at')->get()->groupBy('sensor_id')
            ->map(fn($r) => $r->first()->value)->avg();

        $activeAc    = AcUnit::where('is_active', true)->count();
        $totalAc     = AcUnit::count();
        $currentPower = AcUnit::where('is_active', true)->sum('power_kw');
        $energyToday  = round($currentPower * 11.5, 1);

        $recentAlerts = Alert::with('room')->latest()->limit(4)->get();

        // Load first available floor with canvas_data for dashboard display
        $displayFloor = Floor::with([
            'building',
            'rooms.sensors.readings' => function ($q) {
                $q->latest('recorded_at')->limit(1);
            },
            'rooms.acUnits',
        ])->where(function ($q) {
            $q->whereNotNull('plan_file_path')->orWhereNotNull('canvas_data');
        })->latest()->first();

        // Pre-build all room detail data as JSON (eliminates AJAX roundtrip)
        $roomDetailMap = $rooms->mapWithKeys(function ($room) {
            $readings = [];
            foreach ($room->sensors as $sensor) {
                $latest = $sensor->readings->first();
                $readings[$sensor->type] = [
                    'value'     => $latest ? $latest->value : null,
                    'unit'      => $sensor->unit,
                    'is_active' => $sensor->is_active,
                ];
            }
            $ac = $room->acUnits->first();
            return [$room->id => [
                'id'               => $room->id,
                'name'             => $room->name,
                'status'           => $room->status,
                'updated_at'       => $room->updated_at->format('d/m/Y H:i'),
                'temperature'      => $readings['temperature'] ?? null,
                'humidity'         => $readings['humidity'] ?? null,
                'co2'              => $readings['co2'] ?? null,
                'ac_status'        => $ac ? ($ac->is_active ? 'ON' : 'OFF') : 'N/A',
                'sensor_connected' => $room->sensors->where('is_active', true)->count() > 0,
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
        $room = Room::with(['sensors.readings' => function ($q) {
            $q->latest('recorded_at')->limit(1);
        }, 'acUnits'])->findOrFail($id);

        $readings = [];
        foreach ($room->sensors as $sensor) {
            $latest = $sensor->readings->first();
            $readings[$sensor->type] = [
                'value'     => $latest ? $latest->value : null,
                'unit'      => $sensor->unit,
                'is_active' => $sensor->is_active,
            ];
        }
        $ac = $room->acUnits->first();

        return response()->json([
            'id'               => $room->id,
            'name'             => $room->name,
            'status'           => $room->status,
            'updated_at'       => $room->updated_at->format('d/m/Y H:i'),
            'temperature'      => $readings['temperature'] ?? null,
            'humidity'         => $readings['humidity'] ?? null,
            'co2'              => $readings['co2'] ?? null,
            'ac_status'        => $ac ? ($ac->is_active ? 'ON' : 'OFF') : 'N/A',
            'sensor_connected' => $room->sensors->where('is_active', true)->count() > 0,
        ]);
    }
}
