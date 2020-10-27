<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;

use App\Models\ConfigurationBaseStation;
use App\Models\Device;
use App\Models\DeviceBaseStation;
use App\Models\DeviceRover;
use App\Models\File;
use App\Models\MeasureDevice;
use App\Models\MeasureRover;
use App\Models\Position;

abstract class FetchDeviceData extends Command
{
    var $cMyRxInfoKeys = array(
        "GEOMON rxInfo" =>"version"
    );

    var $cMyDeviceKeys = array(
        "GPS_ID" => "system_id",
        "SD avail." => "available_memory",
        "MCU_FIRMWARE" => "firmware_version",
        "FIRMWARE" => "firmware_version",
    );

    var $cMyDeviceBaseStationKeys = array(
        "BASE NAME" => "name",
        "BBB_MAC" => "bbb_mac_address",
        "BBB_VERSION" => "bbb_version",
        "BBB_MD5" => "bbb_md5",
        "RNX2RTKP_MD5" => "rnx2rtkp_md5",
        "CONVBIN_MD5" => "convbin_md5"
    );

    var $cMyDeviceRoverKeys = array(
        "UNIQUE_ID" => "unique_id",
        "CoordX" => "coordinate_x",
        "CoordY" => "coordinate_y",
        "CoordZ" => "coordinate_z"
    );
    
    var $cMyMeasureDeviceKeys = array(
        "VBat" => "battery_voltage"
    );

    var $cMyMeasureRoverKeys = array(
        "RSSI" => "rssi",
        "AccX" => "raw_acceleration_x",
        "AccY" => "raw_acceleration_y",
        "AccZ" => "raw_acceleration_z",
    );

    var $cMyConfigurationKeys = array(
        "CONTINUOUS_MODE" => "continuous_mode",
        "RESET" => "reset",
        "WAKEUP_PERIOD_IN_MINUTES" => "wakeup_period_in_minutes",
        "SESSION_START_TIME" => "session_start_time",
        "SESSION_PERIOD_IN_WAKEUP_PERIOD" => "session_period_in_wakeup_period",
        "SESSION_DURATION_IN_MINUTES" => "session_duration_in_minutes",
        "NON_CONTINUOUS_STORE_BINR_TO_FTP" => "non_continuous_store_binr_to_ftp",
        "GPS_MODULE" => "reference_gps_module",
        "LATITUDE" => "reference_latitude",
        "LONGITUDE" => "reference_longitude",
        "ALTITUDE" => "reference_altitude"
    );

    var $cStateFillRover         = 0;
    var $cStateFillBaseStation   = 1;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        echo("Nice to do nothing... DO NOT USE!\n");
    }
    
    /**
     * A PHP function that will calculate the median value
     * of an array
     * 
     * @param array $arr The array that you want to get the median value of.
     * @return boolean|float|int
     * @throws Exception If it's not an array
     */
    private function getMedian($arr)
    {
        //Make sure it's an array.
        if (!is_array($arr))
        {
            throw new Exception('$arr must be an array!');
        }

        //If it's an empty array, return FALSE.
        if (empty($arr))
        {
            return false;
        }

        //Count how many elements are in the array.
        $num = count($arr);

        //Determine the middle value of the array.
        $middleVal = floor(($num - 1) / 2);

        //If the size of the array is an odd number,
        //then the middle value is the median.
        if($num % 2)
        { 
            return $arr[$middleVal];
        } 
        //If the size of the array is an even number, then we
        //have to get the two middle values and get their
        //average
        else 
        {
            //The $middleVal var will be the low
            //end of the middle
            $lowMid = $arr[$middleVal];
            $highMid = $arr[$middleVal + 1];
            //Return the average of the low and high.
            return (($lowMid + $highMid) / 2);
        }
    }

    /**
     * Connect to folder
     */
    abstract protected function connect();

    /**
     * Connect to folder
     */
    abstract protected function listDir($dirPath);

    /**
     * Connect to folder
     */
    abstract protected function getFile($filePath);

    /**
     * Connect to folder
     */
    abstract protected function getFileModificationTime($filePath);

    /**
     * Connect to folder
     */
    abstract protected function disconnect();

    /**
     * Fetch data 
     */
    protected function fetch()
    {
        // Connect to the remote file system
        if ($this->connect() == FALSE)
        {
            return;
        }

        // Get all paths of all Geomon Base Stations in /data/Geomon/
        $baseStationPaths = $this->listDir("/data/Geomon/");
        
        // Find all the base stations present in the actual the folder "/data/Geomon/"
        for ($i = 0; $i < count($baseStationPaths); $i++)
        {
            // Check if the current tested path
            $baseStationPath = sprintf("/data/Geomon/GM_BASE_%04d", $i);

            $j = $i;
            while (($j < count($baseStationPaths)) && (strcmp($baseStationPaths[$j], $baseStationPath) != 0))
            {
                $j++;
            }

            if ($j < count($baseStationPaths))
            {
                $baseStations[] = $baseStationPath;
            }
        }

        // For each base station
        foreach ($baseStations as $baseStation)
        {
            // Get the base station id
            $baseStation_id = intval(substr($baseStation, -4));
            echo("Processing Base Station $baseStation_id\n");

            // Get all the measure paths concerning this base station
            $paths = $this->listDir($baseStation);

            //     ___           __ _                    _   _                 __      _      _ 
            //    / __|___ _ _  / _(_)__ _ _  _ _ _ __ _| |_(_)___ _ _  ___  __\ \    (_)_ _ (_)
            //   | (__/ _ \ ' \|  _| / _` | || | '_/ _` |  _| / _ \ ' \(_-< |___> >  _| | ' \| |
            //    \___\___/_||_|_| |_\__, |\_,_|_| \__,_|\__|_\___/_||_/__/    /_/  (_)_|_||_|_|
            //                       |___/                                                      

            unset($iniPaths);
            $iniPaths = array();
            foreach($paths as $path)
            {
                if (strstr($path, ".ini") != FALSE)
                {
                    $iniPaths[] = $path;
                }
            }

            // Get the latest configuration fetching date
            if (($device = Device::where([['system_id', $baseStation_id],['table_type', 'device_base_stations']])->first()) != null)
            {
                if (($latestConfiguration = ConfigurationBaseStation::where('device_base_station_id', $device->table_id)->latest('file_id')->first()) != null)
                {
                    if (($lastfile = File::find($latestConfiguration->file_id)) != null)
                    {
                        foreach($iniPaths as $y => $iniPath)
                        {
                            $iniExplodedPath = explode("/", $iniPath);
                            $path = $iniExplodedPath[0] . "/" . $iniExplodedPath[1] . "/" . $iniExplodedPath[2] . "/" . $iniExplodedPath[3];
                            if ($path <= $lastfile->path)
                            {
                                unset($iniPaths[$y]);
                            }
                        }
        
                        $iniPaths = array_values($iniPaths);
                    }
                }
            }

            // Save the remaining configuration paths that were still not fetched
            foreach($iniPaths as $iniPath)
            {
                // Check file existency in the database Save the rxInfo file in the database
                $iniExplodedPath = explode("/", $iniPath);
                $path = $iniExplodedPath[0] . "/" . $iniExplodedPath[1] . "/" . $iniExplodedPath[2] . "/" . $iniExplodedPath[3];
                $name = end($iniExplodedPath);

                $file = File::where([['path', $path], ['name', $name], ['type', 'ini']])->first();
                if ($file == null)
                {
                    $file = new File;
                    $file->name = $name;
                    $file->type = "ini";
                    $file->version = 1;
                    $file->path = $path;
                    $file->upload_time = $this->getFileModificationTime($iniPath);
                    if ($name[0] >= '0' || $name[0] <= '9')
                    {
                        $file->creation_time = "20" . $name[0]  . $name[1]  . "-" . $name[2]  . $name[3]  . "-" .
                                                      $name[4]  . $name[5]  . " " . $name[7]  . $name[8]  . ":" .
                                                      $name[9]  . $name[10] . ":" . $name[11] . $name[12];
                    }
                    
                    $file->save();
                }

                // Download the configuration file
                $myFileRows = $this->getFile($iniPath);

                // Parse the configuration file
                unset($configurationFields);
                $configurationFields = array();
                foreach ($myFileRows as $myFileRow)
                {
                    preg_match_all("#([A-Z_]+)=([0-9:.-]+)#", $myFileRow, $matches);

                    // Check that something matched the regular expression at this line in the 
                    if ((count($matches[0]) > 0) && (array_key_exists($matches[1][0], $this->cMyConfigurationKeys)))
                    {
                        // If yes, save the key and its the corresponding value
                        $configurationFields[$matches[1][0]] = $matches[count($matches) - 1][0];
                    }
                }

                if (count($configurationFields) == 0)
                {
                    echo("No data recognized in the configuration file $name -> Operation aborted\n");
                    continue;
                }

                if ($device == null)
                {
                    $deviceBaseStation = new DeviceBaseStation;
                    $deviceBaseStation->save();

                    $device = new Device;
                    $device->system_id = $baseStation_id;
                    $device->table_id = $deviceBaseStation->id;
                    $device->table_type = "device_base_stations";
                    $device->save();
                }

                $configurationBaseStation = new ConfigurationBaseStation;
                $configurationBaseStation->device_base_station_id = $device->table_id;
                $configurationBaseStation->file_id = $file->id;
                foreach($configurationFields as $key => $value)
                {
                    $configurationBaseStation[$this->cMyConfigurationKeys[$key]] = $value;
                }

                //dd([$configurationBaseStation, $configurationFields, $myFileRows]);
                $configurationBaseStation->save();
            }

            //    ___                 ___ _        _   _                 __            ___       __     
            //   | _ ) __ _ ___ ___  / __| |_ __ _| |_(_)___ _ _  ___  __\ \   _ ___ _|_ _|_ _  / _|___ 
            //   | _ \/ _` (_-</ -_) \__ \  _/ _` |  _| / _ \ ' \(_-< |___> > | '_\ \ /| || ' \|  _/ _ \
            //   |___/\__,_/__/\___| |___/\__\__,_|\__|_\___/_||_/__/    /_/  |_| /_\_\___|_||_|_| \___/
            //                                                                                          

            // Keep only the folders (remvove other suspicious folder or files)
            $measurePathTemplate = "/data/Geomon/GM_BASE_XXXX/YYMMDD_HH";
            unset($measurePaths);
            $measurePaths = array();
            foreach ($paths as $path)
            {
                if (strlen($path) == strlen($measurePathTemplate))
                {
                    $measurePaths[] = $path;
                }
            }

            // Get the last processed measure date for this base station
            if (($device = Device::where([['system_id', $baseStation_id],['table_type', 'device_base_stations']])->first()) != null)
            {
                if (($lastMeasureDevice = MeasureDevice::where('device_id', $device->id)->latest('file_id')->first()) != null)
                {
                    if (($lastfile = File::find($lastMeasureDevice->file_id)) != null)
                    {
                        foreach($measurePaths as $y => $measurePath)
                        {
                            if ($measurePath <= $lastfile->path)
                            {
                                unset($measurePaths[$y]);
                            }
                        }
        
                        $measurePaths = array_values($measurePaths);
                    }
                }
            }

            if (count($measurePaths) == 0)
            {
                echo("No new information to synchronize for base station $baseStation_id -> Operation aborted\n");
                continue;
            }

            // For all not processed paths
            foreach($measurePaths as $measurePath)
            {
                echo("Processing measure : $measurePath\n");
                $measureFiles = $this->listDir($measurePath);

                // Find the rxInfo file
                $rxInfoPath = null;
                unset($posPaths);
                $posPaths = array();
                foreach ($measureFiles as $measureFile)
                {
                    if (strstr($measureFile, "rxInfo") != FALSE)
                    {
                        $rxInfoPath = $measureFile;
                    }
                    else if (strstr($measureFile, ".pos") != FALSE)
                    {
                        $posPaths[] = $measureFile;
                    }
                }

                if ($rxInfoPath == null)
                {
                    echo("No rxInfo file found -> Operation aborted.\n");
                    continue;
                }

                // Download the rxInfo file
                $myFileRows = $this->getFile($rxInfoPath);

                // Parse the rxInfo file
                $i = 0;
                unset($rxInfo);
                $rxInfo = array();
                foreach ($myFileRows as $myFileRow)
                {
                    preg_match_all("#(([\w \.]+\.)|([\w \.]+\b)) +: ([\w\.\-:]+)#", $myFileRow, $matches);

                    // Check that something matched the regular expression at this line in the 
                    if(count($matches[0]) > 0)
                    {
                        // If yes, save the key...
                        if (strlen($matches[1][0]) > 0)
                        {
                            $rxInfo[$i][0] = $matches[1][0];
                        }
                        else
                        {
                            $rxInfo[$i][0] = $matches[2][0];
                        }

                        //... and its the corresponding value
                        $rxInfo[$i][1] = $matches[count($matches) - 1][0];
                        $i++;
                    }
                }

                // Check that the rxInfo is not null
                if (count($rxInfo) == 0)
                {
                    echo("rxInfo file corrupted -> Operation aborted\n");
                    continue;
                }

                // Check file existency in the database Save the rxInfo file in the database
                $rxInfoExplodedPath = explode("/", $rxInfoPath);
                $path = $rxInfoExplodedPath[0] . "/" . $rxInfoExplodedPath[1] . "/" . 
                        $rxInfoExplodedPath[2] . "/" . $rxInfoExplodedPath[3] . "/" .
                        $rxInfoExplodedPath[4];
                $name = end($rxInfoExplodedPath);

                $file = File::where([['path', $path], ['name', $name], ['type', 'rxInfo']])->first();
                if ($file == null)
                {
                    $file = new File;
                    $file->name = $name;
                    $file->type = "rxInfo";
                    $file->version = $rxInfo[0][1];
                    $file->path = $path;
                    $file->upload_time = $this->getFileModificationTime($rxInfoPath);
                    $file->creation_time = "20" . $rxInfoExplodedPath[4][0] . $rxInfoExplodedPath[4][1] . "-" .
                                                  $rxInfoExplodedPath[4][2] . $rxInfoExplodedPath[4][3] . "-" .
                                                  $rxInfoExplodedPath[4][4] . $rxInfoExplodedPath[4][5] . " " .
                                                  $rxInfoExplodedPath[4][7] . $rxInfoExplodedPath[4][8] . ":00:00";
                    $file->save();
                }

                // Process the rxInfo and set the intial conditions
                $state = $this->cStateFillBaseStation;
                $device = null;
                $deviceBaseStation = null;
                $deviceRover = null;
                $measureDevice = null;
                $measureRover = null;
                foreach($rxInfo as $item)
                {
                    $key = $item[0];
                    $value = $item[1];

                    // Check if data must be saved
                    if ($key == "GPS_ID")
                    {
                        if ($state == $this->cStateFillBaseStation)
                        {
                            $deviceBaseStation->user_id = NULL;
                            $deviceBaseStation->save();

                            $device->table_id = $deviceBaseStation->id;
                            $device->save();

                            $measureDevice->device_id = $device->id;
                            $measureDevice->file_id = $file->id;
                            $measureDevice->save();

                            $device = null;
                            $state = $this->cStateFillRover;
                        }
                        else if ($state == $this->cStateFillRover)
                        {
                            $deviceRover->device_base_station_id = $deviceBaseStation->id;
                            $deviceRover->save();

                            $device->table_id = $deviceRover->id;
                            $device->save();

                            $measureRover->device_rover_id = $deviceRover->id;
                            $measureRover->file_id = $file->id;
                            $measureRover->save();

                            $device = null;
                        }
                    }

                    // Check existency
                    if ($device == null)
                    {
                        if ($state == $this->cStateFillBaseStation)
                        {
                            $device = Device::where([['system_id', $baseStation_id],['table_type', 'device_base_stations']])->first();
                            if($device == null)
                            {
                                // Device was not existing
                                $device = new Device;
                                $device->system_id = $baseStation_id;
                                $device->table_type = 'device_base_stations';
                                $deviceBaseStation = new DeviceBaseStation;
                            }
                            else
                            {
                                // Device was already existing
                                $deviceBaseStation = DeviceBaseStation::find($device->table_id);
                            }

                            $measureDevice = MeasureDevice::where([['device_id', $device->id], ['file_id', $file->id]])->first();
                            if ($measureDevice == null)
                            {
                                $measureDevice = new MeasureDevice;
                            }
                        }
                        else if ($state == $this->cStateFillRover)
                        {
                            // Check existency
                            if ($device == null)
                            {
                                $deviceRover = DeviceRover::whereHas('device', function ($query) use ($value) 
                                                            {
                                                                return $query->where('system_id', $value);
                                                            })
                                                            ->where('device_base_station_id', $deviceBaseStation->id)
                                                            ->first();

                                if($deviceRover == null)
                                {
                                    // Device/DeviceRover were not existing
                                    $device = new Device;
                                    $device->system_id = $value;
                                    $device->table_type = 'device_rovers';
                                    $deviceRover = new DeviceRover;
                                }
                                else
                                {
                                    $device = Device::where([['table_id', $deviceRover->id],['table_type', 'device_rovers']])->first();
                                }

                                $measureRover = MeasureRover::where([['device_rover_id', $deviceRover->id], ['file_id', $file->id]])->first();
                                if ($measureRover == null)
                                {
                                    $measureRover = new MeasureRover;
                                }
                            }
                        }
                    }

                    // Fill the object fields
                    if($state == $this->cStateFillBaseStation)
                    {
                        if (array_key_exists($key, $this->cMyDeviceKeys))
                        {
                            $device[$this->cMyDeviceKeys[$key]] = $value;
                        }
                        else if (array_key_exists($key, $this->cMyMeasureDeviceKeys))
                        {
                            $measureDevice[$this->cMyMeasureDeviceKeys[$key]] = $value;
                        }
                        else if (array_key_exists($key, $this->cMyDeviceBaseStationKeys))
                        {
                            $deviceBaseStation[$this->cMyDeviceBaseStationKeys[$key]] = $value;
                        }
                    }
                    else if ($state == $this->cStateFillRover)
                    {
                        if (array_key_exists($key, $this->cMyDeviceKeys))
                        {
                            $device[$this->cMyDeviceKeys[$key]] = $value;
                        }
                        else if (array_key_exists($key, $this->cMyMeasureRoverKeys))
                        {
                            $measureRover[$this->cMyMeasureRoverKeys[$key]] = $value;
                        }
                        else if (array_key_exists($key, $this->cMyDeviceRoverKeys))
                        {
                            $deviceRover[$this->cMyDeviceRoverKeys[$key]] = $value;
                        }
                    }
                }

                if ($state == $this->cStateFillBaseStation)
                {
                    $deviceBaseStation->user_id = NULL;
                    $deviceBaseStation->save();

                    $device->table_id = $deviceBaseStation->id;
                    $device->save();

                    $measureDevice->device_id = $device->id;
                    $measureDevice->file_id = $file->id;
                    $measureDevice->save();
                }
                else if ($state == $this->cStateFillRover)
                {
                    $deviceRover->device_base_station_id = $deviceBaseStation->id;
                    $deviceRover->save();

                    $device->table_id = $deviceRover->id;
                    $device->save();

                    $measureRover->device_rover_id = $deviceRover->id;
                    $measureRover->file_id = $file->id;
                    $measureRover->save();
                }

                //    ___                   ___        _ _   _                 __                   
                //   | _ \_____ _____ _ _  | _ \___ __(_) |_(_)___ _ _  ___  __\ \     _ __  ___ ___
                //   |   / _ \ V / -_) '_| |  _/ _ (_-< |  _| / _ \ ' \(_-< |___> >  _| '_ \/ _ (_-<
                //   |_|_\___/\_/\___|_|   |_| \___/__/_|\__|_\___/_||_/__/    /_/  (_) .__/\___/__/
                //                                                                    |_|           

                // Process the .pos files if .pos files are existing in the current measure folder path
                if (count($posPaths) == 0)
                {
                    echo("No .pos file found in $measurePath -> Operation aborted\n");
                    continue;
                }

                foreach($posPaths as $posPath)
                {
                    // Download the .pos file
                    $myFileRows = $this->getFile($posPath);

                    //Remove all header lines (useless)
                    foreach($myFileRows as $y => $myFileRow)
                    {
                        if ($myFileRow[0] == "%")
                        {
                            unset($myFileRows[$y]);
                        }
                    }

                    $myFileRows = array_values($myFileRows);
                    
                    // Check that there are position data in this file
                    if (count($myFileRows) == 0)
                    {
                        echo("No position data in $posPath -> Operation aborted\n");
                        continue;
                    }

                    $i = 0;
                    unset($positions);
                    $positions = array();
                    foreach($myFileRows as $myFileRow)
                    {
                        preg_match_all("([:\/\-\d.]+)", $myFileRow, $matches);
                        $matches = $matches[0];

                        // Check that Q = 1
                        if ($matches[5] == 1)
                        {
                            $positions['latitude'][$i]  = $matches[2];
                            $positions['longitude'][$i] = $matches[3];
                            $positions['height'][$i]    = $matches[4];
                            $positions['Q'][$i]         = $matches[5];
                            $positions['ns'][$i]        = $matches[6];
                            $positions['sdn'][$i]       = $matches[7];
                            $positions['sde'][$i]       = $matches[8];
                            $positions['sdu'][$i]       = $matches[9];
                            $positions['sdne'][$i]      = $matches[10];
                            $positions['sdeu'][$i]      = $matches[11];
                            $positions['sdun'][$i]      = $matches[12];
                            $i++;
                        }
                    }

                    // Check that there are positions with Q = 1, then...
                    if ($i == 0)
                    {
                        echo("No position with Q = 1 in file $posPath -> Operation aborted\n");
                        continue;
                    }

                    // Now it worth to save the file
                    // Check if the .pos file is already existing in the database...
                    $posExplodedPath = explode("/", $posPath);
                    $path = $posExplodedPath[0] . "/" . $posExplodedPath[1] . "/" . 
                            $posExplodedPath[2] . "/" . $posExplodedPath[3] . "/" .
                            $posExplodedPath[4];
                    $name = end($posExplodedPath);

                    $file = File::where([['path', $path], ['name', $name], ['type', 'pos']])->first();
                    if ($file == null)
                    {
                        $file = new File;
                        $file->name = $name;
                        $file->type = "pos";
                        $file->version = "1";
                        $file->path = $path;
                        $file->upload_time = $this->getFileModificationTime($posPath);
                        $file->creation_time = "20" . $posExplodedPath[4][0] . $posExplodedPath[4][1] . "-" .
                                                      $posExplodedPath[4][2] . $posExplodedPath[4][3] . "-" .
                                                      $posExplodedPath[4][4] . $posExplodedPath[4][5] . " " .
                                                      $posExplodedPath[4][7] . $posExplodedPath[4][8] . ":00:00";
                        $file->save();
                    }

                    // Check position existency in the database or save it
                    $deviceRoverSystemId = substr($name, 0, -4);
                    $deviceRoverSystemId = intval(substr($deviceRoverSystemId, 29));
                    
                    $deviceRover = DeviceRover::whereHas('device', function ($query) use ($deviceRoverSystemId) 
                                                {
                                                    return $query->where('system_id', $deviceRoverSystemId);
                                                })
                                                ->where('device_base_station_id', $deviceBaseStation->id)
                                                ->first();

                    

                    $position = Position::where([['device_rover_id', $deviceRover->id], ['file_id', $file->id]])->first();
                    if ($position == null)
                    {
                        $position = new Position;
                        $position->device_rover_id = $deviceRover->id;
                        $position->file_id = $file->id;
                        $position->height = $this->getMedian($positions['height']);
                        $position->latitude = $this->getMedian($positions['latitude']);
                        $position->longitude = $this->getMedian($positions['longitude']);
                        $position->nbr_of_samples = count($myFileRows);
                        $position->nbr_of_samples_where_q_equal_1 = count($positions['Q']);
                        $position->nbr_of_satellites = ceil(array_sum($positions['ns']) / count($positions['ns']));
                        $position->save();
                    }
                }

                echo("Measure path processed: $measurePath\n");
            }

            echo("Base station $baseStation_id processed\n");
        }
    
        // Close the connection with the remote file system
        $this->disconnect();
        return;
    }
}