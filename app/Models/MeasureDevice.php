<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeasureDevice extends Model
{
    use HasFactory;

    public function file()
    {
        return $this->hasOne('App\Models\File');
    }
}
