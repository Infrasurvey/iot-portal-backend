<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeviceBaseStation;

class DeviceBaseStationController extends Controller
{
    function getDeviceBaseStations()
    {
        return DeviceBaseStation::get()->append('DeviceRoverCount')->all();
    }

    function getDeviceBaseStation($id)
    {
        return DeviceBaseStation::find($id)->rovers;
    }
}
