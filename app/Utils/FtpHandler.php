<?php

namespace App\Utils;


class FtpHandler
{
    // FTP connection variable
    var $ftp;

    // Geomon credentials
    var $ftpHost;
    var $ftpUser;
    var $ftpPassword;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
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

    function listDir($dirPath)
    {
        $dirPath = "/data/Geomon/" . $dirPath;
        $paths = ftp_nlist($this->ftp, $dirPath);
        sort($paths);

        // Remove "/data/Geomon/" of each path
        foreach($paths as $key => $path)
        {
            $paths[$key] = substr($path, strlen("/data/Geomon/"));
        }

        return $paths;
    }


    function getFile($filePath)
    {
        try {
            $filePath = "/data/Geomon/" . $filePath;
            
            $file = fopen("temp.txt", "w");
            
            if (ftp_fget($this->ftp, $file, $filePath, FTP_ASCII) == FALSE)
            {
                //echo("File $filePath not found.\n");
                ftp_close($this->ftp);
                return null;
            }
            // Parse the rxInfo file
            $fileRows = file('temp.txt');
            fclose($file);
            return $fileRows;
        } catch (\Throwable $th) {
            return null;
        }
        
    }

    function getLastModifiedDate($filePath){
        try {
            $filePath = "/data/Geomon/" . $filePath;
            $lastchanged = ftp_mdtm($this->ftp, $filePath);
            
            if ($lastchanged != -1)
            {
                return date("m.d.Y H:i:s.",$lastchanged);
            }
            else
            {   
                return null;
            }
        } catch (\Throwable $th) {
            return null;
        }
        
    }

    function removeFile($filePath)
    {
        try {
            $filePath = "/data/Geomon/" . $filePath;
            if (ftp_delete($this->ftp, $filePath))
            {
                return true;
            }
            else
            {
                return false;
            }
            
        } catch (\Throwable $th) {
            return false;
        }
        
    }

    function saveFile($filePath,$file)
    {
        try {
            $fp = fopen($file,"r");
            $filePath = "/data/Geomon/" . $filePath;
            if (ftp_fput($this->ftp, $filePath, $fp, FTP_ASCII) == true)
            {
                return true;
            }
            else
            {
                return false;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    function getFileModificationTime($filePath)
    {
        $filePath = "/data/Geomon/" . $filePath;
        return date("Y-m-d h:i:s", ftp_mdtm($this->ftp, $filePath));
    }

    function disconnect()
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
