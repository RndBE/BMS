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

        // Jika periode harian dan tanggal = hari ini → potong dateTo ke jam sekarang
        // supaya chart & tabel tidak menampilkan jam yang belum terjadi
        if ($periode === 'harian' && $date->isSameDay(Carbon::today())) {
            $dateTo = Carbon::now();
        }

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
        $todayTo   = Carbon::now()->toDateTimeString(); // sampai sekarang, bukan akhir hari

        // Daya saat ini = SUM latest sensor4 per ruangan (W → kW)
        $currentPower = round((float) DB::selectOne("
            SELECT COALESCE(SUM(sensor4), 0) AS total
            FROM sensor_reading_latests
            WHERE DATE(waktu) = CURDATE()
        ")->total / 1000, 1);

        // Energi akumulasi hari ini (sensor3)
        $energyToday = round((float) DB::selectOne("
            SELECT COALESCE(SUM(avg_e), 0) AS total
            FROM (
                SELECT AVG(sensor3) AS avg_e FROM sensor_readings
                WHERE waktu BETWEEN ? AND ? GROUP BY room_id
            ) sub
        ", [$todayFrom, $todayTo])->total, 1);

        // Daya puncak hari ini  (sensor4)
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

        // Tegangan saat ini = AVG latest sensor13 per ruangan (V)
        $currentVoltage = round((float) DB::selectOne("
            SELECT COALESCE(AVG(sensor13), 0) AS avg_v
            FROM sensor_reading_latests
            WHERE DATE(waktu) = CURDATE()
        ")->avg_v, 1);

        // Tegangan puncak hari ini = MAX rata-rata tegangan per slot waktu
        $peakVoltage = round((float) DB::selectOne("
            SELECT COALESCE(MAX(slot_avg), 0) AS peak FROM (
                SELECT AVG(sensor13) AS slot_avg
                FROM sensor_readings WHERE waktu BETWEEN ? AND ?
                GROUP BY DATE_FORMAT(waktu, '%H:%i')
            ) sub
        ", [$todayFrom, $todayTo])->peak, 1);

        // Tegangan rata-rata hari ini
        $avgVoltage = round((float) DB::selectOne("
            SELECT COALESCE(AVG(sensor13), 0) AS avg_v
            FROM sensor_readings WHERE waktu BETWEEN ? AND ?
        ", [$todayFrom, $todayTo])->avg_v, 1);

        $parameterLabels = ['power' => 'Daya', 'voltage' => 'Tegangan', 'energy' => 'Energi'];
        $parameterUnits  = ['power' => 'kW',   'voltage' => 'V',        'energy' => 'kWh'];
        $unit            = $parameterUnits[$parameter] ?? 'kW';
        $paramLabel      = $parameterLabels[$parameter] ?? 'Daya';

        // ── Stat card definitions per parameter ───────────────────────────────
        $statCardData = match ($parameter) {
            'voltage' => [
                ['icon' => 'energi/tegangan_saat_ini.svg', 'bg' => '#E8E6FE', 'iconColor' => '#7C3AED',
                 'label' => 'Tegangan Saat Ini',    'value' => $currentVoltage . ' V'],
                ['icon' => 'energi/energi.svg',           'bg' => '#EBFAEF', 'iconColor' => '#16A34A',
                 'label' => 'Energi Hari Ini',       'value' => number_format($energyToday, 1) . ' kWh'],
                ['icon' => 'energi/tegangan_puncak.svg',  'bg' => '#FFE1E2', 'iconColor' => '#FF383C',
                 'label' => 'Tegangan Puncak Hari Ini', 'value' => $peakVoltage . ' V'],
                ['icon' => 'energi/rerata_beban.svg',     'bg' => '#D9F7F4', 'iconColor' => '#00C8B3',
                 'label' => 'Rata-Rata Beban',       'value' => number_format($avgLoad, 1) . ' kW'],
                ['icon' => 'energi/tegangan_rerata.svg',  'bg' => '#E8E6FE', 'iconColor' => '#7C3AED',
                 'label' => 'Tegangan Rata-Rata',    'value' => $avgVoltage . ' V'],
            ],
            default => [ // 'power'
                ['icon' => 'energi/daya_saat_ini.svg', 'bg' => '#FFF5CC', 'iconColor' => '#FFCC00',
                 'label' => 'Daya Saat Ini',         'value' => number_format($currentPower, 1) . ' kW'],
                ['icon' => 'energi/energi.svg',        'bg' => '#EBFAEF', 'iconColor' => '#16A34A',
                 'label' => 'Energi Hari Ini',        'value' => number_format($energyToday, 1) . ' kWh'],
                ['icon' => 'energi/daya_puncak.svg',   'bg' => '#FFE1E2', 'iconColor' => '#FF383C',
                 'label' => 'Daya Puncak Hari Ini',   'value' => number_format($peakPower, 1) . ' kW'],
                ['icon' => 'energi/rerata_beban.svg',  'bg' => '#D9F7F4', 'iconColor' => '#00C8B3',
                 'label' => 'Rata-Rata Beban',        'value' => number_format($avgLoad, 1) . ' kW'],
                ['icon' => 'energi/tegangan_rerata.svg','bg' => '#E8E6FE', 'iconColor' => '#7C3AED',
                 'label' => 'Tegangan Rata-Rata',     'value' => $avgVoltage . ' V'],
            ],
        };


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

        // ── Tabel Data & Chart: akumulasi per slot, div 1000 untuk kW ──────────
        $divisor = ($parameter === 'power') ? 1000 : 1;

        // Untuk periode harian: generate semua label jam dari 00:00 s.d. jam terakhir dateTo
        // sehingga chart & tabel selalu rapi dan tidak loncat jam
        if ($periode === 'harian') {
            // Jam akhir: jam dateTo (jam sekarang jika hari ini, atau 23 jika hari lain)
            $endHour   = (int) $dateTo->format('H');
            $allLabels = [];
            for ($h = 0; $h <= $endHour; $h++) {
                $allLabels[] = sprintf('%02d:00', $h);
            }

            // Index chartData hasil query berdasarkan label
            $indexed = $chartData->keyBy('label');

            // Chart: semua label, slot tanpa data diisi null (garis putus)
            $chartLabels = collect($allLabels);
            $chartValues = $chartLabels->map(
                fn($l) => $indexed->has($l)
                    ? round((float) $indexed[$l]->value / $divisor, 1)
                    : null
            );

            // Tabel: hanya slot yang benar-benar ada data
            $tableData = $chartLabels->map(function ($l) use ($indexed, $th, $divisor) {
                if (! $indexed->has($l)) return null;
                $val    = round((float) $indexed[$l]->value / $divisor, 1);
                $status = 'normal';
                if ($val > $th['warn_upper'])   $status = 'poor';
                elseif ($val > $th['normal_max']) $status = 'warning';
                return ['waktu' => $l, 'nilai' => $val, 'status' => $status];
            })->filter()->values();

        } else {
            // Periode mingguan / bulanan / tahunan: tampilkan semua slot dari query
            $chartLabels = $chartData->pluck('label');
            $chartValues = $chartData->map(fn($r) => round((float) $r->value / $divisor, 1));
            $tableData   = $chartData->map(function ($row) use ($th, $divisor) {
                $val    = round((float) $row->value / $divisor, 1);
                $status = 'normal';
                if ($val > $th['warn_upper'])   $status = 'poor';
                elseif ($val > $th['normal_max']) $status = 'warning';
                return ['waktu' => $row->label, 'nilai' => $val, 'status' => $status];
            });
        }

        return view('energi.index', compact(
            'parameter', 'periode', 'tanggal', 'date',
            'chartLabels', 'chartValues',
            'parameterLabels', 'parameterUnits',
            'unit', 'paramLabel', 'th',
            'currentPower', 'energyToday', 'peakPower', 'avgLoad',
            'statCardData', 'alertLimit', 'alerts', 'tableData'
        ));
    }
}
