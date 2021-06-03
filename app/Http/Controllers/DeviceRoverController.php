<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeviceRover;
use App\Models\Installation;

class DeviceRoverController extends Controller
{
    function getDeviceRovers()
    {
        return DeviceRover::all();
    }

    function getDeviceRover($id)
    {
        return DeviceRover::find($id);
    }

    function getRoverBySystemId($id,$system_id){
        return DeviceRover::whereHas('basestation.installation',function($query) use ($id){
            $query->where('id',$id);
        })->with(['measure_rovers','positions','device.measure_devices'])->get()->where('system_id',$system_id)->first()->makeHidden(['default_position','last_communication'])->makeVisible(['measure_rovers','positions','device']);
    }//with(['measure_rovers','positions','device.measure_devices'])->
}
