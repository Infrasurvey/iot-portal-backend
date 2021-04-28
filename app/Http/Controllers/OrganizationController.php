<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Organization::all();
    }

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function organizationsWithGroups()
    {
        return Organization::with('groups')->get();
    }

    public function getCurrentVisibleOrganizations(){
        $currentUser = Auth::user();
        return Organization::whereHas('users',function($query) use ($currentUser){
            $query->where('id',$currentUser->id);
        })->with('groups')->get();
    }

    public function getGroupsByOrganization($id){
        return Organization::find($id)->groups;
    }

         /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function organizationWithGroups($id)
    {
        return Organization::with('groups')->get()->find($id);   
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
            $organization = Organization::create([
                'name' => $request->name
            ]);
            return response()->json($organization, 201);; 
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
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function show(Organization $organization)
    {
        return $organization;

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Organization $organization)
    {
        $organization->update($request->only(['name']));

        return $organization;    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function destroy(Organization $organization)
    {
        $organization->delete();

        return response()->json(null, 204);   
     }
}
