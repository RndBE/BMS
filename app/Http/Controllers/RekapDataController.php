<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekapDataController extends Controller
{
    /** Halaman utama Rekap Data */
    public function index()
    {
        return view('rekap-data.index');
    }

    /** API: matriks kelengkapan data per ruangan per hari */
    public function getData(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

        if (! $startDate || ! $endDate) {
            return response()->json([
                'success' => false,
                'message' => 'start_date dan end_date harus diisi.',
            ], 400);
        }

        try {
            $tz    = config('app.timezone', 'Asia/Jakarta');
            $start = Carbon::createFromFormat('Y-m-d', $startDate, $tz)->startOfDay();
            $end   = Carbon::createFromFormat('Y-m-d', $endDate,   $tz)->endOfDay();
        } catch (\Exception) {
            return response()->json(['success' => false, 'message' => 'Format tanggal tidak valid.'], 400);
        }

        if ($start->gt($end)) {
            return response()->json(['success' => false, 'message' => 'Tanggal mulai harus sebelum tanggal akhir.'], 400);
        }

        $diffDays = $start->diffInDays($end);
        if ($diffDays > 30) {
            return response()->json(['success' => false, 'message' => 'Rentang tanggal maksimal 31 hari.'], 400);
        }

        // Buat array tanggal
        $dates = [];
        $cur   = $start->copy()->startOfDay();
        while ($cur->lte($end)) {
            $dates[] = $cur->format('Y-m-d');
            $cur->addDay();
        }

        // Semua ruangan
        $rooms = Room::orderBy('name')->get();

        // Hitung count per ruangan per hari dalam satu query
        $counts = DB::table('sensor_readings')
            ->selectRaw('room_id, DATE(waktu) as tgl, COUNT(*) as cnt')
            ->whereBetween('waktu', [$start, $end])
            ->groupBy('room_id', 'tgl')
            ->get()
            ->groupBy('room_id')
            ->map(fn ($g) => $g->keyBy('tgl'));

        $now = Carbon::now($tz);

        $result = $rooms->map(function ($room) use ($dates, $counts, $now) {
            $roomCounts = $counts->get($room->id, collect());

            $days = [];
            $totalPct = 0;
            $activeDays = 0;

            foreach ($dates as $date) {
                $isToday  = $date === $now->format('Y-m-d');
                $expected = $isToday
                    ? $now->hour * 60 + $now->minute   // menit yang sudah lewat hari ini
                    : 1440;                              // 24 jam * 60 menit

                $cnt = $roomCounts->get($date)?->cnt ?? 0;
                $pct = $expected > 0 ? min(100, round($cnt / $expected * 100, 1)) : 0;

                if ($expected > 0) {
                    $totalPct += $pct;
                    $activeDays++;
                }

                $days[] = [
                    'date'     => $date,
                    'count'    => (int) $cnt,
                    'expected' => (int) $expected,
                    'pct'      => $pct,
                ];
            }

            $overallPct = $activeDays > 0 ? round($totalPct / $activeDays, 1) : 0;

            return [
                'id'          => $room->id,
                'name'        => $room->name,
                'overall_pct' => $overallPct,
                'days'        => $days,
            ];
        })->values()->toArray();

        return response()->json([
            'success' => true,
            'dates'   => $dates,
            'rooms'   => $result,
        ]);
    }
}
