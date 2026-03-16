<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorParameter extends Model
{
    protected $fillable = [
        'room_id',
        'nama_parameter',
        'unit',
        'kolom_reading',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
