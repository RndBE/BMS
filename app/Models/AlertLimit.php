<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlertLimit extends Model
{
    protected $fillable = [
        'parameter_key', 'label', 'icon',
        'normal_min', 'normal_max',
        'warn_low_min', 'warn_low_max',
        'warn_high_min', 'warn_high_max',
        'poor_low', 'poor_high',
    ];

    protected $casts = [
        'normal_min'    => 'float',
        'normal_max'    => 'float',
        'warn_low_min'  => 'float',
        'warn_low_max'  => 'float',
        'warn_high_min' => 'float',
        'warn_high_max' => 'float',
        'poor_low'      => 'float',
        'poor_high'     => 'float',
    ];
}
