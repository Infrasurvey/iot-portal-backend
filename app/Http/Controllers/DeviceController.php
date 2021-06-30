<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;

class DeviceController extends Controller
{
    /**
     * return list of all existing devices
     */
    function getDevices()
    {
        return Device::get()->all();
    }

    /**
     * return specific device based on id
     */
    function getDevice($id)
    {
        return Device::find($id);
    }
}
