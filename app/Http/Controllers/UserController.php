<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::all();
    }


    /**
     * Display a listing of users with their groups. Without global admin users
     *
     * @return \Illuminate\Http\Response
     */
    public function usersWithGroups()
    {
        return User::where('is_admin',false)->with(['groups','organizations'])->get();
    }

    /**
     * return list of users by organization without global admin users
     */
    public function getUsersByOrganization($id){
        return User::where('is_admin',false)->whereHas('groups.organization',function($query) use ($id){
            $query->where('id',$id);
        })->with(['groups','organizations'])->get();
    }

    /**
     * return list of organization admins by organization without global admin users
     */
    public function getAdminsByOrganization($id){
        return User::where('is_admin',false)->whereHas('organizations',function($query) use ($id){
            $query->where('id',$id);
        })->with(['groups','organizations'])->get();
    }

    /**
     * return list of users by group without global admin users
     */
    public function getUsersByGroup($id){
        return User::where('is_admin',false)->whereHas('groups',function($query) use ($id){
            $query->where('id',$id);
        })->with(['groups','organizations'])->get();
    }

    /**
     * return list of users depending on current auth user without global admin users
     */
    public function getVisibleUsers(){
        $currentUser = Auth::user();
        if($currentUser->is_admin)
            return response()->json(User::where('is_admin',false)->with(['organizations','groups'])->get(), 201);
        return User::where('is_admin',false)->whereHas('groups.organization.users',function($query) use ($currentUser){
            $query->where('id',$currentUser->id);
        })->with(["organizations" => function($q) use ($currentUser){
            $q->whereHas('users',function($query) use ($currentUser){
                $query->where('id',$currentUser->id);
            });
        } ,"groups" => function($q) use ($currentUser){
            $q->whereHas('organization.users',function($query) use ($currentUser){
                $query->where('id',$currentUser->id);
            });
        }])->get();
    }

    /**
     * return list of users that aren't already in groups
     */
    public function getAvailableUsers($groupid){
        return User::where('is_admin',false)->whereDoesntHave('groups', function($query) use ($groupid){
            $query->where('id',$groupid);
        })->get();
    }

    /**
     * return list of users that aren't already organization admin
     */
    public function getAvailableAdmins($organizationid){
        return User::where('is_admin',false)->whereDoesntHave('organizations', function($query) use ($organizationid){
            $query->where('id',$organizationid);
        })->get();
    }

    /**
     * return list of users that aren't already in organization
     */
    public function getAvailableUsersOrga($organizationid){
        return User::where('is_admin',false)->whereDoesntHave('groups.organization', function($query) use ($organizationid){
            $query->where('id',$organizationid);
        })->get();
    }


        /**
     * Return current user
     *
     * @return \Illuminate\Http\Response
     */
    public function currentUser()
    {
        try {
            return response()->json(Auth::user(), 201);; 
        } catch (\Exception $e) {
            // Return Error Response
            return response()->json([
                'message' => $e
            ], 500);
        }
        
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
            $user = User::create([
                'name' => $request->name,
                'lastname' => $request->lastname,
                'address' => $request->address,
                'city' => $request->city,
                'zip' => $request->zip,
                'country' => $request->country,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => $request->password,
                'language'=> $request->language
            ]);
            return response()->json($user, 201);; 
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
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $user;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $user->update($request->only(['name','lastname','phone','email','address','zip','city','country','email_verified_at','password','language']));

        return $user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(null, 204);
    }
}
