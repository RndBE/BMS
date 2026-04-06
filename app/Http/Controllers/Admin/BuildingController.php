<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Building;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    public function index()
    {
        $buildings = Building::withCount('floors')->get();
        return view('admin.buildings.index', compact('buildings'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:20|unique:buildings',
            'description' => 'nullable|string',
            'address'     => 'nullable|string',
        ]);
        $building = Building::create($data);
        AuditLog::record('create', 'Building', $building->id, "Menambah gedung: {$building->name}", null, $building->toArray());
        return back()->with('success', 'Gedung berhasil ditambahkan.');
    }

    public function update(Request $request, Building $building)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:20|unique:buildings,code,' . $building->id,
            'description' => 'nullable|string',
            'address'     => 'nullable|string',
        ]);
        $oldData = $building->toArray();
        $building->update($data);
        AuditLog::record('update', 'Building', $building->id, "Mengubah gedung: {$building->name}", $oldData, $building->fresh()->toArray());
        return back()->with('success', 'Gedung berhasil diperbarui.');
    }

    public function destroy(Building $building)
    {
        $oldData = $building->toArray();
        $buildingName = $building->name;
        
        $building->delete();
        
        AuditLog::record('delete', 'Building', $oldData['id'], "Menghapus gedung: {$buildingName}", $oldData, null);
        
        return back()->with('success', 'Gedung berhasil dihapus.');
    }
}
