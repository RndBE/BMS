<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SensorGroup extends Model
{
    protected $fillable = [
        'kode_sensor',
        'nama_sensor',
        'deskripsi',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function sensors(): HasMany
    {
        return $this->hasMany(Sensor::class);
    }
}
