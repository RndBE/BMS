<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FloorController extends Controller
{
    public function show(Floor $floor)
    {
        $floor->load(['building', 'rooms.latestReading', 'rooms.acUnits']);

        $buildings = Building::with('floors')->get();

        // Ruangan tersedia = yang belum di-assign ke lantai manapun
        $availableRooms = Room::whereNull('floor_id')
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'status']);

        return view('admin.floors.editor', compact('floor', 'buildings', 'availableRooms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'building_id'  => 'required|exists:buildings,id',
            'name'         => 'required|string|max:255',
            'floor_number' => 'required|integer',
        ]);
        $floor = Floor::create($data);
        AuditLog::record('create', 'Floor', $floor->id, "Menambah lantai: {$floor->name}", null, $floor->toArray());
        return redirect()->route('admin.floors.show', $floor)->with('success', 'Lantai berhasil ditambahkan.');
    }

    public function update(Request $request, Floor $floor)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'floor_number' => 'required|integer',
        ]);
        $oldData = $floor->toArray();
        $floor->update($data);
        AuditLog::record('update', 'Floor', $floor->id, "Mengubah lantai: {$floor->name}", $oldData, $floor->fresh()->toArray());
        return back()->with('success', 'Lantai berhasil diperbarui.');
    }

    public function destroy(Floor $floor)
    {
        $oldData = $floor->toArray();
        $floorName = $floor->name;

        if ($floor->plan_file_path) {
            Storage::disk('public')->delete($floor->plan_file_path);
        }
        $floor->delete();

        AuditLog::record('delete', 'Floor', $oldData['id'], "Menghapus lantai: {$floorName}", $oldData, null);

        return redirect()->route('admin.buildings.index')->with('success', 'Lantai berhasil dihapus.');
    }

    public function saveCanvas(Request $request, Floor $floor)
    {
        $request->validate([
            'canvas_data' => 'nullable|string',
        ]);
        $oldData = $floor->toArray();
        $floor->update(['canvas_data' => $request->input('canvas_data')]);
        AuditLog::record('update', 'Floor', $floor->id, "Menyimpan perubahan canvas denah untuk lantai: {$floor->name}", $oldData, $floor->fresh()->toArray());
        return response()->json(['success' => true]);
    }

    public function uploadPlan(Request $request, Floor $floor)
    {
        $request->validate([
            'plan_file' => 'required|file|mimes:jpg,jpeg,png,webp,svg,pdf|max:20480',
        ]);

        // Delete old file
        if ($floor->plan_file_path) {
            Storage::disk('public')->delete($floor->plan_file_path);
        }

        $file = $request->file('plan_file');
        $ext  = strtolower($file->getClientOriginalExtension());
        $type = $ext === 'svg' ? 'svg' : ($ext === 'pdf' ? 'pdf' : 'image');
        $path = $file->store('floor-plans', 'public');

        $oldData = $floor->toArray();

        $floor->update([
            'plan_file_path' => $path,
            'plan_file_type' => $type,
        ]);

        AuditLog::record('update', 'Floor', $floor->id, "Mengupload gambar blueprint denah lantai: {$floor->name}", $oldData, $floor->fresh()->toArray());

        return back()->with('success', 'Denah berhasil diupload.');
    }

    public function addRoom(Request $request, Floor $floor)
    {
        // Jika room_id dikirim → assign existing room ke lantai ini
        if ($request->filled('room_id')) {
            $request->validate([
                'room_id'  => 'required|exists:rooms,id',
                'marker_x' => 'required|numeric|min:0|max:100',
                'marker_y' => 'required|numeric|min:0|max:100',
            ]);

            $room = Room::findOrFail($request->room_id);
            $oldData = $room->toArray();

            $room->update([
                'floor_id' => $floor->id,
                'marker_x' => $request->marker_x,
                'marker_y' => $request->marker_y,
            ]);

            AuditLog::record('update', 'Room', $room->id, "Menempatkan ruangan ({$room->name}) ke denah lantai ({$floor->name})", $oldData, $room->fresh()->toArray());

            return response()->json(['success' => true, 'room' => $room->fresh()]);
        }

        // Fallback: buat room baru (tidak digunakan dari drag-drop, tapi dipertahankan)
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'code'     => 'required|string|max:20',
            'marker_x' => 'required|numeric|min:0|max:100',
            'marker_y' => 'required|numeric|min:0|max:100',
            'status'   => 'required|in:normal,warning,poor',
        ]);
        $data['floor_id'] = $floor->id;
        $room = Room::create($data);

        AuditLog::record('create', 'Room', $room->id, "Menambahkan ruangan baru ({$room->name}) ke denah lantai ({$floor->name})", null, $room->toArray());

        return response()->json(['success' => true, 'room' => $room]);
    }
}
