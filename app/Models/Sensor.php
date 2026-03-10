<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sensor extends Model
{
    protected $fillable = ['room_id', 'type', 'unit', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function readings(): HasMany
    {
        return $this->hasMany(SensorReading::class);
    }
}
