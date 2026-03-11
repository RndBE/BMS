<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FloorController extends Controller
{
    public function show(Floor $floor)
    {
        $floor->load(['building', 'rooms.sensors.readings' => function ($q) {
            $q->latest('recorded_at')->limit(1);
        }, 'rooms.acUnits']);

        $buildings = Building::with('floors')->get();
        return view('admin.floors.editor', compact('floor', 'buildings'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'building_id'  => 'required|exists:buildings,id',
            'name'         => 'required|string|max:255',
            'floor_number' => 'required|integer',
        ]);
        $floor = Floor::create($data);
        return redirect()->route('admin.floors.show', $floor)->with('success', 'Lantai berhasil ditambahkan.');
    }

    public function update(Request $request, Floor $floor)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'floor_number' => 'required|integer',
        ]);
        $floor->update($data);
        return back()->with('success', 'Lantai berhasil diperbarui.');
    }

    public function destroy(Floor $floor)
    {
        if ($floor->plan_file_path) {
            Storage::disk('public')->delete($floor->plan_file_path);
        }
        $floor->delete();
        return redirect()->route('admin.buildings.index')->with('success', 'Lantai berhasil dihapus.');
    }

    public function saveCanvas(Request $request, Floor $floor)
    {
        $request->validate([
            'canvas_data' => 'nullable|string',
        ]);
        $floor->update(['canvas_data' => $request->input('canvas_data')]);
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

        $floor->update([
            'plan_file_path' => $path,
            'plan_file_type' => $type,
        ]);

        return back()->with('success', 'Denah berhasil diupload.');
    }

    public function addRoom(Request $request, Floor $floor)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'code'     => 'required|string|max:20',
            'marker_x' => 'required|numeric|min:0|max:100',
            'marker_y' => 'required|numeric|min:0|max:100',
            'status'   => 'required|in:normal,warning,poor',
        ]);
        $data['floor_id'] = $floor->id;
        $room = Room::create($data);
        return response()->json(['success' => true, 'room' => $room]);
    }
}
