<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DeviceBaseStation;

class ConfigurationBaseStation extends Model
{
    use HasFactory;


    protected $appends = [
        'file_name',
        'configuration_date'
    ];

    protected $hidden = [
        'file',
        'basestation'
    ];

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function basestation()
    {
        return $this->belongsTo(DeviceBaseStation::class);
    }

    public function getFileNameAttribute()
    {
        return explode('/',$this->file->path)[1];
    }

    public function getConfigurationDateAttribute()
    {
        return $this->file->creation_time;
    }
}
