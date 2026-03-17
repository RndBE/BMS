<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\AlertLimit;
use App\Models\SensorReading;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnergyController extends Controller
{
    public function index(Request $request)
    {
        $parameter = $request->get('parameter', 'power'); // power, voltage, atau energy
        $periode   = $request->get('periode', 'harian');
        $tanggal   = $request->get('tanggal', Carbon::today()->format('d/m/Y'));

        // Parse tanggal
        try {
            $date = Carbon::createFromFormat('d/m/Y', $tanggal)->startOfDay();
        } catch (\Exception $e) {
            $date = Carbon::today()->startOfDay();
        }

        // Tentukan rentang waktu berdasarkan periode
        [$dateFrom, $dateTo, $groupFormat] = match ($periode) {
            'mingguan' => [
                $date->copy()->startOfWeek(),
                $date->copy()->endOfWeek(),
                '%Y-%m-%d',
            ],
            'bulanan' => [
                $date->copy()->startOfMonth(),
                $date->copy()->endOfMonth(),
                '%Y-%m-%d',
            ],
            'tahunan' => [
                $date->copy()->startOfYear(),
                $date->copy()->endOfYear(),
                '%Y-%m',
            ],
            default => [  // harian
                $date->copy()->startOfDay(),
                $date->copy()->endOfDay(),
                '%H:00',
            ],
        };

        // Pemetaan parameter ke kolom sensor_readings
        // power  → sensor4 (Daya W)
        // energy → sensor3 (Energi kWh)
        $columnMap = ['power' => 'sensor4', 'voltage' => 'sensor3', 'energy' => 'sensor3'];
        $column    = $columnMap[$parameter] ?? 'sensor4';

        // ── Chart Data: akumulasi seluruh gedung ──────────────────────────────────────────
        // Step 1: AVG per ruangan per slot → Step 2: SUM semua ruangan
        $chartData = DB::select("
            SELECT label, SUM(avg_val) AS value
            FROM (
                SELECT DATE_FORMAT(waktu, '{$groupFormat}') AS label,
                       AVG({$column}) AS avg_val
                FROM sensor_readings
                WHERE waktu BETWEEN ? AND ?
                GROUP BY room_id, DATE_FORMAT(waktu, '{$groupFormat}')
            ) sub
            GROUP BY label
            ORDER BY label
        ", [$dateFrom, $dateTo]);

        $chartData = collect($chartData);

        // ── Thresholds per parameter (skala gedung) ───────────────────────────
        $thresholds = [
            'power'   => ['normal_min' => 0, 'normal_max' => 10,  'warn_lower' => 0, 'warn_upper' => 15],
            'voltage' => ['normal_min' => 210, 'normal_max' => 230, 'warn_lower' => 200, 'warn_upper' => 240],
            'energy'  => ['normal_min' => 0, 'normal_max' => 100, 'warn_lower' => 0, 'warn_upper' => 150],
        ];
        $th = $thresholds[$parameter] ?? $thresholds['power'];

        // ── Stat Cards — akumulasi hari ini ───────────────────────────────────
        $todayFrom = Carbon::today()->startOfDay()->toDateTimeString();
        $todayTo   = Carbon::today()->endOfDay()->toDateTimeString();

        // Daya saat ini = SUM latest sensor4 per ruangan (W → kW)
        $currentPower = round((float) DB::selectOne("
            SELECT COALESCE(SUM(sensor4), 0) AS total
            FROM sensor_readings
            WHERE id IN (
                SELECT MAX(id) FROM sensor_readings GROUP BY room_id
            )
        ")->total / 1000, 1);

        // Energi akumulasi hari ini (sensor3)
        $energyToday = round((float) DB::selectOne("
            SELECT COALESCE(SUM(avg_e), 0) AS total
            FROM (
                SELECT AVG(sensor3) AS avg_e FROM sensor_readings
                WHERE waktu BETWEEN ? AND ? GROUP BY room_id
            ) sub
        ", [$todayFrom, $todayTo])->total, 1);

        // Daya puncak hari ini = max dari sum akumulasi per slot waktu (sensor4)
        $peakPower = round((float) DB::selectOne("
            SELECT COALESCE(MAX(slot_sum), 0) AS peak FROM (
                SELECT SUM(avg_p) AS slot_sum FROM (
                    SELECT room_id, DATE_FORMAT(waktu, '%H:%i') AS slot, AVG(sensor4) AS avg_p
                    FROM sensor_readings WHERE waktu BETWEEN ? AND ?
                    GROUP BY room_id, DATE_FORMAT(waktu, '%H:%i')
                ) inner_q GROUP BY inner_q.slot
            ) outer_q
        ", [$todayFrom, $todayTo])->peak / 1000, 1);

        // Rata-rata beban hari ini (sensor4)
        $avgLoad = round((float) DB::selectOne("
            SELECT COALESCE(AVG(slot_sum), 0) AS avg_load FROM (
                SELECT SUM(avg_p) AS slot_sum FROM (
                    SELECT room_id, DATE_FORMAT(waktu, '%H:%i') AS slot, AVG(sensor4) AS avg_p
                    FROM sensor_readings WHERE waktu BETWEEN ? AND ?
                    GROUP BY room_id, DATE_FORMAT(waktu, '%H:%i')
                ) inner_q GROUP BY inner_q.slot
            ) outer_q
        ", [$todayFrom, $todayTo])->avg_load / 1000, 1);

        $parameterLabels = ['power' => 'Daya', 'voltage' => 'Tegangan', 'energy' => 'Energi'];
        $parameterUnits  = ['power' => 'kW',   'voltage' => 'V',        'energy' => 'kWh'];
        $unit            = $parameterUnits[$parameter] ?? 'kW';
        $paramLabel      = $parameterLabels[$parameter] ?? 'Daya';

        // ── Map parameter energi → parameter_key di AlertLimit ─────────────
        $paramKeyMap = [
            'power'   => 'daya',
            'voltage' => 'tegangan',
            'energy'  => 'energi',
        ];
        $limitsFromDb = AlertLimit::all()->keyBy('parameter_key');
        $alertLimit   = $limitsFromDb->get($paramKeyMap[$parameter] ?? 'daya');

        // Fallback $thresholds untuk tabel status (backward compat)
        if ($alertLimit) {
            $th = [
                'normal_min' => $alertLimit->normal_min ?? 0,
                'normal_max' => $alertLimit->normal_max ?? 10,
                'warn_lower' => $alertLimit->warn_low_min ?? 0,
                'warn_upper' => $alertLimit->warn_high_max ?? 15,
            ];
        } else {
            $th = $thresholds[$parameter] ?? $thresholds['power'];
        }

        // ── Alerts terkait (seluruh gedung) ───────────────────────────────────
        $alerts = Alert::with('room')->latest()->limit(6)->get();

        // ── Tabel Data: akumulasi per slot, div 1000 untuk kW ─────────────────
        $divisor = ($parameter === 'power') ? 1000 : 1;
        $tableData = $chartData->map(function ($row) use ($th, $unit, $divisor) {
            $val    = round((float) $row->value / $divisor, 1);
            $status = 'normal';
            if ($val > $th['warn_upper']) {
                $status = 'poor';
            } elseif ($val > $th['normal_max']) {
                $status = 'warning';
            }
            return [
                'waktu'  => $row->label,
                'nilai'  => $val,
                'status' => $status,
            ];
        });

        // Chart values juga perlu dibagi supaya skala sama dengan tabel
        $chartValues = $chartData->map(fn($r) => round((float) $r->value / $divisor, 1));
        $chartLabels = $chartData->pluck('label');

        return view('energi.index', compact(
            'parameter', 'periode', 'tanggal', 'date',
            'chartLabels', 'chartValues',
            'parameterLabels', 'parameterUnits',
            'unit', 'paramLabel', 'th',
            'currentPower', 'energyToday', 'peakPower', 'avgLoad',
            'alertLimit', 'alerts', 'tableData'
        ));
    }
}
