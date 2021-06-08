<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

class RegisterController extends Controller
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'lastname' => 'required',
            'address' => 'required',
            'zip' => 'required',
            'city' => 'required',
            'country' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'cpassword' => 'required|same:password',
            'language' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('APIToken')->plainTextToken;
        $success['name'] =  $user->name;
   
        return $this->sendResponse($success, 'User register successfully.');
    }

     /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try {
            if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
                $user = Auth::user()->load(['groups.installations' => function($query) {
                    $query->select(['id','group_id','device_base_station_id']);
                },'organizations' => function($query) {
                    $query->select(['id']);
                }]); 
                $success['token'] =  $user->createToken('APIToken')->plainTextToken; 
                $success['name'] =  $user->name;
                $success['lastname'] =  $user->lastname;
                $success['email'] =  $user->email;
                $success['groups'] =  $user->groups;
                $success['organizations'] =  $user->organizations;
                $success['is_admin'] =  $user->is_admin;
       
                return $this->sendResponse($success, 'User login successfully.');
            } 
            else{ 
                return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
            } 
        } catch (\Throwable $th) {
            return $this->sendError('Unauthorised.', ['error'=>$th]);
        }
        
    }

    public function test()
    {
        return $this->sendError('Unauthorised.', ['error'=>'Unauthorised'],401);
            
        
    }

        /**
     * Update pwd
     *
     * @return \Illuminate\Http\Response
     */
    public function updatePwd(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password'=> 'required',
                'new_password' => 'required|min:5',
                'c_password' => 'required|same:new_password',
            ]);
         
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }
          
            $user = Auth::user(); 
         
            if(!Hash::check($request->password, $user->password)){
                return $this->sendError('Error.', 'You have entered a wrong password');         
            }else{
                $user->password = Hash::make($request->new_password);
                $user->save();
               return $this->sendResponse('Success', 'Password successfully updated');

            }
                
       
        } catch (\Throwable $th) {
            return $this->sendError('Error.', ['error'=>$th]);
        }
        
    }

    public function logout(Request $request){
        Auth::logout();
    }
    
}
