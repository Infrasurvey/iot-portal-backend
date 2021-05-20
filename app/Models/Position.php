<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;


    protected $appends = [
        'date',
    ];

    protected $hidden = [
        'file',
    ];

    public function file()
    {
        return $this->belongsTo('App\Models\File');
    }

    public function getDateAttribute(){
        return $this->file->creation_time;
    }
}
