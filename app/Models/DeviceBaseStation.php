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
        'available_memory',
        'last_configuration',
        'last_communication'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'installation',
        'device',
        'configurations',
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

    public function getLastConfigurationAttribute()
    {
        return $this->configurations->last();
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

    public function getLastCommunicationAttribute(){
        $biggest_date = null;
        $j = $this->rovers->count();
        for($i=0; $i < $j ; $i++) { 
            $rover = $this->rovers->get($i);
            if($rover != null)
            {
                $date1 = $this->rovers->get($i)->last_communication;
                if($date1 != null){
                    if($biggest_date == null)
                    {
                        $biggest_date = $date1;
                    }
                    if($date1 > $biggest_date){
                        $biggest_date = $date1;
                    }
                }
            }
            
        }
        $date = $biggest_date;
        $last_device = $this->device->measure_devices->last();
        if($last_device != null)
        {
            $device_last_time = $this->device->measure_devices->last()->file->creation_time;
            if($device_last_time > $date){
                $date = $device_last_time;
            }
        }
        
        
    
        return $date;
    }
}