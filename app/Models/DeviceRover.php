<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class DeviceRover extends Model
{
    use HasFactory;

    protected $appends = [
        'battery_voltage',
        'system_id',
        'last_communication',
        'default_position'
        ];

    protected $hidden = [
        'device',
        'measure_rovers',
        'positions',
        'basestation',
        'lastmeasurerover',
        'lastposition'
        ];

    public function device()
    {
        return $this->morphOne('App\Models\Device', 'table')->with('lastmeasuredevice');
    }

    public function basestation(){
        return $this->belongsTo('App\Models\DeviceBaseStation','device_base_station_id');
    }

    public function measure_rovers()
    {
        return $this->hasMany('App\Models\MeasureRover')->with('file');
    }

    public function positions()
    {
        return $this->hasMany('App\Models\Position');
    }

    public function lastmeasurerover()
    {
        return $this->hasOne('App\Models\MeasureRover')->latestOfMany()->with('file');
    }

    public function lastposition()
    {
        return $this->hasOne('App\Models\Position')->latestOfMany()->with('file');
    }

    public function getDefaultPositionAttribute()
    {
        $pos = $this->lastposition;
        if($pos != null){
            return $pos;
        }
        else{
            return null;
        }
    }

    public function getBatteryVoltageAttribute()
    {
        return $this->device->battery_voltage;
    }
    public function getSystemIdAttribute()
    {
        return $this->device->system_id;
    }

    public function getLastCommunicationAttribute(){
        try {
            $position = $this->lastposition;
            $measure = $this->lastmeasurerover;

            if($position != null || $measure != null){
                $date1 = $position->date;
                $date2 = $measure->date;
                if($date1 > $date2){
                    return $date1;
                }
            return $date2;
        }
        return null;
        } catch (\Throwable $th) {
            //throw $th;
        }
        return null;
    }
}
