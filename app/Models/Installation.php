<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Group;
use App\Models\DeviceBaseStation;

class Installation extends Model
{
    use HasFactory;

    protected $appends = [
        'device_rover_count',
        'battery_voltage',
        'available_memory'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'basestation',
    ];

    public function group(){
        return $this->belongsTo(Group::class);
    }

    public function basestation(){
        return $this->belongsTo(DeviceBaseStation::class,'device_base_station_id');
    }

    public function getDeviceRoverCountAttribute()
    {
        return $this->basestation->getDeviceRoverCountAttribute();
    }

    public function getBatteryVoltageAttribute()
    {
        //return $this->device->measure_devices->last()->battery_voltage;
        return $this->basestation->getBatteryVoltageAttribute();
    }

    public function getAvailableMemoryAttribute()
    {
        return $this->basestation->getAvailableMemoryAttribute();
    }

}
