<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;
    
    protected $appends = [
        'battery_voltage'
    ];

    protected $hidden = [
        'lastmeasuredevice',
    ];

    public function table()
    {
        return $this->morphTo();
    }

    public function measure_devices()
    {
        return $this->hasMany('App\Models\MeasureDevice');
    }

    public function lastmeasuredevice()
    {
        return $this->hasOne('App\Models\MeasureDevice')->latestOfMany()->with('file');
    }

    public function getBatteryVoltageAttribute()
    {
        $measure =$this->lastmeasuredevice;
        if($measure != null){
            return $measure->battery_voltage;
        }
        return null;
    }
}
