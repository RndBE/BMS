<?php

namespace App\Models;

use App\Models\Room;
use Illuminate\Database\Eloquent\Model;

class AlertRule extends Model
{
    protected $fillable = [
        'name', 'parameter_key', 'condition',
        'threshold', 'severity', 'notification_channel', 'is_active',
        'kategori', 'durasi_tunda', 'room_ids',
    ];

    protected $casts = [
        'threshold'    => 'float',
        'is_active'    => 'boolean',
        'durasi_tunda' => 'integer',
        'room_ids'     => 'array',
    ];

    /** Kembalikan koleksi Room berdasarkan room_ids JSON */
    public function rooms(): \Illuminate\Database\Eloquent\Collection
    {
        $ids = $this->room_ids ?? [];
        return Room::whereIn('id', $ids)->get();
    }
}
