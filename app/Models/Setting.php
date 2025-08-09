<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'monday_start_time', 'monday_end_time', 'monday_is_active',
        'tuesday_start_time', 'tuesday_end_time', 'tuesday_is_active',
        'wednesday_start_time', 'wednesday_end_time', 'wednesday_is_active',
        'thursday_start_time', 'thursday_end_time', 'thursday_is_active',
        'friday_start_time', 'friday_end_time', 'friday_is_active',
        'saturday_start_time', 'saturday_end_time', 'saturday_is_active',
        'location_name', 'latitude', 'longitude', 'radius'
    ];

    protected $casts = [
        'monday_is_active' => 'boolean',
        'tuesday_is_active' => 'boolean',
        'wednesday_is_active' => 'boolean',
        'thursday_is_active' => 'boolean',
        'friday_is_active' => 'boolean',
        'saturday_is_active' => 'boolean',
        'latitude' => 'double',
        'longitude' => 'double',
        'radius' => 'integer'
    ];
}
