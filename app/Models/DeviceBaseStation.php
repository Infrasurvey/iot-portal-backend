<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Installation;

class DeviceBaseStation extends Model
{
    use HasFactory;

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
        return $this->hasMany(DeviceRover::class)->with(['device','lastmeasurerover','lastposition']);
    }

    public function configuration_base_stations()
    {
        return $this->hasMany(ConfigurationBaseStation::class);
    }

    public function installation()
    {
        return $this->hasOne(Installation::class,'device_base_station_id');
    }

    public function lastconf()
    {
        return $this->hasOne('App\Models\ConfigurationBaseStation')->latestOfMany()->with('file');
    }
    public function getLastConfigurationAttribute()
    {
        return $this->lastconf;
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