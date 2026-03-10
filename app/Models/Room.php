<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = [
        'name', 'code', 'svg_x', 'svg_y', 'svg_width', 'svg_height', 'status',
    ];

    public function sensors(): HasMany
    {
        return $this->hasMany(Sensor::class);
    }

    public function acUnits(): HasMany
    {
        return $this->hasMany(AcUnit::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    public function getLatestReadings(): array
    {
        $readings = [];
        foreach ($this->sensors as $sensor) {
            $latest = $sensor->readings()->latest('recorded_at')->first();
            if ($latest) {
                $readings[$sensor->type] = [
                    'value' => $latest->value,
                    'unit'  => $sensor->unit,
                ];
            }
        }
        return $readings;
    }
}
