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
        return $this->measure_devices->last()->battery_voltage;
    }
}
