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

    public function measure_devices()
    {
        return $this->hasMany('App\Models\MeasureDevice');
    }

    public function getBatteryVoltageAttribute()
    {
        $measure =$this->measure_devices->last();
        if($measure != null){
            return $this->measure_devices->last()->battery_voltage;
        }
        return null;
    }
}
