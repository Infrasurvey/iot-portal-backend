<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceBaseStation extends Model
{
    use HasFactory;
    
    protected $appends = [
        'DeviceRoverCount'
    ];

    public function getDeviceRoverCountAttribute()
    {
        return $this->rovers()->count();
    }

    public function device()
    {
        return $this->morphOne('App\Models\Device', 'is_device');
    }
}
