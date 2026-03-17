<?php

namespace App\Models;

use App\Models\AlertRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    protected $fillable = ['room_id', 'alert_rule_id', 'type', 'message', 'nilai', 'is_read'];

    protected $casts = ['is_read' => 'boolean'];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function alertRule(): BelongsTo
    {
        return $this->belongsTo(AlertRule::class);
    }

    public function getIconAttribute(): string
    {
        return match($this->type) {
            'sensor_offline' => 'wifi-off',
            'high_temp'      => 'thermometer',
            'ac_off'         => 'wind',
            'high_power'     => 'zap',
            default          => 'alert-triangle',
        };
    }
}
