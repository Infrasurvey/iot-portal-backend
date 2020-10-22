<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\DeviceRover;

class DeviceController extends Controller
{
    function getDevices()
    {
        return Device::get()->all();
    }

    function getDevice($id)
    {
        return Device::find($id);
    }

    function getDeviceBaseStations()
    {
        
    }

    function getDeviceBaseStation($id)
    {
        
    }

    function getDeviceRovers()
    {

    }

    function getDeviceRover($id)
    {
        return DeviceRover::with('measure_rovers')->find($id);
    }
}
