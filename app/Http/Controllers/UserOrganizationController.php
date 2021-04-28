<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserOrganization;

class UserOrganizationController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return UserOrganization::all();

    
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
            $userorganization = UserOrganization::create([ 
                'user_id'=>$request->user_id,
                'organization_id'=>$request->organization_id
            ]);
            return response()->json($userorganization, 201);; 
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
     * @param  \App\UserOrganization  $userorganization
     * @return \Illuminate\Http\Response
     */
    public function show(UserOrganization $userorganization)
    {
        return $userorganization;
         
    }
 
 
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserOrganization  $userorganization
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserOrganization $userorganization)
    {
        $userorganization->update($request->only(['user_id','organization_id']));

        return $userorganization;  
         
    }
 
 
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserOrganization  $userorganization
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserOrganization $userorganization)
    {
        $userorganization->delete();
 
        return response()->json(null, 204); 
         
    }



    /**
     * Update all of the userorganization relation for one user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserOrganization  $userorganization
     * @return \Illuminate\Http\Response
     */
    public function updateUserOrganizationRelations(Request $request)
    {
        try {
            foreach ($request->relationstoremove as $relationtoremove) {
                $exist = UserOrganization::where('user_id','=',$relationtoremove['user_id'])->where('organization_id','=',$relationtoremove['organization_id'])->exists();
                if($exist){
                    UserOrganization::where('user_id','=',$relationtoremove['user_id'])->where('organization_id','=',$relationtoremove['organization_id'])->delete();
                }
            }
            foreach ($request->userorganizations as $userorganization) {
                $exist = UserOrganization::where('user_id','=',$userorganization['user_id'])->where('organization_id','=',$userorganization['organization_id'])->exists();
                if(!$exist){
                    $ug = UserOrganization::create([ 
                        'user_id'=>$userorganization['user_id'],
                        'organization_id'=>$userorganization['organization_id']]);
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

    public function addUserOrganizations(Request $request)
    {
        try {
          foreach ($request->userorganizations as $userorganization) {
              $exist = UserOrganization::where('user_id','=',$userorganization['user_id'])->where('organization_id','=',$userorganization['organization_id'])->exists();
            echo $exist;
              if(!$exist){
                $ug = UserOrganization::create([ 
                    'user_id'=>$userorganization['user_id'],
                    'organization_id'=>$userorganization['organization_id']]);
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
