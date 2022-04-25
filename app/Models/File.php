<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    public function configuration_base_station()
    {
        return $this->hasOne(ConfigurationBaseStation::class);
    }

    public function measure_device()
    {
        return $this->hasOne(MeasureDevice::class);
    }

    public function measure_rover()
    {
        return $this->hasOne(MeasureRover::class);
    }

    public function position()
    {
        return $this->hasOne(Position::class);
    }
}
