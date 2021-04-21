<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//use App\Http\Controllers\BasestationController;

/* 
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceBaseStationController;
use App\Http\Controllers\DeviceRoverController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\InstallationController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UserGroupController;
use App\Http\Controllers\RegisterController;


Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);
Route::post('logout', [RegisterController::class, 'logout']);


Route::middleware('auth:sanctum')->group( function () {
    //Not working 
    Route::get('device/all', [DeviceController::class, 'getDevices']);

    Route::get('device/{id}', [DeviceController::class, 'getDevice']);

    Route::get('device/basestation/all', [DeviceBaseStationController::class, 'getDeviceBaseStations']);
    //Work using system_id as PK
    Route::get('device/basestation/{id}', [DeviceBaseStationController::class, 'getDeviceBaseStation']);
    Route::get('device/basestation/{id}/configurations', [DeviceBaseStationController::class, 'getBaseStationConfigs']);
    Route::get('device/basestation/{id}/rovers', [DeviceBaseStationController::class, 'getBaseStationWithRovers']);

    Route::get('device/rover/all', [DeviceRoverController::class, 'getDeviceRovers']);
    Route::get('device/rover/{id}', [DeviceRoverController::class, 'getDeviceRover']);
    
    Route::apiResource('user', 'UserController');
    Route::apiResource('organization', 'OrganizationController');
    Route::apiResource('group', 'GroupController');
    Route::apiResource('installation', 'InstallationController');
    Route::apiResource('usergroup', 'UserGroupController');

    Route::get('installationByUser', [InstallationController::class, 'getInstallationsByUser']);
    
    Route::get('usersWithGroups', [UserController::class, 'usersWithGroups']);
    Route::post('updateUserGroups', [UserGroupController::class, 'updateUserGroupRelations']);
    Route::get('getCurrentUser', [UserController::class, 'currentUser']);
    Route::get('getCompleteInstallations', [InstallationController::class, 'getCompleteInstallations']);
    Route::post('updatePwd', [RegisterController::class, 'updatePwd']);

});
