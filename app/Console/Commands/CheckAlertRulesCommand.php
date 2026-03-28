<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Models\AlertRule;
use App\Models\Room;
use App\Models\SensorParameter;
use App\Models\SensorReading;
use App\Models\SensorReadingLatest;
use App\Services\WhatsappService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CheckAlertRulesCommand extends Command
{
    protected $signature   = 'alert:check';
    protected $description = 'Periksa sensor readings terhadap alert rules dan kirim notifikasi jika terpicu';

    public function handle(WhatsappService $wa): int
    {
        $rules = AlertRule::where('is_active', true)->get();

        if ($rules->isEmpty()) {
            $this->info('Tidak ada alert rule aktif.');
            return self::SUCCESS;
        }

        $triggered = 0;

        foreach ($rules as $rule) {
            $roomIds = $rule->room_ids ?? [];

            // Jika tidak ada room_ids spesifik → berlaku untuk semua room
            if (empty($roomIds)) {
                $roomIds = SensorParameter::where('nama_parameter', 'LIKE', '%' . $rule->parameter_key . '%')
                    ->distinct('room_id')
                    ->pluck('room_id')
                    ->toArray();
            }

            foreach ($roomIds as $roomId) {
                $triggered += $this->checkRoom($rule, (int) $roomId, $wa);
            }
        }

        $this->info("Selesai. Total alert baru: {$triggered}");
        Log::info("[alert:check] Selesai. Alert baru: {$triggered}");

        // ── Cek sensor offline ────────────────────────────────────────────────
        $this->checkOfflineSensors();

        return self::SUCCESS;
    }

    /**
     * Cek ruangan yang sensor-nya tidak mengirim data > 60 menit.
     * Buat Alert type='sensor_offline' + ubah rooms.status ke 'poor'.
     * Cooldown: tidak buat alert duplikat dalam 60 menit.
     */
    private function checkOfflineSensors(int $thresholdMinutes = 60): void
    {
        $cutoff  = Carbon::now()->subMinutes($thresholdMinutes);

        // Ruangan yang sensor_reading_latests.waktu-nya sudah melewati threshold
        $offlineRooms = SensorReadingLatest::where('waktu', '<', $cutoff)
            ->orWhereNull('waktu')
            ->with('room')
            ->get();

        foreach ($offlineRooms as $latest) {
            $roomId   = $latest->room_id;
            $roomName = $latest->room->name ?? "Room #{$roomId}";

            // Selalu update status room ke 'poor' saat sensor offline
            // (terlepas dari cooldown alert — status harus langsung terefleksi di dashboard)
            Room::where('id', $roomId)
                ->where('status', '!=', 'poor')
                ->update(['status' => 'poor', 'updated_at' => now()]);

            // Cooldown: skip pembuatan alert jika sudah ada alert offline dalam 60 menit terakhir
            $sudahAda = Alert::where('room_id', $roomId)
                ->where('type', 'sensor_offline')
                ->where('created_at', '>=', Carbon::now()->subMinutes(60))
                ->exists();

            if ($sudahAda) {
                continue;
            }

            // Buat alert
            Alert::create([
                'room_id'       => $roomId,
                'alert_rule_id' => null,
                'type'          => 'sensor_offline',
                'message'       => "Sensor {$roomName} offline — dalam {$thresholdMinutes} menit",
                'nilai'         => null,
                'is_read'       => false,
            ]);

            $this->line("  ⚠ OFFLINE: {$roomName} (terakhir: " . ($latest->waktu ?? 'tidak ada') . ")");
            Log::warning("[alert:check] Sensor offline | Room={$roomName} | waktu={$latest->waktu}");
        }

    }

    private function checkRoom(AlertRule $rule, int $roomId, WhatsappService $wa): int
    {
        // Cari parameter yang cocok di room ini
        $param = SensorParameter::where('room_id', $roomId)
            ->where('nama_parameter', 'LIKE', '%' . $rule->parameter_key . '%')
            ->first();

        if (! $param) {
            return 0;
        }

        // Ambil reading terbaru dari snapshot table (lebih cepat dari sensor_readings)
        $reading = SensorReadingLatest::where('room_id', $roomId)->first();

        if (! $reading) {
            return 0;
        }

        // ── Skip jika sensor offline (data > 60 menit) ───────────────────────
        // Alert sensor_offline sudah ditangani terpisah di checkOfflineSensors()
        if ($reading->waktu && $reading->waktu->lt(Carbon::now()->subMinutes(60))) {
            return 0;
        }

        $kolom = $param->kolom_reading; // e.g. "sensor2"
        $nilai = $reading->{$kolom};

        if ($nilai === null) {
            return 0;
        }

        $nilai = (float) $nilai;

        // Evaluasi kondisi
        if (! $this->evaluate($nilai, $rule->condition, $rule->threshold)) {
            return 0;
        }

        // Cek cooldown (durasi_tunda menit)
        $cooldown = max(1, (int) ($rule->durasi_tunda ?? 5));
        $sudahAda = Alert::where('alert_rule_id', $rule->id)
            ->where('room_id', $roomId)
            ->where('created_at', '>=', Carbon::now()->subMinutes($cooldown))
            ->exists();

        if ($sudahAda) {
            return 0;
        }

        // Pesan singkat untuk disimpan di DB (tampil di app)
        $roomName     = \App\Models\Room::find($roomId)?->name ?? "Room #{$roomId}";
        $shortMessage = $rule->name;

        // Tentukan type berdasarkan parameter_key + condition
        $typeMap = [
            'suhu'       => ['>' => 'high_temp',     '<' => 'low_temp'],
            'kelembaban' => ['>' => 'high_humidity',  '<' => 'low_humidity'],
            'co2'        => ['>' => 'co2_tinggi',     '<' => 'co2_rendah'],
            'daya'       => ['>' => 'high_power',     '<' => 'low_power'],
            'tegangan'   => ['>' => 'high_voltage',   '<' => 'low_voltage'],
        ];
        $alertType = $typeMap[$rule->parameter_key][$rule->condition]
                     ?? ($rule->severity === 'critical' ? 'critical' : 'warning');

        // Pesan lengkap untuk notifikasi WhatsApp
        $pesan = "[BMS ALERT - {$rule->severity}] {$rule->name}\n" .
                 "Ruangan : {$roomName}\n" .
                 "Parameter : {$param->nama_parameter}\n" .
                 "Nilai : {$nilai} {$param->unit}\n" .
                 "Kondisi : {$rule->parameter_key} {$rule->condition} {$rule->threshold}\n" .
                 "Waktu : " . now()->format('d/m/Y H:i:s');

        // Simpan ke tabel alerts
        Alert::create([
            'room_id'       => $roomId,
            'alert_rule_id' => $rule->id,
            'type'          => $alertType,
            'message'       => $shortMessage,
            'nilai'         => $nilai,
            'is_read'       => false,
        ]);

        // Kirim WhatsApp jika channel-nya whatsapp
        if ($rule->notification_channel === 'whatsapp') {
            $wa->send($pesan);
        }

        $this->line("  ✓ ALERT [{$rule->severity}] {$rule->name} → {$roomName} | {$nilai}");
        Log::warning("[alert:check] {$rule->name} terpicu | Room={$roomName} | nilai={$nilai}");

        return 1;
    }

    /**
     * Evaluasi kondisi numerik.
     */
    private function evaluate(float $nilai, string $condition, float $threshold): bool
    {
        return match ($condition) {
            '>'  => $nilai >  $threshold,
            '<'  => $nilai <  $threshold,
            '>=' => $nilai >= $threshold,
            '<=' => $nilai <= $threshold,
            '==' => $nilai == $threshold,
            '!=' => $nilai != $threshold,
            default => false,
        };
    }
}
