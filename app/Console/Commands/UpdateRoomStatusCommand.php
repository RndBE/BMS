<?php

namespace App\Console\Commands;

use App\Models\AlertLimit;
use App\Models\Room;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateRoomStatusCommand extends Command
{
    protected $signature   = 'rooms:update-status';
    protected $description = 'Hitung ulang status (normal/warning/poor) tiap ruangan berdasarkan sensor reading terbaru vs AlertLimit';

    /** Mapping parameter_key di alert_limits → kolom sensor_readings */
    private const PARAM_COLUMN = [
        'suhu'       => 'sensor1',
        'kelembaban' => 'sensor2',
        'co2'        => 'sensor5',
    ];

    public function handle(): int
    {
        $limits = AlertLimit::all()->keyBy('parameter_key');

        if ($limits->isEmpty()) {
            $this->warn('Tidak ada AlertLimit dikonfigurasi, skip.');
            return self::SUCCESS;
        }

        $rooms   = Room::with('latestReading')->get();
        $updated = 0;

        foreach ($rooms as $room) {
            $reading = $room->latestReading;

            if (! $reading) {
                continue;
            }

            $statuses = [];

            foreach (self::PARAM_COLUMN as $paramKey => $col) {
                $lim = $limits->get($paramKey);
                if (! $lim) continue;
                $val = (float) $reading->$col;
                if ($val == 0) continue; // sensor tidak aktif / tidak ada data
                $statuses[] = $this->calcStatus($val, $lim);
            }

            $newStatus = empty($statuses) ? 'normal' : $this->worstStatus(...$statuses);

            if ($room->status !== $newStatus) {
                $room->update(['status' => $newStatus]);
                $updated++;
                $this->line("  ✓ {$room->name}: {$room->status} → {$newStatus}");
            }
        }

        $this->info("Selesai. {$updated} ruangan diperbarui dari {$rooms->count()} total.");
        Log::info("[rooms:update-status] Selesai. Updated: {$updated}/{$rooms->count()}");

        return self::SUCCESS;
    }

    private function calcStatus(float $val, AlertLimit $lim): string
    {
        // Poor: di luar batas poor
        if ($lim->poor_low  !== null && $val < $lim->poor_low)  return 'poor';
        if ($lim->poor_high !== null && $val > $lim->poor_high) return 'poor';

        // Warning low zone
        if ($lim->warn_low_min !== null && $lim->warn_low_max !== null) {
            if ($val >= $lim->warn_low_min && $val <= $lim->warn_low_max) return 'warning';
        }
        // Warning high zone
        if ($lim->warn_high_min !== null && $lim->warn_high_max !== null) {
            if ($val >= $lim->warn_high_min && $val <= $lim->warn_high_max) return 'warning';
        }

        // Di luar normal range
        if ($lim->normal_min !== null && $val < $lim->normal_min) return 'warning';
        if ($lim->normal_max !== null && $val > $lim->normal_max) return 'warning';

        return 'normal';
    }

    private function worstStatus(string ...$statuses): string
    {
        $rank  = ['normal' => 0, 'warning' => 1, 'poor' => 2];
        $worst = 'normal';
        foreach ($statuses as $s) {
            if (($rank[$s] ?? 0) > ($rank[$worst] ?? 0)) {
                $worst = $s;
            }
        }
        return $worst;
    }
}
