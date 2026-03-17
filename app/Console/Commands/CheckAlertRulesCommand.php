<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Models\AlertRule;
use App\Models\SensorParameter;
use App\Models\SensorReading;
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

        return self::SUCCESS;
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

        // Ambil reading terbaru
        $reading = SensorReading::where('room_id', $roomId)
            ->orderByDesc('waktu')
            ->first();

        if (! $reading) {
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

        // Buat pesan alert
        $roomName = \App\Models\Room::find($roomId)?->name ?? "Room #{$roomId}";
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
            'type'          => $rule->severity === 'critical' ? 'critical' : 'warning',
            'message'       => $pesan,
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
