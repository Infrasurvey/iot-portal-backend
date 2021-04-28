<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\Installation;
use App\Models\Organization;

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
        try {
            $user = Auth::user();
            $groups = $user->groups;
            $installations = collect([]);

            foreach ($groups as $group) {
                if($group->name == "admin")
                    return response()->json(Installation::all(), 201); 
                foreach ($group->installations as $install) {
                    $installations->push($install);
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

    public function getInstallationsByOrganization($id){
        return Installation::whereHas('group.organization',function($query) use ($id){
            $query->where('id',$id);
        })->with('group.organization')->get();
    }

    public function getInstallationsByGroup($id){
        return Installation::whereHas('group',function($query) use ($id){
            $query->where('id',$id);
        })->with('group.organization')->get();
    }

    public function getCompleteInstallations(){
        return Installation::with(['group.organization','basestation'])->get();
    }

    public function getVisibleInstallations(){
        $currentUser = Auth::user();
        return Installation::whereHas('group.organization.users',function($query) use ($currentUser){
            $query->where('id',$currentUser->id);
        })->with(['group.organization','basestation'])->get();
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
 
 
    /**
     * Display the specified resource.
     *
     * @param  \App\Installation  $installation
     * @return \Illuminate\Http\Response
     */
    public function show(Installation $installation)
    {
         return $installation;
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
