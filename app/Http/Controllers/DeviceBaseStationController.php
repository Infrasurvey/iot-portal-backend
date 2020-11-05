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
        return DeviceBaseStation::whereHas('device', function($query) use ($id)
        {
            $query->where('system_id', $id);
        })->get();
    }

    function getConfigurations($id)
    {
        $baseStation = DeviceBaseStation::whereHas('device', function($query) use ($id)
        {
            $query->where('system_id', $id);
        })->first();

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
