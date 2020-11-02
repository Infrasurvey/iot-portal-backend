<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;

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
}
