<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        Building::create($data);
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
        $building->update($data);
        return back()->with('success', 'Gedung berhasil diperbarui.');
    }

    public function destroy(Building $building)
    {
        $building->delete();
        return back()->with('success', 'Gedung berhasil dihapus.');
    }
}
