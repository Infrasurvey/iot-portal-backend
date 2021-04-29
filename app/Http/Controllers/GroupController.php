<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Group::with('organization')->get();

    
    }

    public function getCurrentVisibleGroups(){
        $currentUser = Auth::user();
        return Group::whereHas('organization.users',function($query) use ($currentUser){
            $query->where('id',$currentUser->id);
        })->get();
    }

    public function getGroupWithOrganization($id){

        return Group::with('organization')->get()->find($id);
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
            $group = Group::create([ 
                'organization_id'=>$request->organization_id,
                'name' => $request->name
            ]);
            return response()->json($group, 201);; 
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
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group)
    {
        return $group;
         
    }
 
 
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Group $group)
    {
        $group->update($request->only(['organization_id','name']));

        return $group;  
         
    }
 
 
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        $group->delete();
 
        return response()->json(null, 204); 
         
    }
}
