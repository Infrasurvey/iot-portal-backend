<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserGroup;

class UserGroupController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return UserGroup::all();

    
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
            $usergroup = UserGroup::create([ 
                'user_id'=>$request->user_id,
                'group_id'=>$request->group_id,
                'is_group_admin' => $request->is_admin
            ]);
            return response()->json($usergroup, 201);; 
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
     * @param  \App\UserGroup  $usergroup
     * @return \Illuminate\Http\Response
     */
    public function show(UserGroup $usergroup)
    {
        return $usergroup;
         
    }
 
 
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserGroup  $usergroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserGroup $usergroup)
    {
        $usergroup->update($request->only(['user_id','group_id','is_group_admin']));

        return $usergroup;  
         
    }
 
 
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserGroup  $usergroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserGroup $usergroup)
    {
        $usergroup->delete();
 
        return response()->json(null, 204); 
         
    }
}
