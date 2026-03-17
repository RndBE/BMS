<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sensor extends Model
{
    protected $fillable = ['room_id', 'sensor_group_id', 'gambar', 'tipe_sensor', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function sensorGroup(): BelongsTo
    {
        return $this->belongsTo(SensorGroup::class);
    }
}
