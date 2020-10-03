<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Basestation;
use App\Models\Rower;

class BasestationController extends Controller
{
    function index(Request $request) {
        
    }

    function getBasestations(){
        return Basestation::get()->all();
    }

    function getBasestation($id){
        return Basestation::with('rower')->find($id);
    }
}
