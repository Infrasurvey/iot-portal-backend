<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Installation;

class DeviceBaseStation extends Model
{
    use HasFactory;
    
    protected $appends = [
        'device_rover_count',
        'battery_voltage',
        'available_memory'
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

    public function installation(){
        return $this->hasOne(Installation::class,'device_base_station_id');
    }


    public function getDeviceRoverCountAttribute()
    {
        return $this->rovers()->count();
    }

    public function getBatteryVoltageAttribute()
    {
        //return $this->device->measure_devices->last()->battery_voltage;
        return $this->device->battery_voltage;
    }

    public function getAvailableMemoryAttribute()
    {
        return $this->device->available_memory;
    }
}