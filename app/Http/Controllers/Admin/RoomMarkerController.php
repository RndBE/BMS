<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomMarkerController extends Controller
{
    /** Update marker position (from drag & drop in editor) */
    public function update(Request $request, Room $room): JsonResponse
    {
        $data = $request->validate([
            'marker_x' => 'required|numeric|min:0|max:100',
            'marker_y' => 'required|numeric|min:0|max:100',
        ]);

        $room->update($data);

        return response()->json(['success' => true, 'room' => $room->only('id', 'name', 'marker_x', 'marker_y')]);
    }

    /** Update full room details (name, code, status, svg position) */
    public function updateDetails(Request $request, Room $room): JsonResponse
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'code'       => 'required|string|max:20|unique:rooms,code,' . $room->id,
            'status'     => 'required|in:normal,warning,poor',
            'svg_x'      => 'nullable|integer|min:0',
            'svg_y'      => 'nullable|integer|min:0',
            'svg_width'  => 'nullable|integer|min:10',
            'svg_height' => 'nullable|integer|min:10',
        ]);

        $room->update($data);

        return response()->json(['success' => true, 'room' => $room->fresh()]);
    }

    /** Delete a room from a floor */
    public function destroy(Room $room): JsonResponse
    {
        $room->delete();
        return response()->json(['success' => true]);
    }
}
