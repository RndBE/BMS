<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorReadingLatest extends Model
{
    public $timestamps = false;                // hanya pakai updated_at manual

    protected $table = 'sensor_reading_latests';

    protected $fillable = [
        'room_id',
        'sensor1',  'sensor2',  'sensor3',  'sensor4',
        'sensor5',  'sensor6',  'sensor7',  'sensor8',
        'sensor9',  'sensor10', 'sensor11', 'sensor12',
        'sensor13', 'sensor14', 'sensor15', 'sensor16',
        'recorded_at',
        'waktu',
        'updated_at',
    ];

    protected $casts = [
        'sensor1'  => 'float', 'sensor2'  => 'float', 'sensor3'  => 'float',
        'sensor4'  => 'float', 'sensor5'  => 'float', 'sensor6'  => 'float',
        'sensor7'  => 'float', 'sensor8'  => 'float', 'sensor9'  => 'float',
        'sensor10' => 'float', 'sensor11' => 'float', 'sensor12' => 'float',
        'sensor13' => 'float', 'sensor14' => 'float', 'sensor15' => 'float',
        'sensor16' => 'float',
        'recorded_at'      => 'datetime',
        'waktu'            => 'datetime',
        'updated_at'       => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
