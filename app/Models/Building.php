<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    protected $fillable = ['name', 'code', 'description', 'address'];

    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class)->orderBy('floor_number');
    }

    public function rooms(): HasMany
    {
        return $this->hasManyThrough(Room::class, Floor::class);
    }
}
