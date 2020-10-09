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

Route::get('device/all', [DeviceController::class, 'getDevices']);
Route::get('device/{id}', [DeviceController::class, 'getDevice']);
Route::get('device/basestation/all', [DeviceController::class, 'getDeviceBaseStations']);
Route::get('device/basestation/{id}', [DeviceController::class, 'getDeviceBaseStation']);
Route::get('device/rover/all', [DeviceController::class, 'getDeviceRovers']);
Route::get('device/rover/{id}', [DeviceController::class, 'getDeviceRover']);

Route::middleware('auth:api')->get('/user', function (Request $request)
{
    return $request->user();
});
