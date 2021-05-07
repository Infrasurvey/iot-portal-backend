<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Utils\FtpHandler;
use App\Models\Installation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use DateTime;
use DateTimeZone;


class ConfigurationController extends Controller
{
    function parseFile($file){
        $config = [];
        foreach ($file as $line) {
            $line = preg_replace('/\n/', '', $line);
            if($line != '' && !str_contains($line, '[') ){
                $l =  explode('=',$line);
                $key = strtolower($l[0]);
                $value = $l[1];
                $config[$key] = $value;
            }
        }
        return $config;
    }

    function getPendingConfiguration($id)
    {
        $basestation = Installation::find($id)->basestation;
        $ftpHandler = new FtpHandler;
        $response = [];
        if ( $ftpHandler->connect()) {
            $date = $ftpHandler->getLastModifiedDate('test/GM_BASE_STATION.ini');
            $response['date'] = $date;
            //$file = $ftpHandler->getFile($basestation->name.'/170721_091516_GM_BASE_STATION.ini');
            $file = $ftpHandler->getFile('test/GM_BASE_STATION.ini');
            $ftpHandler->disconnect();
            if($file != null){
                $response['configuration'] = $this->parseFile($file);
                return response()->json($response, 201);
            }
            else
                return response()->json('No pending file found', 204 );
        }
        return response()->json('An error happened during ftp connection', 500 );
    }

    


    function removePendingConfiguration($id){
        $basestation = Installation::find($id)->basestation;
        $ftpHandler = new FtpHandler;
        if ( $ftpHandler->connect()) {
            //$file = $ftpHandler->removeFile($basestation->name.'/170721_091516_GM_BASE_STATION.ini');
            $status = $ftpHandler->removeFile('test/GM_BASE_STATION.ini');
            $ftpHandler->disconnect();
            if($status)
                return response()->json('File succesfully deleted', 201);
            else
                return response()->json('Error while deleting file', 204);
        }
        return response()->json('An error happened during file remove', 500);
    }

    function applyNewConfiguration(Request $request,$id){
        $basestation = Installation::find($id)->basestation;
        $file = $request->file('configuration');
        $ftpHandler = new FtpHandler;
        if ( $ftpHandler->connect()) {
            //$file = $ftpHandler->saveFile($basestation->name.'/GM_BASE_STATION.ini');
            $status = $ftpHandler->saveFile('test/GM_BASE_STATION.ini',$file);
            $ftpHandler->disconnect();
            if($status)
                return response()->json('Configuration file successfully applied', 201);
            else
                return response()->json('Error while applying configuration file', 204);
        }
        return response()->json('An error happened while applying configuration', 500);
    }
}