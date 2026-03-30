<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\AlertLimit;
use App\Models\AlertRule;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PeringatanController extends Controller
{
    // ── Default parameter definitions ────────────────────────────────────────
    private const DEFAULTS = [
        'suhu'       => ['label' => 'Suhu (°C)',       'icon' => 'icons/suhu.svg',       'icon_type' => 'img'],
        'kelembaban' => ['label' => 'Kelembaban (%)',   'icon' => 'icons/kelembapan.svg',  'icon_type' => 'img'],
        'co2'        => ['label' => 'CO₂ (ppm)',        'icon' => 'icons/co2.svg',         'icon_type' => 'img'],
        'daya'       => ['label' => 'Daya (kW)',        'icon' => 'icons/daya.svg',        'icon_type' => 'img'],
        'tegangan'   => ['label' => 'Tegangan (Volt)',  'icon' => 'icons/tegangan.svg',     'icon_type' => 'img'],
    ];

    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $tab = $request->get('tab', 'batas-normal');

        // Load / seed alert limits
        $limits = [];
        foreach (self::DEFAULTS as $key => $meta) {
            $limits[$key] = AlertLimit::firstOrCreate(
                ['parameter_key' => $key],
                array_merge(['parameter_key' => $key], $meta)
            );
        }

        $rules = AlertRule::orderBy('created_at', 'desc')->get();
        $rooms = Room::orderBy('name')->get();

        $filterRoom = $request->get('room_id');
        $filterType = $request->get('severity');

        $alertsQuery = Alert::with('room')
            ->when($filterRoom, fn($q) => $q->where('room_id', $filterRoom))
            ->when($filterType,  fn($q) => $q->where('type', $filterType))
            ->orderByDesc('created_at');

        $alerts = $tab === 'log-peringatan'
            ? $alertsQuery->paginate(20)->withQueryString()
            : collect();

        return view('pengaturan.peringatan', compact('tab', 'limits', 'rules', 'rooms', 'alerts'));
    }

    // ── Batas Normal ──────────────────────────────────────────────────────────

    public function batasNormalSave(Request $request)
    {
        $request->validate([
            'limits'                    => 'required|array',
            'limits.*.parameter_key'    => 'required|string',
            'limits.*.normal_min'       => 'nullable|numeric',
            'limits.*.normal_max'       => 'nullable|numeric',
            'limits.*.warn_low_min'     => 'nullable|numeric',
            'limits.*.warn_low_max'     => 'nullable|numeric',
            'limits.*.warn_high_min'    => 'nullable|numeric',
            'limits.*.warn_high_max'    => 'nullable|numeric',
            'limits.*.poor_low'         => 'nullable|numeric',
            'limits.*.poor_high'        => 'nullable|numeric',
        ]);

        foreach ($request->input('limits') as $data) {
            AlertLimit::where('parameter_key', $data['parameter_key'])->update([
                'normal_min'    => $data['normal_min']    ?? null,
                'normal_max'    => $data['normal_max']    ?? null,
                'warn_low_min'  => $data['warn_low_min']  ?? null,
                'warn_low_max'  => $data['warn_low_max']  ?? null,
                'warn_high_min' => $data['warn_high_min'] ?? null,
                'warn_high_max' => $data['warn_high_max'] ?? null,
                'poor_low'      => $data['poor_low']      ?? null,
                'poor_high'     => $data['poor_high']     ?? null,
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function batasNormalReset()
    {
        foreach (self::DEFAULTS as $key => $meta) {
            AlertLimit::where('parameter_key', $key)->update([
                'normal_min' => null, 'normal_max' => null,
                'warn_low_min' => null, 'warn_low_max' => null,
                'warn_high_min' => null, 'warn_high_max' => null,
                'poor_low' => null, 'poor_high' => null,
            ]);
        }
        return response()->json(['success' => true]);
    }

    // ── Aturan Peringatan ─────────────────────────────────────────────────────

    public function rulesStore(Request $request)
    {
        $data = $request->validate([
            'name'                 => 'required|string|max:100',
            'kategori'             => 'nullable|string|max:100',
            'parameter_key'        => 'required|string',
            'condition'            => 'required|in:>,<,>=,<=,==,!=',
            'threshold'            => 'required|numeric',
            'severity'             => 'required|in:warning,critical',
            'notification_channel' => 'nullable|string|max:50',
            'durasi_tunda'         => 'nullable|integer|min:0',
            'room_ids'             => 'nullable|array',
            'room_ids.*'           => 'integer',
            'is_active'            => 'nullable|boolean',
        ]);
        $data['is_active']  = $request->boolean('is_active', true);
        $data['room_ids']   = $request->input('room_ids', []);

        $rule = AlertRule::create($data);
        Cache::forget('alert_rules_active'); // flush cache Observer
        return response()->json(['success' => true, 'rule' => $rule]);
    }

    public function rulesUpdate(Request $request, AlertRule $alertRule)
    {
        $data = $request->validate([
            'name'                 => 'required|string|max:100',
            'kategori'             => 'nullable|string|max:100',
            'parameter_key'        => 'required|string',
            'condition'            => 'required|in:>,<,>=,<=,==,!=',
            'threshold'            => 'required|numeric',
            'severity'             => 'required|in:warning,critical',
            'notification_channel' => 'nullable|string|max:50',
            'durasi_tunda'         => 'nullable|integer|min:0',
            'room_ids'             => 'nullable|array',
            'room_ids.*'           => 'integer',
            'is_active'            => 'nullable|boolean',
        ]);
        $data['is_active']  = $request->boolean('is_active', true);
        $data['room_ids']   = $request->input('room_ids', []);

        $alertRule->update($data);
        Cache::forget('alert_rules_active'); // flush cache Observer
        return response()->json(['success' => true, 'rule' => $alertRule->fresh()]);
    }

    public function rulesDestroy(AlertRule $alertRule)
    {
        $alertRule->delete();
        Cache::forget('alert_rules_active'); // flush cache Observer
        return response()->json(['success' => true]);
    }

    public function rulesToggle(AlertRule $alertRule)
    {
        $alertRule->update(['is_active' => !$alertRule->is_active]);
        Cache::forget('alert_rules_active'); // flush cache Observer
        return response()->json(['success' => true, 'is_active' => $alertRule->is_active]);
    }

    // ── Log Peringatan ────────────────────────────────────────────────────────

    public function logIndex(Request $request)
    {
        $rooms    = Room::orderBy('name')->get();
        $kategori = AlertRule::whereNotNull('kategori')->distinct()->orderBy('kategori')->pluck('kategori');

        $query = Alert::with(['room', 'alertRule'])
            ->when($request->room_id,   fn($q) => $q->where('room_id', $request->room_id))
            ->when($request->severity === 'critical', fn($q) => $q->where('type', 'critical'))
            ->when($request->severity === 'warning',  fn($q) => $q->where('type', '!=', 'critical'))
            ->when($request->kategori,  fn($q) => $q->whereHas('alertRule', fn($r) => $r->where('kategori', $request->kategori)))
            ->when($request->search,    fn($q) => $q->where('message', 'like', '%'.$request->search.'%')
                ->orWhereHas('alertRule', fn($r) => $r->where('name', 'like', '%'.$request->search.'%')))
            ->when($request->waktu !== 'semua', function ($q) use ($request) {
                $days = match($request->waktu) {
                    '7hari'  => 7,
                    '30hari' => 30,
                    default  => 1,   // hari_ini
                };
                $q->where('created_at', '>=', now()->subDays($days)->startOfDay());
            })
            ->orderByDesc('created_at');

        $alerts = $query->paginate(10)->withQueryString();

        return view('log-peringatan', compact('alerts', 'rooms', 'kategori'));
    }

    public function logMarkRead(Alert $alert)
    {
        $alert->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function logMarkAllRead()
    {
        Alert::where('is_read', false)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function logDestroy(Alert $alert)
    {
        $alert->delete();
        return response()->json(['success' => true]);
    }

    public function logClear()
    {
        Alert::query()->delete();
        return response()->json(['success' => true]);
    }
}
