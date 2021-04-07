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
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
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
                $user = Auth::user(); 
                $success['token'] =  $user->createToken('APIToken')->plainTextToken; 
                $success['name'] =  $user->name;
                $success['email'] =  $user->email;
                $success['groups'] =  $user->groups;
       
                return $this->sendResponse($success, 'User login successfully.');
            } 
            else{ 
                return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
            } 
        } catch (\Throwable $th) {
            return $this->sendError('Unauthorised.', ['error'=>$th]);
        }
        
    }

    public function logout(Request $request){
        Auth::logout();
    }
    
}
