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


    protected $appends = [
        'device_rover_count',
        'battery_voltage',
        'available_memory',
        'last_configuration',
        'last_communication'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //'basestation',
    ];

    public function group(){
        return $this->belongsTo(Group::class);
    }

    public function basestation(){
        return $this->belongsTo(DeviceBaseStation::class,'device_base_station_id');
    }

    public function getDeviceRoverCountAttribute()
    {
        return $this->basestation->getDeviceRoverCountAttribute();
    }

    public function getBatteryVoltageAttribute()
    {
        //return $this->device->measure_devices->last()->battery_voltage;
        return $this->basestation->getBatteryVoltageAttribute();
    }

    public function getAvailableMemoryAttribute()
    {
        return $this->basestation->getAvailableMemoryAttribute();
    }

    public function getLastConfigurationAttribute(){
        if($this->basestation->configurations->last() != null){
            return $this->basestation->configurations->last()->configuration_date;
        }
        return null;
    }

    public function getLastCommunicationAttribute(){
        return $this->basestation->last_communication;
    }

}
