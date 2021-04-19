<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeviceBaseStation;

class DeviceBaseStationController extends Controller
{
    function getDeviceBaseStations()
    {
        return DeviceBaseStation::all();
    }

    function getDeviceBaseStation($id)
    {
        return DeviceBaseStation::find($id);
    }

    function getBaseStationWithRovers($id)
    {
        return DeviceBaseStation::with('rovers.positions')->get()->find($id);
    }

    function getConfigurations($id)
    {
        $baseStation = DeviceBaseStation::find($id);

        if ($baseStation == null)
        {
            return null;
        }
        else
        {
            return $baseStation->configurations;
        }
    }
}
