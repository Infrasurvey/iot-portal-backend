<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

use Auth;

class Basestation extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];
}