<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    public function configuration_base_station()
    {
        return $this->hasOne('App\Models\ConfigurationBaseStation');
    }

    public function measure_device()
    {
        return $this->hasOne('App\Models\MeasureDevice');
    }

    public function measure_rover()
    {
        return $this->hasOne('App\Models\MeasureRover');
    }

    public function position()
    {
        return $this->hasOne('App\Models\Position');
    }
}
