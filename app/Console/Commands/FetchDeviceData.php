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
     * @brief Connnect to remote folder
     */
    abstract protected function connect();

    /**
     * @brief List all paths in current dirPath
     * @param dirPath 
     */
    abstract protected function listFiles($dirPath);

    /**
     * @brief List all folder paths in current directory
     * @param dirPath 
     */
    abstract protected function listFolders($dirPath);

    /**
     * @brief 
     */
    abstract protected function getFile($filePath);

    /**
     * @brief 
     */
    abstract protected function getFileModificationTime($filePath);

    /**
     * @brief Disconnect from remote folder
     */
    abstract protected function disconnect();

    /**
     * @brief List all base station names present in the Geomon/data folder
     */
    protected function getListOfBaseStationNames()
    {
        // Connect to the remote file system
        if ($this->connect() == FALSE)
        {
            return null;
        }

        // Get all paths of all Geomon Base Stations in root folder.
        $baseStationPaths = $this->listFolders("");

        // Find all the base stations present in the root the folder.
        $baseStations = array();
        for ($i = 0; $i < count($baseStationPaths); $i++)
        {
            // Check if the current tested path
            $baseStationPath = sprintf("GM_BASE_%04d", $i);

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
    
        // Close the connection with the remote file system
        $this->disconnect();
        return $baseStations;
    }

    /**
     * @brief Save/update file in database.
     * @param type File extension type.
     * @param version File version.
     * @param path File path.
     * @return File File model.
     */
    protected function saveFile($type, $version, $path)
    {
        $creationTime = "2000-01-01 01:00:00"; // default
        $name = explode("/", $path);
        $name = end($name);
        switch($type)
        {
            case "ini":
                if (($name[0] >= '0') && ($name[0] <= '9'))
                {
                    $creationTime = "20" . $name[0] . $name[1]  . "-" . $name[2]  . $name[3]  . "-" .
                                           $name[4] . $name[5]  . " " . $name[7]  . $name[8]  . ":" .
                                           $name[9] . $name[10] . ":" . $name[11] . $name[12];
                }
                else
                {
                    $creationTime = $this->getFileModificationTime($path);
                }
                break;
            case "rxInfo":
                $creationTime = "20" . $name[0]  . $name[1]  . "-" .
                                       $name[2]  . $name[3]  . "-" .
                                       $name[4]  . $name[5]  . " " .
                                       $name[7]  . $name[8]  . ":" .
                                       $name[9]  . $name[10] . ":" .
                                       $name[11] . $name[12];
                break;
            case "pos":
                $creationTime = "20" . $name[0] . $name[1] . "-" .
                                       $name[2] . $name[3] . "-" .
                                       $name[4] . $name[5] . " " .
                                       $name[7] . $name[8] . ":00:00";
                break;
        }

        File::updateOrInsert(
        [
            'path' => $path,
            'type' => $type
        ],
        [
            'version' => $version,
            'upload_time' => $this->getFileModificationTime($path),
            'creation_time' => $creationTime
        ]);

        return File::where('path', $path)->first();
    }

    /**
     * @brief Fetch all configuration files of a single base station
     *   ___           __ _                    _   _                 __      _      _ 
     *  / __|___ _ _  / _(_)__ _ _  _ _ _ __ _| |_(_)___ _ _  ___  __\ \    (_)_ _ (_)
     * | (__/ _ \ ' \|  _| / _` | || | '_/ _` |  _| / _ \ ' \(_-< |___> >  _| | ' \| |
     *  \___\___/_||_|_| |_\__, |\_,_|_| \__,_|\__|_\___/_||_/__/    /_/  (_)_|_||_|_|
     *                     |___/                                                      
     */
    protected function fetchConfigurations($deviceBaseStation)
    {
        // Get all paths concerning this base stations
        $paths = $this->listFiles($deviceBaseStation->name);

        // Keep all .ini only
        $iniPaths = array();
        foreach($paths as $path)
        {
            if (strstr($path, ".ini"))
            {
                $iniPaths[] = $path;
            }
        }

        // Ignore the configuration files that are already fetched
        if (($latestConfiguration = ConfigurationBaseStation::where('device_base_station_id', $deviceBaseStation->id)->latest('file_id')->first()) != null)
        {
            if (($lastfile = File::find($latestConfiguration->file_id)) != null)
            {
                foreach($iniPaths as $y => $iniPath)
                {
                    if ($iniPath <= $lastfile->path)
                    {
                        unset($iniPaths[$y]);
                    }
                }

                $iniPaths = array_values($iniPaths);
            }
        }

        // Save the remaining configuration paths that were still not fetched
        foreach($iniPaths as $iniPath)
        {
            // Processing file
            echo("Processing configuration : $iniPath\n");

            // Update/save the configuration file
            $file = $this->saveFile("ini", 1, $iniPath);

            // Download the configuration file
            $myFileRows = $this->getFile($iniPath);

            // Parse the configuration file
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

            $configurationBaseStation = new ConfigurationBaseStation;
            $configurationBaseStation->device_base_station_id = $deviceBaseStation->id;
            $configurationBaseStation->file_id = $file->id;
            $configurationBaseStation->validity = 'valid';

            if (count($configurationFields) == 0)
            {
                $configurationBaseStation->validity = 'corrupted';
            }
            
            foreach($configurationFields as $key => $value)
            {
                if (!is_numeric($value))
                {
                    $configurationBaseStation->validity = 'corrupted';
                    $configurationBaseStation[$this->cMyConfigurationKeys[$key]] = 0;
                }
                else
                {
                    $configurationBaseStation[$this->cMyConfigurationKeys[$key]] = $value;
                }
            }

            $configurationBaseStation->save();
        }
    }

    /**
     * @brief Fetch all rxinfo files of a single base station
     *  ___                 ___ _        _   _                 __            ___       __     
     * | _ ) __ _ ___ ___  / __| |_ __ _| |_(_)___ _ _  ___  __\ \   _ ___ _|_ _|_ _  / _|___ 
     * | _ \/ _` (_-</ -_) \__ \  _/ _` |  _| / _ \ ' \(_-< |___> > | '_\ \ /| || ' \|  _/ _ \
     * |___/\__,_/__/\___| |___/\__\__,_|\__|_\___/_||_/__/    /_/  |_| /_\_\___|_||_|_| \___/                                                                                       
     */
    protected function fetchRxInfos($deviceBaseStation)
    {
        // Get all paths concerning this base stations
        $paths = $this->listFolders($deviceBaseStation->name);

        // Keep only the folders (remvove other suspicious folder or files)
        $template = "GM_BASE_XXXX/YYMMDD_HH";
        foreach ($paths as $y => $path)
        {
            if (strlen($path) != strlen($template))
            {
                unset($paths[$y]);
            }
        }

        // Protection against empty paths
        if (count($paths) == 0)
        {
            echo("No measurement path found in $deviceBaseStation->name\n");
            return;
        }

        // Ignore the rxInfo files that are already fetched
        if (($lastMeasureDevice = MeasureDevice::where('device_id', Device::where([['table_id', $deviceBaseStation->id], ['table_type', 'device_base_stations']])->first()->id)->latest('file_id')->first()) != null)
        {
            if (($lastfile = File::find($lastMeasureDevice->file_id)) != null)
            {
                foreach($paths as $y => $path)
                {
                    if ($path <= $lastfile->path)
                    {
                        unset($paths[$y]);
                    }
                }
            }
        }

        $paths = array_values($paths);

        // For all not processed paths
        foreach($paths as $path)
        {
            $measureFiles = $this->listFiles($path);

            // Find the rxInfo file
            $rxInfoPath = null;
            foreach ($measureFiles as $measureFile)
            {
                if (strstr($measureFile, "rxInfo"))
                {
                    $rxInfoPath = $measureFile;
                }
            }

            if ($rxInfoPath == null)
            {
                echo("No rxInfo file found in $path -> Operation aborted\n");
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

                // Check that something matched the regular expression at this line in the rxInfo file.
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
                echo("Empty rxInfo file $rxInfoPath -> Operation aborted\n");
                continue;
            }

            // Fetch rxinfo file
            echo("Processing rxInfo : $rxInfoPath\n");

            // Update/save the rxInfo file
            $file = $this->saveFile("rxInfo", $rxInfo[0][1], $rxInfoPath);

            // Process the rxInfo file
            $state = $this->cStateFillBaseStation;
            $device = Device::where([['table_id', $deviceBaseStation->id], ['table_type', 'device_base_stations']])->first();
            $measureDevice = new MeasureDevice;
            $measureDevice->device_id = $device->id;
            $measureDevice->file_id = $file->id;
            $deviceRover = null;
            $measureRover = null;
            foreach($rxInfo as $item)
            {
                $key = $item[0];
                $value = $item[1];

                // Change state and create device and device rover on the fly if needed
                if ($key == "GPS_ID")
                {
                    // Save last measurement set
                    if ($state == $this->cStateFillBaseStation)
                    {
                        $deviceBaseStation->save();
                        $measureDevice->save();
                        $state = $this->cStateFillRover;
                    }
                    else if ($state == $this->cStateFillRover)
                    {
                        $deviceRover->save();
                        $measureDevice->save();
                        $measureRover->save();
                    }

                    // Prepare next measurement set
                    $deviceRover = DeviceRover::whereHas('device', function ($query) use ($value) {
                        return $query->where('system_id', $value);
                    })->where('device_base_station_id', $deviceBaseStation->id)->first();
                    if ($deviceRover == null)
                    {
                        $deviceRover = new DeviceRover;
                        $deviceRover->device_base_station_id = $deviceBaseStation->id;
                        $deviceRover->save();

                        $device = new Device;
                        $device->table_id = $deviceRover->id;
                        $device->table_type = 'device_rovers';
                        $device->system_id = $value;
                        $device->save();
                    }
                    else
                    {
                        $device = Device::where([['table_id', $deviceRover->id], ['table_type', 'device_rovers']])->first();
                    }

                    if (($measureRover = MeasureRover::where([['device_rover_id', $deviceRover->id], ['file_id', $file->id]])->first()) == null)
                    {
                        $measureRover = new MeasureRover;
                        $measureRover->device_rover_id = $deviceRover->id;
                        $measureRover->file_id = $file->id;
                    }

                    if (($measureDevice = MeasureDevice::where([['device_id', $device->id], ['file_id', $file->id]])->first()) == null)
                    {
                        $measureDevice = new MeasureDevice;
                        $measureDevice->device_id = $device->id;
                        $measureDevice->file_id = $file->id;
                    }
                }

                // Fill the object fields
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
                else if (array_key_exists($key, $this->cMyMeasureRoverKeys))
                {
                    $measureRover[$this->cMyMeasureRoverKeys[$key]] = $value;
                }
                else if (array_key_exists($key, $this->cMyDeviceRoverKeys))
                {
                    $deviceRover[$this->cMyDeviceRoverKeys[$key]] = $value;
                }
            }
            
            $measureDevice->save();
            if ($state == $this->cStateFillRover)
            {
                $deviceRover->save();
                $measureRover->save();
            }
        }
    }

    /**
     * Fetch all pos files of a single base station
     *  ___                   ___        _ _   _                 __                   
     * | _ \_____ _____ _ _  | _ \___ __(_) |_(_)___ _ _  ___  __\ \     _ __  ___ ___
     * |   / _ \ V / -_) '_| |  _/ _ (_-< |  _| / _ \ ' \(_-< |___> >  _| '_ \/ _ (_-<
     * |_|_\___/\_/\___|_|   |_| \___/__/_|\__|_\___/_||_/__/    /_/  (_) .__/\___/__/
     *                                                                  |_|           
     */
    protected function fetchPositions($deviceBaseStation)
    {
        // Get all paths concerning this base stations
        $paths = $this->listFolders($deviceBaseStation->name);

        // Keep only the folders (remvove other suspicious folder or files)
        $template = "GM_BASE_XXXX/YYMMDD_HH";
        foreach ($paths as $y => $path)
        {
            if (strlen($path) != strlen($template))
            {
                unset($paths[$y]);
            }
        }

        // Protection against empty paths
        if (count($paths) == 0)
        {
            echo("No measurement path found in $deviceBaseStation->name\n");
            return;
        }

        if (($deviceRovers = DeviceRover::select('id')->where('device_base_station_id', $deviceBaseStation->id)->get()) != null)
        {
            $deviceRoverIds = array();
            foreach($deviceRovers as $deviceRover)
            {
                $deviceRoverIds[] = $deviceRover->id;
            }

            if (($lastPosition = Position::whereIn('device_rover_id', $deviceRoverIds)->orderBy('file_id', 'desc')->first()) != null)
            {
                if (($lastfile = File::find($lastPosition->file_id)) != null)
                {
                    foreach($paths as $y => $path)
                    {
                        if ($path <= $lastfile->path)
                        {
                            unset($paths[$y]);
                        }
                    }
                }
            }
        }

        $paths = array_values($paths);

        // For all not processed paths
        foreach($paths as $path)
        {
            $measureFiles = $this->listFiles($path);

            // Find the rxInfo file
            $posPaths = array();
            foreach ($measureFiles as $measureFile)
            {
                if (strstr($measureFile, ".pos") != FALSE)
                {
                    $posPaths[] = $measureFile;
                }
            }

            // Process the .pos files if .pos files are existing in the current measure folder path
            if (count($posPaths) == 0)
            {
                echo("No .pos file found in $path -> Operation aborted\n");
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

                echo("Processing position : $posPath\n");

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
                
                // Update/save the position file
                $file = $this->saveFile("pos", 1, $posPath);

                // Check position existency in the database or save it
                $deviceRoverSystemId = substr($posPath, 0, -4);
                $deviceRoverSystemId = intval(substr($deviceRoverSystemId, 52));
                
                $deviceRover = DeviceRover::whereHas('device', function ($query) use ($deviceRoverSystemId) {
                    return $query->where('system_id', $deviceRoverSystemId);
                })->where('device_base_station_id', $deviceBaseStation->id)->first();

                if ($deviceRover == null)
                {
                    echo("Position file corresponding to a non-existing rover ($deviceRoverSystemId)\n");
                    continue;
                }
                
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
        }
    }

    /**
     * Fetch one base station
     */
    protected function fetch($geomonId)
    {
        // Connect to the remote file system
        if ($this->connect() == FALSE)
        {
            return;
        }

        // Variables
        $deviceBaseStation = null;
        $device = Device::where([['system_id', $geomonId], ['table_type', 'device_base_stations']])->first();
        
        // Check base station existency in the database
        if ($device == null)
        {
            // Check base station existency in the FTP server
            $name = sprintf('GM_BASE_%04d', $geomonId);
            if ($this->listFolders($name) != null)
            {
                $deviceBaseStation = new DeviceBaseStation;
                $deviceBaseStation->name = $name;
                $deviceBaseStation->save();
    
                $device = new Device;
                $device->table_type = 'device_base_stations';
                $device->table_id = $deviceBaseStation->id;
                $device->system_id = $geomonId;
                $device->save();
            }
            else
            {
                echo("Base station not existing\n");
                return;
            }
        }
        else
        {
            $deviceBaseStation = DeviceBaseStation::where('id', $device->table_id)->first();
        }

        // CONFIGURATIONS -> .ini
        $this->fetchConfigurations($deviceBaseStation);

        // RXINFO -> .rxInfo
        $this->fetchRxInfos($deviceBaseStation);

        // POSITIONS -> .pos
        $this->fetchPositions($deviceBaseStation);

        // Close the connection with the remote file system
        $this->disconnect();
    }

    /**
     * Fetch all base stations
     */
    protected function fetchAll()
    {
        $baseStationNames = $this->getListOfBaseStationNames();
        
        foreach ($baseStationNames as $baseStationName)
        {
            $geomonId = intval(substr($baseStationName, -4));
            $this->fetch($geomonId);
        }
    }
}