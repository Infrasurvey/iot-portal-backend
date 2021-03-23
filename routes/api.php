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


Route::get('device/all', [DeviceController::class, 'getDevices']);
Route::get('device/{id}', [DeviceController::class, 'getDevice']);

Route::get('device/basestation/all', [DeviceBaseStationController::class, 'getDeviceBaseStations']);
Route::get('device/basestation/{id}', [DeviceBaseStationController::class, 'getDeviceBaseStation']);
Route::get('device/basestation/{id}/configurations', [DeviceBaseStationController::class, 'getConfigurations']);

Route::get('device/rover/all', [DeviceRoverController::class, 'getDeviceRovers']);
Route::get('device/rover/{id}', [DeviceRoverController::class, 'getDeviceRover']);

Route::apiResource('user', 'UserController');
Route::apiResource('organization', 'OrganizationController');
Route::apiResource('group', 'InstallationController');
Route::apiResource('installation', 'GroupController');