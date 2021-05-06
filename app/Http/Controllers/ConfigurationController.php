<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Utils\FtpHandler;
use App\Models\Installation;

class ConfigurationController extends Controller
{
    function getPendingConfiguration($id)
    {
        $basestation = Installation::find($id)->basestation;
        $ftpHandler = new FtpHandler;
        if ( $ftpHandler->connect()) {
            //$file = $ftpHandler->getFile($basestation->name.'/170721_091516_GM_BASE_STATION.ini');
            $file = $ftpHandler->getFile('test/GM_BASE_STATION.ini');
            $ftpHandler->disconnect();
            if($file != null)
                return response()->json($file, 201);
            else
                return response()->json('No pending file found', 201);
        }
        return response()->json('An error happened during ftp connection', 500);
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
                return response()->json('Error while deleting file', 500);
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
                return response()->json('Error while applying configuration file', 500);
        }
        return response()->json('An error happened while applying configuration', 500);
    }
}