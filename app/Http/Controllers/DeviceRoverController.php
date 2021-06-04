<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeviceRover;
use App\Models\Installation;
use App\Models\Position;
use App\Models\MeasureRover;
use App\Models\MeasureDevice;

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
        
        $rover = DeviceRover::whereHas('basestation.installation',function($query) use ($id){
            $query->where('id',$id);
        })->get()->where('system_id',$system_id)->first()->makeHidden(['default_position']);

        $positions = collect();
        Position::where('device_rover_id',$rover->id)->with('file')->chunk(300,function ($poss) use($positions) {
            foreach ($poss as $pos) {
                $positions->push($pos);
            }
        });

        $measurerovers = collect();
        MeasureRover::where('device_rover_id',$rover->id)->with('file')->chunk(300,function ($measures) use($measurerovers) {
            foreach ($measures as $measure) {
                $measurerovers->push($measure);
            }
        });

        $measuredevices = collect();
        MeasureDevice::where('device_id',$rover->device->id)->with('file')->chunk(300,function ($measures) use($measuredevices) {
            foreach ($measures as $measure) {
                $measuredevices->push($measure);
            }
        });

        $rover['r_positions'] = $positions;
        $rover['r_measure_rovers'] = $measurerovers;
        $rover['r_measure_devices'] = $measuredevices;
        return $rover;

        /* return DeviceRover::whereHas('basestation.installation',function($query) use ($id){
            $query->where('id',$id);
        })->with(['positions'=> function($q){
            return $q->chunk(1000,function ($measurerovers) {});
        }])->get()->where('system_id',$system_id)->first()->makeHidden(['default_position'])->makeVisible(['measure_rovers','positions','device']); */
    }//with(['measure_rovers','positions','device.measure_devices'])->
}

