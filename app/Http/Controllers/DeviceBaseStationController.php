<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeviceBaseStation;
use App\Models\ConfigurationBaseStation;

class DeviceBaseStationController extends Controller
{
    function getDeviceBaseStations()
    {
        return DeviceBaseStation::all()->makeHidden(['rovers','last_configuration']);
    }

    function getDeviceBaseStation($id)
    {
        return DeviceBaseStation::find($id);
    }

    function getBaseStationWithRovers($id)
    {
        return DeviceBaseStation::with('rovers')->get()->find($id);
    }

    function getBaseStationWithRoversPositions($id)
    {
        return DeviceBaseStation::with('rovers.positions')->get()->find($id);
    }

    function getBaseStationConfigs($id)
    {
        return DeviceBaseStation::find($id)->configurations;
    }

    function getAvailableBasestations()
    {
        return DeviceBaseStation::doesnthave('installation')->get()->makeHidden(['rovers','last_configuration']);
    }

}
