<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class DeviceRover extends Model
{
    use HasFactory;

    public function device()
    {
        return $this->morphOne('App\Models\Device', 'table');
    }

    public function measure_rovers()
    {
        return $this->hasMany('App\Models\MeasureRover');
    }

    public function positions()
    {
        return $this->hasMany('App\Models\Position');
    }
}
