<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorReading extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'room_id',
        'sensor1',  'sensor2',  'sensor3',  'sensor4',
        'sensor5',  'sensor6',  'sensor7',  'sensor8',
        'sensor9',  'sensor10', 'sensor11', 'sensor12',
        'sensor13', 'sensor14', 'sensor15', 'sensor16',
        'waktu',
    ];

    protected $casts = ['waktu' => 'datetime'];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
