<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeviceRover;

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
}
