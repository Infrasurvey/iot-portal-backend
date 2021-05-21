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
        'basestation'
    ];

    public function device()
    {
        return $this->morphOne('App\Models\Device', 'table');
    }

    public function basestation(){
        return $this->belongsTo('App\Models\DeviceBaseStation','device_base_station_id');
    }

    public function measure_rovers()
    {
        return $this->hasMany('App\Models\MeasureRover');
    }

    public function positions()
    {
        return $this->hasMany('App\Models\Position');
    }

    public function getDefaultPositionAttribute()
    {
        $pos = $this->positions->last();
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
            $position = $this->positions->last();
            $measure = $this->measure_rovers->last();
            if($position != null || $measure != null){
                $date1 = $this->positions->last()->date;
                $date2 = $this->measure_rovers->last()->date;
                if($date1 > $date2){
                    return $date1;
                }
            return $date2;
        }

        return null;
        
        } catch (\Throwable $th) {
            //throw $th;
        }
        
    }
}
