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
        //return DeviceBaseStation::with('rovers.positions')->get()->makeVisible(['rovers','last_configuration'])->find($id);
        return DeviceBaseStation::whereHas('installation',function($query) use ($id){
            $query->where('id',$id);
        })->with(['rovers'])->get()->makeVisible(['rovers','last_configuration']);
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
