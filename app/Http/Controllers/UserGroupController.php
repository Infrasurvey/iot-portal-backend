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
                'group_id'=>$request->group_id
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
        $usergroup->update($request->only(['user_id','group_id']));

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



  /**
     * Update all of the Group relation for one user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserGroup  $usergroup
     * @return \Illuminate\Http\Response
     */
    public function updateUserGroupRelations(Request $request)
    {
        try {
            foreach ($request->relationstoremove as $relationtoremove) {
                if(UserGroup::where('user_id','=',$relationtoremove['user_id'])->where('group_id','=',$relationtoremove['group_id'])->exists()){
                    UserGroup::where('user_id','=',$relationtoremove['user_id'])->where('group_id','=',$relationtoremove['group_id'])->delete();
                }
            }
            foreach ($request->usergroups as $usergroup) {
                $exist = UserGroup::where('user_id','=',$usergroup['user_id'])->where('group_id','=',$usergroup['group_id'])->exists();
                if(!$exist){
                    $ug = UserGroup::create([ 
                        'user_id'=>$usergroup['user_id'],
                        'group_id'=>$usergroup['group_id']]);
                }
            }
            return response()->json($request, 201);; 
        } catch (Exception $e) {
            // Return Error Response
            return response()->json([
                'message' => $e
            ], 500);
        }            
    }

    public function addUserGroups(Request $request)
    {
        try {
          foreach ($request->usergroups as $usergroup) {
              $exist = UserGroup::where('user_id','=',$usergroup['user_id'])->where('group_id','=',$usergroup['group_id'])->exists();
              if(!$exist){
                $ug = UserGroup::create([ 
                    'user_id'=>$usergroup['user_id'],
                    'group_id'=>$usergroup['group_id']]);
                }
              }
                
            return response()->json($request, 201);; 
        } catch (Exception $e) {
            // Return Error Response
            return response()->json([
                'message' => $e
            ], 500);
        }            
    }
}
