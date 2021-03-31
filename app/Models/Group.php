<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Installation;
use App\Models\Organization;

class Group extends Model
{
    use HasFactory;

    public function users(){
        return $this->belongsToMany(User::class, 'user_groups');
    }

    public function installations(){
        return $this->hasMany(Installation::class);
    }

    public function organization(){
        return $this->belongsTo(Organization::class);
    }
}
