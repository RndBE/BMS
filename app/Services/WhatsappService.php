<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected string $apiUrl;
    protected string $defaultPhone;

    public function __construct()
    {
        $this->apiUrl      = rtrim(config('services.whatsapp.url'), '/');
        $this->defaultPhone = config('services.whatsapp.phone', '');
    }

    /**
     * Kirim pesan WhatsApp.
     *
     * @param string      $message Isi pesan
     * @param string|null $phone   Nomor tujuan (default dari .env)
     * @return bool
     */
    public function send(string $message, ?string $phone = null): bool
    {
        $target = $phone ?? $this->defaultPhone;

        if (empty($target) || empty($this->apiUrl)) {
            Log::warning('[WhatsApp] Nomor/URL tidak dikonfigurasi.', compact('target'));
            return false;
        }

        try {
            $response = Http::timeout(10)->post($this->apiUrl, [
                'phone'   => $target,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info('[WhatsApp] Pesan terkirim.', ['phone' => $target]);
                return true;
            }

            Log::error('[WhatsApp] Gagal kirim.', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
        } catch (\Throwable $e) {
            Log::error('[WhatsApp] Exception: ' . $e->getMessage());
        }

        return false;
    }
}
