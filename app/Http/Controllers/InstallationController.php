<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\Installation;
use App\Models\Organization;
use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;



class InstallationController extends Controller
{
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Installation::all();
    }

    public function getInstallationsByUser(){
        //\DB::disableQueryLog();
        //DB::disableQueryLog();
        //DB::connection()->disableQueryLog();
        //DB::enableQueryLog();
        try {
            $user = Auth::user();
            $groups = $user->groups;
            $installations =[];
            if($user->is_admin)
                return response()->json(Installation::with(['basestation.device','basestation.configurations','basestation.rovers'])->get()->append([
                    'device_rover_count',
                    'battery_voltage',
                    'available_memory',
                    'last_configuration',
                    'last_communication',
                    'latitude',
                    'longitude'
                ]), 201); 
            foreach ($groups as $group) {
                foreach ($group->installations as $install) {
                    $installations->push($install->append([
                        'device_rover_count',
                        'battery_voltage',
                        'available_memory',
                        'last_configuration',
                        'last_communication',
                        'latitude',
                        'longitude'
                    ]));
                }
            }
            return response()->json($installations, 201); 
        } catch (\Exception $e) {
            // Return Error Response
            return response()->json([
                'message' => $e
            ], 500);
        } 
        
    }
    public function getUsersByInstallation($id){
        return User::whereHas('groups.installations',function($query) use ($id){
            $query->where('id',$id);
        })->get();
    }

    public function getInstallationsByOrganization($id){
        return Installation::whereHas('group.organization',function($query) use ($id){
            $query->where('id',$id);
        })->with(['group.organization','basestation'])->get()->makeVisible(['basestation']);
    }

    public function getInstallationsByGroup($id){
        return Installation::where('group_id',$id)->with(['group.organization','basestation'])->get()->makeVisible(['basestation']);
    }

    public function getCompleteInstallations(){
        return Installation::with(['group.organization','basestation'])->get()->makeVisible(['basestation']);
    }

    public function getVisibleInstallations(){
        $currentUser = Auth::user();
        if($currentUser->is_admin)
            return response()->json(Installation::with(['group.organization','basestation'])->get()->makeVisible(['basestation']), 201); 

        return Installation::whereHas('group.organization.users',function($query) use ($currentUser){
            $query->where('id',$currentUser->id);
        })->with(['group.organization','basestation'])->get()->makeVisible(['basestation']);
    }
 
    function getBasestationByInstallation($id){
        return Installation::find($id)->basestation->append([
            'device_rover_count',
            'battery_voltage',
            'available_memory',
            'last_configuration',
            'last_communication'
        ]);
    }

    function getBaseStationConfigsByInstallation($id){
        return Installation::find($id)->basestation->configurations;
    }

    function getBaseStationRoversByInstallation($id){
        return Installation::find($id)->basestation->rovers->makeHidden(['default_position','last_communication','battery_voltage']);
    }
 
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $file = $request->file('image');
            $filename = null;
            if(!empty($file)){
                $filename = $file->hashName();
                $path = $file->storeAs(
                    'public/images', $filename
                );
            }
            else{
                $filename = 'default_image.png';
            }

            $validatedData = $request->validate([
                'name' => 'required',
                'group_id' => 'required',
                'device_base_station_id' => 'required',
                'installation_date' => 'required',
              ]);
            
            $installation = Installation::create([
                'group_id' => $request->group_id,
                'device_base_station_id' => $request->device_base_station_id,
                'name' => $request->name,
                'active' =>true,
                'image_path' => $filename,
                'installation_date'=>$request->installation_date,
                'last_human_intervention'=>$request->installation_date
            ]);

            return response()->json($installation, 201); 
        } catch (\Exception $e) {
            // Return Error Response
            return response()->json([
                'message' => $e
            ], 500);
        }   
         
    }


    public function updateInstallationImages(Request $request,$id){
        try {
            $installation = Installation::find($id);
            $file = $request->file('image');
            $filename = null;
            if(!empty($file)){
                $filename = $file->hashName();
                $path = $file->storeAs(
                    'public/images', $filename
                );
                if($installation->image_path != "default_image.png"){
                    Storage::delete('public/images/'.$installation->image_path);
                }
                $installation->image_path = $filename;
                
            }
            $installation->name = $request->name;
            $installation->save();
            return response()->json($installation, 201); 
        } catch (\Exception $e) {
            // Return Error Response
            return response()->json([
                'message' => $e
            ], 500);
        }   
    }
 
 
    /**
     * Display the specified resource.
     *
     * @param  \App\Installation  $installation
     * @return \Illuminate\Http\Response
     */
    public function show(Installation $installation)
    {
         return $installation->append([
            'device_rover_count',
            'battery_voltage',
            'available_memory',
            'last_configuration',
            'last_communication',
        ]);
    }

 
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Installation  $installation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Installation $installation)
    {
        $installation->update($request->only(['group_id','device_base_station_id','name','image_path','installation_date','last_human_,intervention']));

        return $installation;  
         
    }
 
 
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Installation  $installation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Installation $installation)
    {
        if($installation->image_path != "default_image.png"){
            Storage::delete('public/images/'.$installation->image_path);
        }
        $installation->delete();
 
        return response()->json(null, 204); 
         
    }
}
