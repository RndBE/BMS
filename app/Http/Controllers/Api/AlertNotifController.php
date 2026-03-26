<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Illuminate\Http\Request;

class AlertNotifController extends Controller
{
    /**
     * GET /api/alerts/unread
     * Return jumlah alert belum dibaca + data alert terbaru.
     * Digunakan untuk polling notifikasi dari frontend (setiap 30 detik).
     */
    public function unread(Request $request)
    {
        if (! auth()->check()) {
            return response()->json(['count' => 0, 'alerts' => []]);
        }

        // Ambil alert unread terbaru (max 5 untuk ditampilkan di toast)
        $alerts = Alert::where('is_read', false)
            ->with('room:id,name')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'room_id', 'type', 'message', 'created_at']);

        $count = Alert::where('is_read', false)->count();

        return response()->json([
            'count'  => $count,
            'alerts' => $alerts->map(fn($a) => [
                'id'         => $a->id,
                'room'       => $a->room?->name ?? '-',
                'type'       => $a->type,
                'message'    => $a->message,
                'created_at' => $a->created_at?->format('H:i'),
            ]),
        ]);
    }
}
