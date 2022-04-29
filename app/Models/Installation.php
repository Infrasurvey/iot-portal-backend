<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Group;
use App\Models\DeviceBaseStation;

class Installation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'group_id',
        'device_base_station_id',
        'active',
        'image_path',
        'installation_date',
        'last_human_intervention'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'basestation',
    ];

    public function group(){
        return $this->belongsTo(Group::class);
    }

    public function basestation(){
        return $this->belongsTo('App\Models\DeviceBaseStation', 'device_base_station_id')->with('lastconf');
    }

    public function getDeviceRoverCountAttribute()
    {
        return $this->basestation->getDeviceRoverCountAttribute();
    }

    public function getBatteryVoltageAttribute()
    {
        return $this->basestation->getBatteryVoltageAttribute();
    }

    public function getAvailableMemoryAttribute()
    {
        return $this->basestation->getAvailableMemoryAttribute();
    }

    public function getLastConfigurationAttribute(){
        $last_conf = $this->basestation->last_configuration;
        if($last_conf != null){
            return $last_conf->configuration_date;
        }
        return null;
    }

    public function getLastCommunicationAttribute(){
        return $this->basestation->last_communication;
    }

    public function getLatitudeAttribute(){
        $last_conf = $this->basestation->last_configuration;
        if($last_conf != null){
            return $last_conf->reference_latitude;
        }
        return null;
    }

    public function getLongitudeAttribute(){
        $last_conf = $this->basestation->last_configuration;
        if($last_conf != null){
            return $last_conf->reference_longitude;
        }
        return null;
    }
}
