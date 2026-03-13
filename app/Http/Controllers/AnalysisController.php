<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Room;
use App\Models\SensorReading;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::orderBy('name')->get();

        // Default: room pertama, parameter temperature, harian, hari ini
        $selectedRoomId = $request->get('room_id', $rooms->first()?->id);
        $parameter      = $request->get('parameter', 'temperature');
        $periode        = $request->get('periode', 'harian');
        $tanggal        = $request->get('tanggal', Carbon::today()->format('d/m/Y'));

        $selectedRoom = $rooms->find($selectedRoomId);

        // Parse tanggal
        try {
            $date = Carbon::createFromFormat('d/m/Y', $tanggal)->startOfDay();
        } catch (\Exception $e) {
            $date = Carbon::today()->startOfDay();
        }

        // Tentukan rentang waktu berdasarkan periode
        [$dateFrom, $dateTo, $groupFormat, $labelFormat] = match ($periode) {
            'mingguan' => [
                $date->copy()->startOfWeek(),
                $date->copy()->endOfWeek(),
                '%Y-%m-%d', 'd/m',
            ],
            'bulanan' => [
                $date->copy()->startOfMonth(),
                $date->copy()->endOfMonth(),
                '%Y-%m-%d', 'd/m',
            ],
            default => [  // harian
                $date->copy()->startOfDay(),
                $date->copy()->endOfDay(),
                '%H:00', 'H:i',
            ],
        };

        // Query chart data (group by jam/hari)
        $chartData = SensorReading::where('room_id', $selectedRoomId)
            ->whereBetween('waktu', [$dateFrom, $dateTo])
            ->selectRaw("DATE_FORMAT(waktu, '{$groupFormat}') as label, AVG({$parameter}) as value, MAX(waktu) as last_waktu")
            ->groupByRaw("DATE_FORMAT(waktu, '{$groupFormat}')")
            ->orderBy('last_waktu')
            ->get();

        // Stats
        $statsQuery = SensorReading::where('room_id', $selectedRoomId)
            ->whereBetween('waktu', [$dateFrom, $dateTo]);

        $latest  = SensorReading::where('room_id', $selectedRoomId)->orderByDesc('waktu')->value($parameter);
        $average = round((float) $statsQuery->avg($parameter), 1);
        $max     = round((float) $statsQuery->max($parameter), 1);
        $min     = round((float) $statsQuery->min($parameter), 1);

        // Batas normal per parameter
        $thresholds = [
            'temperature' => ['normal_min' => 23,  'normal_max' => 26,   'warn_lower' => 21,  'warn_upper' => 28],
            'humidity'    => ['normal_min' => 40,  'normal_max' => 60,   'warn_lower' => 30,  'warn_upper' => 70],
            'energy'      => ['normal_min' => 0,   'normal_max' => 100,  'warn_lower' => 0,   'warn_upper' => 150],
            'power'       => ['normal_min' => 0,   'normal_max' => 3000, 'warn_lower' => 0,   'warn_upper' => 5000],
            'co2'         => ['normal_min' => 400, 'normal_max' => 800,  'warn_lower' => 350, 'warn_upper' => 1200],
        ];

        // Peringatan terkait ruangan ini
        $alerts = Alert::with('room')
            ->where('room_id', $selectedRoomId)
            ->latest()
            ->limit(6)
            ->get();

        // Tabel data parameter utama (per jam/interval)
        $th = $thresholds[$parameter] ?? $thresholds['temperature'];
        $tableData = SensorReading::where('room_id', $selectedRoomId)
            ->whereBetween('waktu', [$dateFrom, $dateTo])
            ->orderBy('waktu')
            ->get(['waktu', $parameter]);

        $tableData = $tableData->map(function ($row) use ($parameter, $th) {
            $val = (float) $row->$parameter;
            $status = 'normal';
            if ($val < $th['warn_lower'] || $val > $th['warn_upper']) {
                $status = 'poor';
            } elseif ($val < $th['normal_min'] || $val > $th['normal_max']) {
                $status = 'warning';
            }
            return [
                'waktu'  => $row->waktu,
                'nilai'  => round($val, 1),
                'status' => $status,
            ];
        });

        // ── Tabel Energy (selalu tampil, terpisah) ──────────────────────────────
        $thEnergy = $thresholds['energy'];
        $energyTableData = SensorReading::where('room_id', $selectedRoomId)
            ->whereBetween('waktu', [$dateFrom, $dateTo])
            ->orderBy('waktu')
            ->get(['waktu', 'energy']);

        $energyTableData = $energyTableData->map(function ($row) use ($thEnergy) {
            $val = (float) $row->energy;
            $status = 'normal';
            if ($val < $thEnergy['warn_lower'] || $val > $thEnergy['warn_upper']) {
                $status = 'poor';
            } elseif ($val < $thEnergy['normal_min'] || $val > $thEnergy['normal_max']) {
                $status = 'warning';
            }
            return [
                'waktu'  => $row->waktu,
                'nilai'  => round($val, 2),
                'status' => $status,
            ];
        });

        $parameterLabels = [
            'temperature' => 'Suhu',
            'humidity'    => 'Kelembapan',
            'energy'      => 'Energi',
            'power'       => 'Daya',
            'co2'         => 'CO₂',
        ];
        $parameterUnits = [
            'temperature' => '°C',
            'humidity'    => '%',
            'energy'      => 'kWh',
            'power'       => 'W',
            'co2'         => 'ppm',
        ];

        return view('analisa-data.index', compact(
            'rooms', 'selectedRoom', 'selectedRoomId',
            'parameter', 'periode', 'tanggal', 'date',
            'chartData', 'latest', 'average', 'max', 'min',
            'thresholds', 'alerts', 'tableData', 'energyTableData',
            'parameterLabels', 'parameterUnits'
        ));
    }
}
