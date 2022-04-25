<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeasureDevice extends Model
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
        return $this->belongsTo(File::class);
    }

    public function getDateAttribute(){
        return $this->file->creation_time;
    }
}
