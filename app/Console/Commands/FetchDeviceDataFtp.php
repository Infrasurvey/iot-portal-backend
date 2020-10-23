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
    protected $signature = 'geomon:fetch_ftp';

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

        return TRUE;
    }

    /**
     * See FetchDeviceData.php
     */
    protected function listDir($dirPath)
    {
        $paths = ftp_nlist($this->ftp, $dirPath);
        sort($paths);
        return $paths;
    }

    /**
     * See FetchDeviceData.php
     */
    protected function getFile($filePath)
    {
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
        return date("Y-m-d h:i:s", ftp_mdtm($this->ftp, $filePath));
    }

    /**
     * See FetchDeviceData.php
     */
    protected function disconnect()
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
        $this->fetch();
    }
}
