<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcUnit extends Model
{
    protected $fillable = ['room_id', 'name', 'is_active', 'power_kw'];

    protected $casts = ['is_active' => 'boolean'];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
