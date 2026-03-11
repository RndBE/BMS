<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Floor extends Model
{
    protected $fillable = [
        'building_id', 'name', 'floor_number',
        'plan_file_path', 'plan_file_type', 'plan_width', 'plan_height',
        'canvas_data',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function getPlanUrlAttribute(): ?string
    {
        return $this->plan_file_path
            ? Storage::url($this->plan_file_path)
            : null;
    }

    public function hasPlan(): bool
    {
        return !empty($this->plan_file_path);
    }
}
