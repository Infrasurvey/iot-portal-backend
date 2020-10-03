<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

use Auth;
use Rower;

class Basestation extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public function rower()
    {
        return $this->hasMany('App\Models\Rower');
    }
}