<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\PwdRecoveryEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use Validator;

class RegisterController extends Controller
{
    /**
     * Create a new user and return user's info and token
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $mailvalidator = Validator::make($request->all(), [
            'email' => 'unique:users,email',
        ]);
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'lastname' => 'required',
            'address' => 'required',
            'zip' => 'required',
            'city' => 'required',
            'country' => 'required',
            'phone' => 'required',
            'password' => 'required',
            'cpassword' => 'required|same:password',
            'language' => 'required',
        ]);

        if($mailvalidator->fails()){
            return $this->sendError('Email already exists.', $validator->errors());       
        }
        if($validator->fails()){
            return $this->sendError('Validation Error. Please check your informations', $validator->errors());       
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
     * Log In given user and return user info and token
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

    /**
     * Reset password for a given email
     * send a new password to email
     */
    public function resetPassword(Request $request)
    {
        $mailvalidator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if($mailvalidator->fails()){
            return $this->sendError('Email not valid', $validator->errors());       
        }
        $email = $request->email;
        $users = User::where('email',$email)->get();
        if($users->count()==1){
            $user = $users[0];
            $newpassword = Str::random(12);
            $user->password = Hash::make($newpassword);
            $user->save();

            $data = ['password' => $newpassword];
            Mail::to($email)->send(new PwdRecoveryEmail($data));
            return $this->sendResponse('true', 'Email sent successfully.');
        }
        return $this->sendError('Email not found', 500);
        
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

    /**
     * Log out the current auth user
     */
    public function logout(Request $request){
        Auth::logout();
    }
    
}
