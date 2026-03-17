<?php

namespace App\Http\Controllers;

use App\Models\AcUnit;
use App\Models\Room;
use App\Models\Sensor;
use App\Models\SensorGroup;
use App\Models\SensorParameter;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    public function umum()
    {
        return view('pengaturan.umum', [
            'site_name'        => Setting::get('site_name',        'Beacon Engineering'),
            'timezone'         => Setting::get('timezone',         'Asia/Jakarta'),
            'date_format'      => Setting::get('date_format',      'DD/MM/YYYY'),
            'time_format'      => Setting::get('time_format',      '24'),
            'refresh_interval' => Setting::get('refresh_interval', '86400'),
            'default_range'    => Setting::get('default_range',    'harian'),
        ]);
    }

    public function umumSave(Request $request)
    {
        $request->validate([
            'site_name'        => 'required|string|max:100',
            'timezone'         => 'required|string',
            'date_format'      => 'required|string',
            'time_format'      => 'required|in:24,12',
            'refresh_interval' => 'required|string',
            'default_range'    => 'required|in:harian,mingguan,bulanan',
        ]);

        foreach (['site_name','timezone','date_format','time_format','refresh_interval','default_range'] as $key) {
            Setting::set($key, $request->input($key));
        }

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function konfigurasi(Request $request)
    {
        $tab    = $request->get('tab', 'ruangan');
        $search = $request->get('search', '');

        $rooms        = collect();
        $sensors      = collect();
        $acUnits      = collect();
        $allRooms     = collect();
        $sensorGroups = collect();

        if ($tab === 'ruangan') {
            $rooms = Room::with(['floor.building', 'sensors'])
                ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")
                                            ->orWhere('code', 'like', "%{$search}%"))
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate(10)
                ->withQueryString();
        }

        if ($tab === 'sensor') {
            $sensors = Sensor::with(['room.parameters', 'sensorGroup'])
                ->when($search, fn($q) => $q->whereHas('room', fn($r) => $r->where('name', 'like', "%{$search}%"))
                               ->orWhereHas('sensorGroup', fn($sg) => $sg->where('nama_sensor', 'like', "%{$search}%")))
                ->orderBy('id')
                ->paginate(10)
                ->withQueryString();

            // Data untuk dropdown di modal sensor
            $allRooms     = Room::orderBy('name')->get(['id', 'name', 'code']);
            $sensorGroups = SensorGroup::orderBy('sort_order')->get(['id', 'kode_sensor', 'nama_sensor']);
        }

        if ($tab === 'perangkat') {
            $acUnits = AcUnit::with('room')
                ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")
                                            ->orWhereHas('room', fn($r) => $r->where('name', 'like', "%{$search}%")))
                ->orderBy('room_id')
                ->orderBy('name')
                ->paginate(10)
                ->withQueryString();

            $allRooms = Room::orderBy('name')->get(['id', 'name', 'code']);
        }

        return view('pengaturan.konfigurasi', compact(
            'tab', 'search', 'rooms', 'sensors', 'acUnits', 'allRooms', 'sensorGroups'
        ));
    }

    // ── ROOM CRUD ────────────────────────────────────────────────────────────────

    public function roomStore(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'code'       => 'required|string|max:20|unique:rooms,code',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|boolean',
        ]);

        $data['is_active']  = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $room = Room::create($data);

        return response()->json(['success' => true, 'room' => $room]);
    }

    public function roomUpdate(Request $request, Room $room)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'code'       => 'required|string|max:20|unique:rooms,code,' . $room->id,
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|boolean',
        ]);

        $data['is_active']  = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $room->update($data);

        return response()->json(['success' => true, 'room' => $room->fresh()]);
    }

    public function roomDestroy(Room $room)
    {
        $room->delete();
        return response()->json(['success' => true]);
    }

    // ── SENSOR CRUD ──────────────────────────────────────────────────────────────

    public function sensorShow(Sensor $sensor)
    {
        $sensor->load(['sensorGroup', 'room.parameters']);
        return response()->json([
            'id'                 => $sensor->id,
            'room_id'            => $sensor->room_id,
            'room_name'          => $sensor->room?->name ?? '—',
            'sensor_group_id'    => $sensor->sensor_group_id,
            'sensor_group_name'  => $sensor->sensorGroup?->nama_sensor ?? '—',
            'sensor_group_code'  => $sensor->sensorGroup?->kode_sensor ?? '',
            'tipe_sensor'        => $sensor->tipe_sensor,
            'is_active'          => $sensor->is_active,
            'gambar_url'         => $sensor->gambar ? asset('storage/' . $sensor->gambar) : null,
            'parameters'         => $sensor->room?->parameters?->map(fn($p) => [
                'nama_parameter' => $p->nama_parameter,
                'kolom_reading'  => $p->kolom_reading,
                'unit'           => $p->unit,
            ]) ?? [],
        ]);
    }

    public function sensorStore(Request $request)
    {
        $request->validate([
            'room_id'          => 'required|exists:rooms,id',
            'sensor_group_id'  => 'nullable|exists:sensor_groups,id',
            'tipe_sensor'      => 'nullable|string|max:100',
            'gambar'           => 'nullable|image|max:2048',
            'is_active'        => 'nullable|boolean',
            'parameters'       => 'nullable|array',
            'parameters.*.nama_parameter' => 'required|string|max:100',
            'parameters.*.kolom_reading'  => 'required|string|max:20',
            'parameters.*.unit'           => 'nullable|string|max:20',
        ]);

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('sensors', 'public');
        }

        $sensor = Sensor::create([
            'room_id'         => $request->room_id,
            'sensor_group_id' => $request->sensor_group_id,
            'tipe_sensor'     => $request->tipe_sensor,
            'gambar'          => $gambarPath,
            'is_active'       => $request->boolean('is_active', true),
        ]);

        // Sync parameters untuk room: overwrite semua
        $submitted = collect($request->input('parameters', []))
            ->filter(fn($p) => !empty($p['kolom_reading']) && !empty($p['nama_parameter']));

        $submittedKoloms = $submitted->pluck('kolom_reading')->all();

        // Hapus parameter yang tidak ada di submit
        SensorParameter::where('room_id', $request->room_id)
            ->whereNotIn('kolom_reading', $submittedKoloms)
            ->delete();

        // Upsert parameter yang ada di submit
        foreach ($submitted as $i => $param) {
            SensorParameter::updateOrCreate(
                ['room_id' => $request->room_id, 'kolom_reading' => $param['kolom_reading']],
                ['nama_parameter' => $param['nama_parameter'], 'unit' => $param['unit'] ?? null, 'sort_order' => $i + 1]
            );
        }

        return response()->json(['success' => true, 'sensor' => $sensor->load(['room', 'sensorGroup'])]);
    }

    public function sensorUpdate(Request $request, Sensor $sensor)
    {
        $request->validate([
            'room_id'         => 'required|exists:rooms,id',
            'sensor_group_id' => 'nullable|exists:sensor_groups,id',
            'tipe_sensor'     => 'nullable|string|max:100',
            'gambar'          => 'nullable|image|max:2048',
            'is_active'       => 'nullable|boolean',
            'parameters'      => 'nullable|array',
            'parameters.*.nama_parameter' => 'required|string|max:100',
            'parameters.*.kolom_reading'  => 'required|string|max:20',
            'parameters.*.unit'           => 'nullable|string|max:20',
        ]);

        $gambarPath = $sensor->gambar;
        if ($request->hasFile('gambar')) {
            if ($gambarPath) Storage::disk('public')->delete($gambarPath);
            $gambarPath = $request->file('gambar')->store('sensors', 'public');
        }

        $sensor->update([
            'room_id'         => $request->room_id,
            'sensor_group_id' => $request->sensor_group_id,
            'tipe_sensor'     => $request->tipe_sensor,
            'gambar'          => $gambarPath,
            'is_active'       => $request->boolean('is_active', true),
        ]);

        // Sync parameters: hapus yang dibuang, upsert yang dikirim
        $submitted = collect($request->input('parameters', []))
            ->filter(fn($p) => !empty($p['kolom_reading']) && !empty($p['nama_parameter']));

        $submittedKoloms = $submitted->pluck('kolom_reading')->all();
        $newRoomId = $request->room_id;
        $oldRoomId = $sensor->getOriginal('room_id'); // room sebelum update

        // Jika room berubah, bersihkan parameter room lama yang tidak lagi dipakai
        if ($oldRoomId && $oldRoomId != $newRoomId) {
            SensorParameter::where('room_id', $oldRoomId)->delete();
        }

        // Hapus parameter yang dihilangkan dari list
        SensorParameter::where('room_id', $newRoomId)
            ->whereNotIn('kolom_reading', $submittedKoloms)
            ->delete();

        // Upsert parameter yang ada di submit
        foreach ($submitted as $i => $param) {
            SensorParameter::updateOrCreate(
                ['room_id' => $newRoomId, 'kolom_reading' => $param['kolom_reading']],
                ['nama_parameter' => $param['nama_parameter'], 'unit' => $param['unit'] ?? null, 'sort_order' => $i + 1]
            );
        }

        return response()->json(['success' => true, 'sensor' => $sensor->fresh()->load(['room', 'sensorGroup'])]);
    }

    public function sensorDestroy(Sensor $sensor)
    {
        if ($sensor->gambar) {
            Storage::disk('public')->delete($sensor->gambar);
        }
        $sensor->delete();
        return response()->json(['success' => true]);
    }

    // ── AC UNIT CRUD ─────────────────────────────────────────────────────────────

    public function acStore(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'room_id'  => 'required|exists:rooms,id',
            'power_kw' => 'nullable|numeric|min:0',
            'is_active'=> 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', false);
        $data['power_kw']  = $data['power_kw'] ?? 0;

        $unit = AcUnit::create($data);
        return response()->json(['success' => true, 'unit' => $unit->load('room')]);
    }

    public function acUpdate(Request $request, AcUnit $acUnit)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'room_id'  => 'required|exists:rooms,id',
            'power_kw' => 'nullable|numeric|min:0',
            'is_active'=> 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', false);
        $data['power_kw']  = $data['power_kw'] ?? 0;

        $acUnit->update($data);
        return response()->json(['success' => true, 'unit' => $acUnit->fresh()->load('room')]);
    }

    public function acDestroy(AcUnit $acUnit)
    {
        $acUnit->delete();
        return response()->json(['success' => true]);
    }

    public function acToggle(AcUnit $acUnit)
    {
        $acUnit->update(['is_active' => !$acUnit->is_active]);
        return response()->json(['success' => true, 'is_active' => $acUnit->is_active]);
    }
}
