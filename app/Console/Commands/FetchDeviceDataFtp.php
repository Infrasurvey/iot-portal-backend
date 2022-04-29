<?php

namespace App\Console\Commands;
use App\Console\Commands\FetchDeviceData;

class FetchDeviceDataFtp extends FetchDeviceData
{
    // FTP connection variable
    var $ftp;

    // Geomon credentials
    var $ftpHost;
    var $ftpUser;
    var $ftpPassword;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geomon:fetch_ftp
                            {geomonId? : Base station ID given in the Geomon system (GM_BASE_<ID>)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Geomon device data from motilis FTP server';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->ftpHost = "motilis.com";
        $this->ftpUser = "geomon";
        $this->ftpPassword = "laurent";
    }

    /**
     * See FetchDeviceData.php
     */
    public function connect()
    {
        // Connect to the Geomon FTP server
        if (($this->ftp = ftp_connect($this->ftpHost)) == FALSE)
        {
            echo("Unable to connect to the FTP server!\n");
            ftp_close($this->ftp);
            return FALSE;
        }

        // Login to the server
        if (ftp_login($this->ftp, $this->ftpUser, $this->ftpPassword) == FALSE)
        {
            echo("Unable to log to the FTP server as $this->ftpUser. Wrong username or password.\n");
            ftp_close($this->ftp);
            return FALSE;
        }

        // Switch to passive mode
        if (ftp_pasv($this->ftp, true) == FALSE)
        {
            echo("Unable to switch the connection to passive mode.\n");
            ftp_close($this->ftp);
            return FALSE;
        }

        ftp_set_option($this->ftp, FTP_USEPASVADDRESS, true);
        return TRUE;
    }

    /**
     * @brief Returns file paths
     * @param dirPath Current directory where to return all file paths.
     * @return paths All file paths.
     */
    protected function listFiles($dirPath)
    {
        return $this->getPaths($dirPath, '-');
    }

    /**
     * @brief Returns directory paths.
     * @param dirPath Current directory where to return all directory paths.
     * @return paths All directory paths.
     */
    protected function listFolders($dirPath)
    {
        return $this->getPaths($dirPath, 'd');
    }

    /**
     * @brief Returns file or directory.
     * @param dirPath Current directory where to return all file/directory paths.
     * @param character File ('-') or Directory ('d') character.
     * @return paths All file/directory paths.
     */
    private function getPaths($dirPath, $character)
    {
        $dirPath = "/data/Geomon/" . $dirPath;
        $paths = ftp_nlist($this->ftp, $dirPath);
        $metas = ftp_rawlist($this->ftp, $dirPath);
        
        // Check for empty folder
        if ($paths == null)
        {
            return null;
        }
        
        // Remove each item not being a file
        sort($paths);
        foreach($metas as $y => $meta)
        {
            if ($meta[0] != $character)
            {
                unset($paths[$y]);
            }
        }

        $paths = array_values($paths);
        
        // Remove "/data/Geomon/" of each path
        foreach($paths as $key => $path)
        {
            $paths[$key] = substr($path, strlen("/data/Geomon/"));
        }

        return $paths;
    }

    /**
     * See FetchDeviceData.php
     */
    public function getFile($filePath)
    {
        $filePath = "/data/Geomon/" . $filePath;
        $file = fopen("temp.txt", "w");
        if (ftp_fget($this->ftp, $file, $filePath, FTP_ASCII) == FALSE)
        {
            echo("File $filePath not found.\n");
            ftp_close($this->ftp);
            return;
        }
    
        // Parse the rxInfo file
        $fileRows = file('temp.txt');
        fclose($file);
        return $fileRows;
    }

    /**
     * See FetchDeviceData.php
     */
    protected function getFileModificationTime($filePath)
    {
        $filePath = "/data/Geomon/" . $filePath;
        return date("Y-m-d h:i:s", ftp_mdtm($this->ftp, $filePath));
    }

    /**
     * See FetchDeviceData.php
     */
    public function disconnect()
    {
        ftp_close($this->ftp);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $geomonId = $this->argument('geomonId');
        if ($geomonId == null)
        {
            $this->fetchAll();
        }
        else
        {
            $this->fetch($geomonId);
        }
    }
}
