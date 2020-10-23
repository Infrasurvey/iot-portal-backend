<?php

namespace App\Console\Commands;
use App\Console\Commands\FetchDeviceData;

class FetchDeviceDataLocal extends FetchDeviceData
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geomon:fetch_local';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Geomon device data from local copy';

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
     * See FetchDeviceData.php
     */
    public function connect()
    {
        
    }

    /**
     * See FetchDeviceData.php
     */
    protected function listDir($dirPath)
    {
        return scandir($filePath, SCANDIR_SORT_ASCENDING);
    }

    /**
     * See FetchDeviceData.php
     */
    protected function getFile($filePath)
    {
        return file($filePath);
    }

    /**
     * See FetchDeviceData.php
     */
    protected function getFileModificationTime($filePath)
    {
        return date("Y-m-d h:i:s", filemtime($filePath));
    }

    /**
     * See FetchDeviceData.php
     */
    protected function disconnect()
    {

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
