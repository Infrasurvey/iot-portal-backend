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
        })->get()->where('system_id',$system_id)->first()->makeVisible(['measure_rovers','positions','device']);
    }
}
