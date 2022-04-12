<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConfigurationBaseStation;
use App\Models\Device;
use App\Models\DeviceBaseStation;
use App\Models\DeviceRover;
use App\Models\File;
use App\Models\MeasureDevice;
use App\Models\MeasureRover;
use App\Models\Position;

class DeviceBaseStationController extends Controller
{
    /**
     * return list of all base stations.
     */
    function getAll()
    {
        return DeviceBaseStation::all()->makeHidden(['rovers','last_configuration']);
    }

    /**
     * @brief Update all base stations data from the Geomon FTP server.
     */
    function updateAll()
    {
        \Artisan::call('geomon:fetch_ftp');
    }

    /**
     * @brief Delete all base stations and related data
     */
    function deleteAll()
    {
        $deviceBaseStations = DeviceBaseStation::all();
        foreach($deviceBaseStations as $deviceBaseStation)
        {
            $this->deleteSingle($deviceBaseStation->id);
        }
    }

    /**
     * return specific base station based on id field.
     */
    function get($id)
    {
        return DeviceBaseStation::find($id);
    }

    /**
     * return specific base station based on id field.
     */
    function update($id)
    {
        if (($deviceBaseStation = DeviceBaseStation::where('id', $id)->first()) != null)
        {
            \Artisan::call('geomon:fetch_ftp', ['id' => $deviceBaseStation->name]);
        }
        else
        {
            // return 404 not found
        }
    }

    /**
     * @brief Delete a single base station and related data
     */
    function delete($id)
    {
        $fileIds = array();

        // Check that base station record exists
        if (DeviceBaseStation::where('id', $id)->doesntExist())
        {
            return response()->json([
                'message' => "no data",
                'num' => 12
            ], 500);
        }

        // Delete all configurations
        foreach (ConfigurationBaseStation::where('device_base_station_id', $id)->cursor() as $configuration)
        {
            // Delete all configurations files
            ConfigurationBaseStation::where('id', $configuration->id)->delete();
            File::where('id', $configuration->file_id)->delete();
        }

        // Delete all rover measures and positions
        foreach (DeviceRover::where('device_base_station_id', $id)->cursor() as $deviceRover)
        {
            // Positions
            foreach (Position::where('device_rover_id', $deviceRover->id)->cursor() as $position)
            {
                Position::where('id', $position->id)->delete();
                File::where('id', $position->file_id)->delete();
            }

            // Rover measures
            foreach (MeasureRover::where('device_rover_id', $deviceRover->id)->cursor() as $measureRover)
            {
                MeasureRover::where('id', $measureRover->id)->delete();
                array_push($fileIds, File::where('id', $measureRover->file_id)->first()->id);
            }

            // Device measures
            foreach (MeasureDevice::where('device_id', Device::where('table_id', $deviceRover->id)->where('table_type', 'device_rovers')->first()->id)->cursor() as $measureDevice)
            {
                MeasureDevice::where('id', $measureDevice->id)->delete();
                array_push($fileIds, File::where('id', $measureDevice->file_id)->first()->id);
            }

            // Delete rover
            DeviceRover::where('id', $deviceRover->id)->delete();

            // Delete device
            Device::where('table_id', $deviceRover->id)->where('table_type', 'device_rovers')->delete();
        }
            // Delete all device measures and device measure files
        foreach (MeasureDevice::where('device_id', Device::where('table_id', $id)->where('table_type', 'device_base_stations')->first()->id)->cursor() as $measureDevice)
            {
                MeasureDevice::where('id', $measureDevice->id)->delete();
                array_push($fileIds, File::where('id', $measureDevice->file_id)->first()->id);
        }

        // Delete device (linked to base station)
        Device::where('table_id', $id)->where('table_type', 'device_base_stations')->delete();

        // Delete base station
        DeviceBaseStation::where('id', $id)->delete();

        // Delete all files
        File::whereIn('id', array_unique($fileIds))->delete();
    }

    /**
     * return base station's configurations list based on base station id.
     */
    function getConfigurations($id)
    {
        return DeviceBaseStation::find($id)->configurations;
    }

    /**
     * return specific base station based on id field with linked rovers.
     */
    function getRovers($id)
    {
        return DeviceBaseStation::with('rovers')->get()->find($id);
    }

    /**
     * brief Get all rover positions
     */
    function getRoversPositions($id)
    {
        return DeviceBaseStation::whereHas('installation',function($query) use ($id){
            $query->where('id',$id);
        })->with(['rovers'])->get()->makeVisible(['rovers','last_configuration']);
    }

    /**
     * return list of base station that aren't linked with an installation
     */
    function getAvailable()
    {
        return DeviceBaseStation::doesnthave('installation')->get()->makeHidden(['rovers','last_configuration']);
    }
}