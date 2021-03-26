<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Installation;


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
 
 
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $installation = Installation::create([
                'group_id' => $request->group_id,
                'name' => $request->name,
                'image_path' => $request->image_path,
                'installation_date'=>$request->installation_date,
                'last_human_intervention'=>$request->last_human_intervention
            ]);
            return response()->json($installation, 201);; 
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
        $installation->update($request->only(['group_id','name','image_path','installation_date','last_human_,intervention']));

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
       $installation->delete();
 
        return response()->json(null, 204); 
         
    }
}
