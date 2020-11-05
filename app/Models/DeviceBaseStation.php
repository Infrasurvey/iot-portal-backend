<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceBaseStation extends Model
{
    use HasFactory;
    
    protected $appends = [
        'device_rover_count',
        'last_battery_voltage'
    ];

    public function device()
    {
        return $this->morphOne('App\Models\Device', 'table');
    }

    public function rovers()
    {
        return $this->hasMany('App\Models\DeviceRover');
    }

    public function configurations()
    {
        return $this->hasMany('App\Models\ConfigurationBaseStation');
    }

    public function getDeviceRoverCountAttribute()
    {
        return $this->rovers()->count();
    }

    public function getLastBatteryVoltageAttribute()
    {
        //return $this->device->measure_devices->last()->battery_voltage;
        return $this->device->last_battery_voltage;
    }
}