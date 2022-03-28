<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeviceBaseStation;
use App\Models\ConfigurationBaseStation;

class DeviceBaseStationController extends Controller
{
    /**
     * return list of base stations.
     */
    function getDeviceBaseStations()
    {
        return DeviceBaseStation::all()->makeHidden(['rovers','last_configuration']);
    }

    /**
     * return specific base station based on id field.
     */
    function getDeviceBaseStation($id)
    {
        return DeviceBaseStation::find($id);
    }

    /**
     * return specific base station based on id field with linked rovers.
     */
    function getBaseStationWithRovers($id)
    {
        return DeviceBaseStation::with('rovers')->get()->find($id);
    }

    /**
     * return specific base station based on installation id field. Return base station with rovers
     */
    function getBaseStationWithRoversPositions($id)
    {
        return DeviceBaseStation::whereHas('installation',function($query) use ($id){
            $query->where('id',$id);
        })->with(['rovers'])->get()->makeVisible(['rovers','last_configuration']);
    }

    /**
     * return base station's configurations list based on base station id.
     */
    function getBaseStationConfigs($id)
    {
        return DeviceBaseStation::find($id)->configurations;
    }

    /**
     * return list of base station that aren't linked with an installation
     */
    function getAvailableBasestations()
    {
        return DeviceBaseStation::doesnthave('installation')->get()->makeHidden(['rovers','last_configuration']);
    }

}
