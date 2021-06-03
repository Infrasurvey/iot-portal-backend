<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Installation;

class DeviceBaseStation extends Model
{
    use HasFactory;
    
    /* protected $appends = [
        'device_rover_count',
        'battery_voltage',
        'available_memory',
        'last_configuration',
        'last_communication'
    ]; */

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'installation',
        'device',
         'rovers',
        'configurations',
        'lastconf'
    ];

    public function device()
    {
        return $this->morphOne('App\Models\Device', 'table')->with('lastmeasuredevice');
    }

    public function rovers()
    {
        return $this->hasMany('App\Models\DeviceRover')->with(['device','lastmeasurerover','lastposition']);
    }

    public function configurations()
    {
        return $this->hasMany('App\Models\ConfigurationBaseStation');
    }

    public function installation(){
        return $this->hasOne(Installation::class,'device_base_station_id');
    }

    public function lastconf()
    {
        return $this->hasOne('App\Models\ConfigurationBaseStation')->latestOfMany()->with('file');
    }
    public function getLastConfigurationAttribute()
    {
        return $this->lastconf;
        //return $this->lastconfiguration;
        /* $conf = $this->configurations->last();
        if($conf != null){
            return $conf->with('file');
        }
        else{
            return null;
        } */

    }


    public function getDeviceRoverCountAttribute()
    {
        return $this->rovers->count();
    }

    public function getBatteryVoltageAttribute()
    {
        return $this->device->battery_voltage;
    }

    public function getAvailableMemoryAttribute()
    {
        return $this->device->available_memory;
    }

    public function getLastCommunicationAttribute(){
        $biggest_date = null;
        $rovers = $this->rovers;
        $j = $rovers->count();

        for($i=0; $i < $j ; $i++) { 
            $rover = $rovers->get($i);
            if($rover != null)
            {
                $date1 = $rover->last_communication;
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
        $last_device = $this->device->lastmeasuredevice;
        if($last_device != null)
        {   
            $device_last_time = $last_device->file->creation_time;
            if($device_last_time > $date){
                $date = $device_last_time;
            }
        }
        return $date;
    }
}