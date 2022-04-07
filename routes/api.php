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
use App\Http\Controllers\UserOrganizationController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ConfigurationController;


Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);
Route::post('logout', [RegisterController::class, 'logout']);
Route::post('resetPassword', [RegisterController::class, 'resetPassword']);


Route::middleware('auth:sanctum')->group( function () {

    // Device 
    Route::get('device/all', [DeviceController::class, 'getDevices']);
    Route::get('device/{id}', [DeviceController::class, 'getDevice']);

    // Device -> Base station
    Route::get('device/basestation/all', [DeviceBaseStationController::class, 'getAll']);
    Route::get('device/basestation/available', [DeviceBaseStationController::class, 'getAvailable']);
    Route::get('device/basestation/{id}', [DeviceBaseStationController::class, 'getSingle']);
    Route::get('device/basestation/{id}/configurations', [DeviceBaseStationController::class, 'getConfigurations']);
    Route::get('device/basestation/{id}/rovers', [DeviceBaseStationController::class, 'getRovers']);
    Route::get('device/basestation/{id}/roversPositions', [DeviceBaseStationController::class, 'getRoversPositions']);
    Route::delete('device/basestation/all', [DeviceBaseStationController::class, 'deleteAll']);
    Route::delete('device/basestation/{id}', [DeviceBaseStationController::class, 'deleteSingle']);

    // Device -> Rover
    Route::get('device/rover/all', [DeviceRoverController::class, 'getDeviceRovers']);
    Route::get('device/rover/{id}', [DeviceRoverController::class, 'getDeviceRover']);
    

    Route::apiResource('user', 'UserController');
    Route::get('usersWithGroups', [UserController::class, 'usersWithGroups']);
    Route::post('updateUserGroups', [UserGroupController::class, 'updateUserGroupRelations']);
    Route::post('updateUserOrganizations', [UserOrganizationController::class, 'updateUserOrganizationRelations']);
    Route::post('addUserGroups', [UserGroupController::class, 'addUserGroups']);
    Route::post('addUserOrganizations', [UserOrganizationController::class, 'addUserOrganizations']);
    Route::get('getCurrentUser', [UserController::class, 'currentUser']);
    Route::get('getUsersByOrganization/{id}', [UserController::class, 'getUsersByOrganization']);
    Route::get('getAdminsByOrganization/{id}', [UserController::class, 'getAdminsByOrganization']);
    Route::get('getUsersByGroup/{id}', [UserController::class, 'getUsersByGroup']);
    Route::get('getVisibleUsers', [UserController::class, 'getVisibleUsers']);
    Route::get('availableUsersForGroup/{groupid}', [UserController::class, 'getAvailableUsers']);
    Route::get('availableUsersForOrganization/{organizationid}', [UserController::class, 'getAvailableUsersOrga']);
    Route::get('availableAdminsForOrganization/{organizationid}', [UserController::class, 'getAvailableAdmins']);

    Route::get('getGroupWithOrganization/{id}', [GroupController::class, 'getGroupWithOrganization']);

    Route::apiResource('organization', 'OrganizationController');
    Route::get('organizationWithGroups', [OrganizationController::class, 'organizationsWithGroups']);
    Route::get('organizationWithGroups/{id}', [OrganizationController::class, 'organizationWithGroups']);
    Route::get('getCurrentVisibleOrganizations', [OrganizationController::class, 'getCurrentVisibleOrganizations']);
    Route::get('getGroupsByOrganization/{id}', [OrganizationController::class, 'getGroupsByOrganization']);

    Route::apiResource('group', 'GroupController');
    Route::get('getCurrentVisibleGroups', [GroupController::class, 'getCurrentVisibleGroups']);

    Route::apiResource('installation', 'InstallationController');
    Route::get('installation/{id}/basestation', [InstallationController::class, 'getBasestationByInstallation']);
    Route::get('installation/{id}/groupid', [InstallationController::class, 'getGroupIdByInstallation']);
    Route::get('installation/{id}/basestation/configurations', [InstallationController::class, 'getBaseStationConfigsByInstallation']);
    Route::get('installation/{id}/basestation/rovers', [InstallationController::class, 'getBaseStationRoversByInstallation']);
    Route::get('installation/{id}/basestation/roversPositions', [DeviceBaseStationController::class, 'getBaseStationWithRoversPositions']);
    Route::get('installation/{id}/basestation/rovers/{system_id}', [DeviceRoverController::class, 'getRoverBySystemId']);

    Route::post('updateInstallationImage/{id}', [InstallationController::class, 'updateInstallationImages']);
    Route::get('getUsersByInstallation/{id}', [InstallationController::class, 'getUsersByInstallation']);

    Route::get('installationByUser', [InstallationController::class, 'getInstallationsByUser']);
    Route::get('getCompleteInstallations', [InstallationController::class, 'getCompleteInstallations']);
    Route::get('getVisibleInstallations', [InstallationController::class, 'getVisibleInstallations']);
    Route::get('getInstallationsByOrganization/{id}', [InstallationController::class, 'getInstallationsByOrganization']);
    Route::get('getInstallationsByGroup/{id}', [InstallationController::class, 'getInstallationsByGroup']);

    Route::apiResource('usergroup', 'UserGroupController');

    Route::post('updatePwd', [RegisterController::class, 'updatePwd']);

    Route::get('installation/{id}/basestation/pendingConfiguration', [ConfigurationController::class, 'getPendingConfiguration']);
    Route::get('installation/{id}/basestation/removePendingConfiguration', [ConfigurationController::class, 'removePendingConfiguration']);
    Route::post('installation/{id}/basestation/applyNewConfiguration', [ConfigurationController::class, 'applyNewConfiguration']);


});
