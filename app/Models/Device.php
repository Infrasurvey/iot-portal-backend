<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    public function getDeviceRovers()
    {
        $this->hasMany('App\Models\DeviceRovers');
    }

    public function getDeviceBaseStations()
    {
        $this->hasMany('App\Models\DeviceBaseStations');
    }
}
