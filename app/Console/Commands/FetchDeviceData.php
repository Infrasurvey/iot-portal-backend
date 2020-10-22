<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\File;
use App\Models\Device;
use App\Models\DeviceBaseStation;
use App\Models\DeviceRover;
use App\Models\MeasureDevice;
use App\Models\MeasureRover;
use App\Models\Position;

class FetchDeviceData extends Command
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

    var $cStateFillRover         = 0;
    var $cStateFillBaseStation   = 1;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geomon:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Do not use!';

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
        // Geomon credentials
        $ftpHost = "motilis.com";
        $ftpUser = "geomon";
        $ftpPassword = "laurent";

        // Connect to the Geomon FTP server
        if (($ftp = ftp_connect($ftpHost)) == FALSE)
        {
            echo("Unable to connect to the FTP server!\n");
            ftp_close($ftp);
            return;
        }

        // Login to the server
        if (ftp_login($ftp, $ftpUser, $ftpPassword) == FALSE)
        {
            echo("Unable to log to the FTP server as $ftpUser. Wrong username or password.\n");
            ftp_close($ftp);
            return;
        }

        // Switch to passive mode
        ftp_pasv($ftp, true) or die("Cannot switch to passive mode");

        // Get all path of all Geomon Base Stations in /data/Geomon/
        $baseStationPaths = ftp_nlist($ftp , "/data/Geomon/");
        sort($baseStationPaths);

        // Find all the base stations present in the FTP server
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
            echo("Processing Base Station $baseStation\n");

            // Get the base station id
            $baseStation_id = intval(substr($baseStation, -4));

            // Get all the measure paths concerning this base station
            $paths = ftp_nlist($ftp , $baseStation);
            sort($paths);

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

            // Get the last processed measure date for this base stations
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

            // For all not processed paths
            foreach($measurePaths as $measurePath)
            {
                $measureFiles = ftp_nlist($ftp, $measurePath);

                // Find the rxInfo file
                $rxInfoPath = null;
                foreach ($measureFiles as $measureFile)
                {
                    if (strstr($measureFile, "rxInfo") != FALSE)
                    {
                        $rxInfoPath = $measureFile;
                    }
                }

                if ($rxInfoPath == null)
                {
                    echo("No rxInfo file found for $measurePath.\n");
                    continue;
                }

                // Download the rxInfo file
                $file = fopen("temp.txt", "w");
                if (ftp_fget($ftp, $file, $rxInfoPath, FTP_ASCII) == FALSE)
                {
                    echo("File $rxInfoPath not found.\n");
                    ftp_close($ftp);
                    return;
                }

                // Parse the rxInfo file
                $myFileRows = file('temp.txt');
                fclose($file);
                $i = 0;
                foreach ($myFileRows as $myFileRow)
                {
                    preg_match_all("#(([\w \.]+\.)|([\w \.]+\b)) +: ([\w\.\-:]+)#", $myFileRow, $matches);

                    // Check that something mathced the regular expression at this line in the 
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

                // Check file existency in the database Save the rxInfo file in the database
                $rxInfoExplodedPath = explode("/", $rxInfoPath);
                $path = $rxInfoExplodedPath[0] . "/" . $rxInfoExplodedPath[1] . "/" . 
                        $rxInfoExplodedPath[2] . "/" . $rxInfoExplodedPath[3] . "/" .
                        $rxInfoExplodedPath[4];

                $file = File::where('path', $path)->first();
                if ($file == null)
                {
                    $file = new File;
                    $file->name = end($rxInfoExplodedPath);
                    $file->type = "rxInfo";
                    $file->version = $rxInfo[0][1];
                    $file->path = $path;
                    
                    $file->creation_time = "20" . $rxInfoExplodedPath[4][0] . $rxInfoExplodedPath[4][1] . "-" .
                                                  $rxInfoExplodedPath[4][2] . $rxInfoExplodedPath[4][3] . "-" .
                                                  $rxInfoExplodedPath[4][4] . $rxInfoExplodedPath[4][5] . " " .
                                                  $rxInfoExplodedPath[4][7] . $rxInfoExplodedPath[4][8] . ":00:00";
    
                    $file->upload_time = date("Y-m-d h:i:s", ftp_mdtm($ftp, $rxInfoPath));
                }
                
                $file->save();

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

                $deviceRover->device_base_station_id = $deviceBaseStation->id;
                $deviceRover->save();

                $device->table_id = $deviceRover->id;
                $device->save();

                $measureRover->device_rover_id = $deviceRover->id;
                $measureRover->file_id = $file->id;
                $measureRover->save();

                echo("Measure path processed: $measurePath\n");
            }

            echo("Base station $baseStation processed\n");
        }
    
        // Close the FTP connection
        ftp_close($ftp);
        return;
    }
}