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
        'last_communication'
    ];

    protected $hidden = [
        'device',
        'measure_rovers',
        'positions'
    ];

    public function device()
    {
        return $this->morphOne('App\Models\Device', 'table');
    }

    public function measure_rovers()
    {
        return $this->hasMany('App\Models\MeasureRover');
    }

    public function positions()
    {
        return $this->hasMany('App\Models\Position');
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
                $date1 = $this->positions->last()->file->creation_time;
                $date2 = $this->measure_rovers->last()->file->creation_time;
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
